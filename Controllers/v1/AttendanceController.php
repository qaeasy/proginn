<?php

/*
     * @Author: Larry Yang
     * @Created Date: 2016-05-03
     * @Description: Attendance API
     *
     */
namespace hitachi\Phrest\Controllers\v1;

use Phalcon\Mvc\Controller;
use hitachi\Phrest\Core\Utils;
use hitachi\Phrest\Models\AmUser;
use hitachi\Phrest\Models\AmGroup;
use hitachi\Phrest\Models\AtTimesheet;
use hitachi\Phrest\Models\AmUserAuthority;
use hitachi\Phrest\Transformers\AttendanceTransformer;
use hitachi\Phrest\Transformers\AttendanceManagementTransformer;

/**
 * @RoutePrefix("/v1/attendance")
 * @Api(level=1,
 *   limits={
 *   "key" : {
 *   "increment" : "-1 day", "limit" : 1000}
 *   }
 * )
 */
class AttendanceController extends Controller
{
    public $TIMESHEET_PHASE_NONE = 0;
    public $TIMESHEET_PHASE_PM = 1;
    public $TIMESHEET_PHASE_ADMIN = 2;
    public $TIMESHEET_PHASE_DONE = 3;

    /**
     * @Post("/search")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */
    public function indexAction()
    {
        $user_id = $this->request->getHeader("HTTP_USER_ID");
        if($this->request->getPost("year")){
            $year = $this->request->getPost("year");
        }else{
            $year = date("Y");
        }
        if($this->request->getPost("month")){
            $month = $this->request->getPost("month");
        }else{
            $month = (int) date("m");
        }

        if($this->request->getPost("user_id")){
            $group_id = AmUser::findFirst(
                array(
                    "user_id = '{$this->request->getPost("user_id")}'"
                )
            )->getGroupId();
            $group = AmGroup::find(
                array(
                    "group_id = '{$group_id}' AND leader_id_list LIKE '%,{$this->request->getHeader("HTTP_USER_ID")},%'"
                )
            )->count();
            if($group <= 0){
                return $this->apiResponse->errorUnauthorized();
            }
            $user_id = $this->request->getPost("user_id");
        }

        //  get prev month data
        $w = date("w",strtotime($year."-".$month."-01"));
        $current_timestamp = strtotime($year."-".$month."-01");
        $time = ($w*60*60*24);
        $prev_year = date("Y",$current_timestamp - $time);
        $prev_month = intval(date("m",$current_timestamp - $time));
        $prev_day = intval(date("d",$current_timestamp - $time));
        //  get next month data
        $date1 = $year."-".$month."-01";
        if($month==12){
            $date2 = ($year+1)."-01-01";
        }else{
            $date2 = $year."-".($month+1)."-01";
        }
        $Utils = new Utils();
        $days = $Utils->getDays($date1, $date2);
        $w = 6 -(date("w",strtotime($year."-".$month."-".$days)));
        $current_timestamp = strtotime($year."-".$month."-".$days);
        $time = ($w*60*60*24);
        $next_year = date("Y",$current_timestamp + $time);
        $next_month = intval(date("m",$current_timestamp + $time));
        $next_day = intval(date("d",$current_timestamp + $time));

        $attendance = AtTimesheet::find("user_id = '{$user_id}'
                                         AND (
                                            (ts_year='{$year}' AND ts_month='{$month}')
                                            OR
                                            (ts_year='{$prev_year}' AND ts_month='{$prev_month}' AND ts_day >= '{$prev_day}')
                                            OR
                                            (ts_year='{$next_year}' AND ts_month='{$next_month}' AND ts_day <= '{$next_day}')
                                         )
                                        ");

        if(!$attendance){
            return $this->apiResponse->errorUnauthorized();
        }else{
            return $this->apiResponse->withItem($attendance, new AttendanceTransformer());    
        }
    }

    /**
     * @Post("/comment")
     * @Limit({"increment": "-1 hour", "limit": 50});
     */
    public function addAction()
    {        
        $ts_id = $this->request->getPost("ts_id");
        $comment = $this->request->getPost("comment");
        return $this->commentAndApprove($ts_id, $comment);
    }

    /**
     * @Post("/approve")
     * @Limit({"increment": "-1 hour", "limit": 50});
     */
    public function approveAction()
    {        
        $role = $this->request->getPost("role");
        $ts_id = $this->request->getPost("ts_id");
        $comment = $this->request->getPost("comment");
        $seconds = $this->request->getPost("seconds");
        $user_id = $this->request->getHeader("HTTP_USER_ID");
        $approve = $this->request->getPost("approve");

        // get user's authorities
        $auth_ids = AmUserAuthority::find(
            array(
                "columns" => 'authority_id',
                "user_id = '{$user_id}'"
            )
        );
        foreach ($auth_ids as $row){
            $ids[] = $row->authority_id;
        }

        $timeSheets = AtTimesheet::find("ts_id IN ({$ts_id})");

        if(in_array('1', $ids) && $role == 1){
            // PM approved

            $groups =  AmGroup::find(
                array(
                    "leader_id_list LIKE '%,{$user_id},%'"
                )
            );
            foreach ($groups as $row){
                $group_ids[] = $row->group_id;
            }
            foreach ($timeSheets as $row)
            {
                $user_ids[] = $row->getUserId();
                // check if the currect user is supvisor
                if(!in_array($row->User->group_id,$group_ids)){
                    return $this->apiResponse->errorUnauthorized();
                }
            }
            return $this->approveByPmAction($ts_id, $comment, $user_id, $approve);
        }elseif(in_array('3', $ids) && $role == 2){
            return $this->approveByAdminAction($ts_id, $comment, $user_id, $approve, $seconds);            
        }else{
            return $this->apiResponse->errorUnauthorized();
        }
    }

    /**
     * @Post("/get/todolist")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */

    public function gettdlAction()
    {
        $user_id = $this->request->getHeader("HTTP_USER_ID");
        $group_id = $this->request->getPost("group_id");

        if($group_id){
            $groups = AmGroup::find(
                array(
                    "group_id = '{$group_id}' AND leader_id_list LIKE '%,{$user_id},%'"
                )
            );
            if($groups->count() <= 0){
                return $this->apiResponse->errorUnauthorized();
            }
            $ts_timesheets = $this->getTimesheetByGroup($group_id, $all = false);
        }else{
            $groups = AmGroup::find(
                array(
                    "leader_id_list LIKE '%,{$user_id},%'"
                )
            );
            if($groups->count() <= 0){
                return $this->apiResponse->errorUnauthorized();
            }
            foreach ($groups as $row){
                $group_ids[] = $row->group_id;
            }
            $group_ids = implode(',',$group_ids);
            $ts_timesheets = $this->getTimesheetByGroup($group_ids, $all = true);
        }

        if ($ts_timesheets) {
            return $this->apiResponse->withItem($ts_timesheets, new AttendanceManagementTransformer());
        } else {
            return $this->apiResponse->errorInternalError();
        }

    }

    private function getTimesheetByGroup($group, $all = false)
    {
        if($all == true)
        {
            $users = AmUser::find(
                array(
                    "group_id IN ({$group})"
                )
            );
        }else{
            $users = AmUser::find(
                array(
                    "group_id = '{$group}'"
                )
            );
        }
        if($users->count() <= 0){
            $user_ids = "null";
        }else {
            foreach ($users as $row) {
                $user_ids[] = $row->user_id;
            }
            $user_ids = implode(',', $user_ids);
        }
        $ts_timesheets = AtTimesheet::find(
            array(
                "user_id IN ({$user_ids}) AND ts_flowid = '1'"
            )
        );
        $ts_timesheets->count = $ts_timesheets->count();
        return $ts_timesheets;
    }


    //  approve or reject by pm
    public function approveByPmAction($ts_id, $comment, $user_id, $approve)
    {
        if($approve){
            return $this->commentAndApprove($ts_id, $comment, "PM", $user_id);
        }else{
            return $this->commentAndReject($ts_id, $comment, "PM", $user_id);
        }
    }

    //  approve or reject by admin
    public function approveByAdminAction($ts_id, $comment, $user_id, $approve, $seconds)
    {        
        if($approve){
            return $this->commentAndApprove($ts_id, $comment, "ADMIN", $user_id, $seconds);
        }else{
            return $this->commentAndReject($ts_id, $comment, "ADMIN", $user_id, $seconds);
        }
    }

    //  comment and approve
    private function commentAndApprove($ts_id, $comment, $type='', $user_id=0, $admin_add_second=0){
        $api = AtTimesheet::find("ts_id IN ({$ts_id})");
        $flowid = 0;
        switch ($type) {
            case 'PM':                
                $data = array(
                        'ts_pm_comment' => $comment,
                        'ts_pm_user' => $user_id,
                        'ts_flowid' => $this->TIMESHEET_PHASE_ADMIN
                    );
                break;
            case 'ADMIN':
                // if multiple approve ts_total_seconds cannot be calculated
                return $this->apiResponse->errorInternalError();
                // $data = array(
                //         'ts_admin_comment' => $comment,
                //         'ts_admin_user' => $user_id,
                //         'ts_total_seconds' => $api->getTsSeconds() + $admin_add_second + $api->getTsReceiptSeconds(),
                //         'ts_flowid' => $this->TIMESHEET_PHASE_DONE
                //     );
                break;
            default:
                $api = AtTimesheet::findFirst("ts_id = {$ts_id}");
                $data = array(
                        'ts_self_comment' => $comment,
                        'ts_pm_comment' => null,
                        'ts_pm_user' => null,
                        'ts_admin_comment' => null,
                        'ts_admin_user' => null,
                        'ts_total_seconds' => $api->getTsSeconds() + $api->getTsReceiptSeconds(),
                        'ts_flowid' => $this->TIMESHEET_PHASE_PM,
                    );                
                break;
        }
        if($api->update($data))
        {
            return $this->apiResponse->withArray(["data" =>["success" => true,"message"=>'Done!']]);
        }else{
            return $this->apiResponse->errorInternalError();
        }
    }

    // comment and reject
    private function commentAndReject($ts_id, $comment, $type='', $user_id=0){
        $api = AtTimesheet::find("ts_id IN ({$ts_id})");
        switch ($type) {
            case 'PM':
                $data = array(
                        'ts_pm_comment' => $comment,
                        'ts_pm_user' => $user_id,                        
                    );                
                break;
            default://ADMIN
                $data = array(
                        'ts_admin_comment' => $comment,
                        'ts_admin_user' => $user_id,                        
                    );
                break;
        }
        $data['ts_flowid'] = $this->TIMESHEET_PHASE_NONE;
        if($api->update($data))
        {
            return $this->apiResponse->withArray(["data" =>["success" => true,"message"=>'Done!']]);
        }else{
            return $this->apiResponse->errorInternalError();
        }
    }

}

// EOF
