<?php

/*
     * @Author: Larry Yang
     * @Created Date: 2016-05-29
     * @Description: Leave API
     *
     */
namespace hitachi\Phrest\Controllers\v1;

use hitachi\Phrest\Models\AmGroup;
use hitachi\Phrest\Models\AtTimesheet;
use Phalcon\Mvc\Controller;
use hitachi\Phrest\Core\Utils;
use hitachi\Phrest\Models\AmUser;
use hitachi\Phrest\Models\AmUserAuthority;
use hitachi\Phrest\Models\AtLeaveReceipt;
use hitachi\Phrest\Models\AmConstantOption;
use hitachi\Phrest\Models\AtVacationHistory;
use hitachi\Phrest\Transformers\UserleaveTransformer;
use hitachi\Phrest\Transformers\LeaveReceiptTransformer;

/**
 * @RoutePrefix("/v1/leave")
 * @Api(level=1,
 *   limits={
 *   "key" : {
 *   "increment" : "-1 day", "limit" : 1000}
 *   }
 * )
 */
class LeaveController extends Controller
{
    /**
     * @Post("/search")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */
    public function indexAction()
    {
        $user_id = $this->request->getHeader("HTTP_USER_ID");
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

        if($this->request->getPost("year")){
            $l_year = $this->request->getPost("year");
        }else{
            $l_year = date("Y");
        }

        $l_receipt = AtLeaveReceipt::find(
            array(
                "user_id = '{$user_id}' AND l_year = '{$l_year}'",
                "order" => "l_fromdate DESC"
            )
        );
        $l_receipt->count = $l_receipt->count();

        if(!$l_receipt){
            return $this->apiResponse->errorUnauthorized();
        }else{
            return $this->apiResponse->withItem($l_receipt, new UserleaveTransformer());
        }        
    }

    /**
     * @Post("/add")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */
    public function addAction()
    {
        $user_id = $this->request->getHeader("HTTP_USER_ID");
        $l_leave_type= (int) $this->request->getPost("leave_type");
        $l_fromdate = trim($this->request->getPost("fromdate"));
        $l_enddate = trim($this->request->getPost("enddate"));
        $l_self_comment = $this->request->getPost("self_comment");
        $l_flowid = (int) $this->request->getPost("flowid");
        $l_year = date('Y');
        $l_request_time = date('Y-m-d H:i:s');
        $config = $this->di->get("config");
        $holidayDays = $config["holiday"]; # variable and fixed holidays
        $workdays = $config["workday"];

        $message = $this->leaveValidationRulesAction($l_fromdate, $l_enddate, $l_leave_type);

        if($message !==  true){
            return $this->apiResponse->withArray(["data" =>["success" => false,"message"=>$message]]);
        }
        $Utils = new Utils();
        $l_total_seconds = $Utils->getWorkingDays($l_fromdate,$l_enddate,$holidayDays,$workdays)*9*60*60;
        $this->db->begin();
        // Get key_cn
        $key_cn = AmConstantOption::findFirst(
            array(
                "c_type = '5' AND c_value = '{$l_leave_type}'"
            )
        )->getKeyCn();

        // Get tempNo
        $tempNo = AtLeaveReceipt::maximum(
            array(
                "column"     => "SUBSTRING(l_receipt_no,-4)",
                "conditions" => "l_leave_type = '{$l_leave_type}' AND  l_year = '{$l_year}'"
            )
        );

        if ($tempNo == null || strlen(trim($tempNo)) < 1) {

            $tempNo = "0";

        }

        $tempNo = $this->tranIntToString((int) $tempNo + 1, 4);


        $leave = new AtLeaveReceipt();
        $leave->setLReceiptNo("$key_cn-$l_year-$tempNo");
        $leave->setLLeaveType($l_leave_type);
        $leave->setLFromdate($l_fromdate);
        $leave->setLEnddate($l_enddate);
        $leave->setLSelfComment($l_self_comment);
        $leave->setUserId($user_id);
        $leave->setLYear($l_year);
        $leave->setLRequestTime($l_request_time);
        $leave->setLTotalSeconds($l_total_seconds);
        $leave->setLFlowid($l_flowid);
        $leave->setLPmUser(null);
        $leave->setLPmComment(null);
        $leave->setLAdminUser(null);
        $leave->setLAdminComment(null);

        if ($leave->save()) {
            $this->db->commit();
            return $this->apiResponse->withItem($leave, new LeaveReceiptTransformer());
        } else {
            $this->db->rollback();
            return $this->apiResponse->errorInternalError();
        }
    }

    /**
     * @Post("/get/todolist")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */

    public function gettdlAction()
    {
        $group_id = null;
        $flowid = '1,2,3,4';
        $user_id = $this->request->getHeader("HTTP_USER_ID");
        if($this->request->getPost("group_id"))
        {
            $group_id = $this->request->getPost("group_id");
        }

        if($this->request->getPost("flowid"))
        {
            $flowid = $this->request->getPost("flowid");
        }

        if(!is_null($group_id)){
            $groups = AmGroup::find(
                array(
                    "group_id = '{$group_id}' AND leader_id_list LIKE '%,{$user_id},%'"
                )
            );
            if($groups->count() <= 0){
                return $this->apiResponse->errorUnauthorized();
            }
            $l_receipt = $this->getLeaveReceiptByGroup($group_id, $flowid, $all = false);
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
            $l_receipt = $this->getLeaveReceiptByGroup($group_ids, $flowid, $all = true);
        }

        if ($l_receipt) {
            return $this->apiResponse->withItem($l_receipt, new UserleaveTransformer());
        } else {
            return $this->apiResponse->errorInternalError();
        }

    }

    private function getLeaveReceiptByGroup($group, $flowid, $all = false)
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
        $l_receipt = AtLeaveReceipt::find(
            array(
                "user_id IN ({$user_ids}) AND l_flowid IN ({$flowid})",
                "order" => "l_fromdate DESC"
            )
        );
        $l_receipt->count = $l_receipt->count();
        return $l_receipt;
    }

    /**
     * @Post("/get/one")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */

    public function getOneAction()
    {
        $user_id = $this->request->getHeader("HTTP_USER_ID");
        $l_id = $this->request->getPost("leave_id");
        $l_receipt = AtLeaveReceipt::findFirst(
            array(
                "user_id = '{$user_id}' AND l_id = '{$l_id}'"
            )
        );
        if ($l_receipt) {
            return $this->apiResponse->withItem($l_receipt, new LeaveReceiptTransformer());
        } else {
            return $this->apiResponse->errorInternalError();
        }

    }

    /**
     * @Post("/update")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */

    public function updateAction()
    {
        $user_id = $this->request->getHeader("HTTP_USER_ID");
        $l_leave_type= (int) $this->request->getPost("leave_type");
        $l_fromdate = trim($this->request->getPost("fromdate"));
        $l_enddate = trim($this->request->getPost("enddate"));
        $l_self_comment = $this->request->getPost("self_comment");
        $l_flowid = (int) $this->request->getPost("flowid");
        $l_request_time = date('Y-m-d H:i:s');
        $config = $this->di->get("config");
        $holidayDays = $config["holiday"]; # variable and fixed holidays
        $workdays = $config["workday"];
        $message = $this->leaveValidationRulesAction($l_fromdate, $l_enddate, $l_leave_type);

        if($message !==  true){
            return $this->apiResponse->withArray(["data" =>["success" => false,"message"=>$message]]);
        }
        $Utils = new Utils();
        $l_total_seconds = $Utils->getWorkingDays($l_fromdate,$l_enddate,$holidayDays,$workdays)*9*60*60;
        $l_id = (int) $this->request->getPost("leave_id");
        $leave = AtLeaveReceipt::findFirst("l_id = '{$l_id}' AND user_id = '{$user_id}'");
        // Get key_cn
        $key_cn = AmConstantOption::findFirst(
            array(
                "c_type = '5' AND c_value = '{$l_leave_type}'"
            )
        )->getKeyCn();
        $leaveR = $leave->l_receipt_no;
        $leaveReceiptNo = "$key_cn".substr($leave->l_receipt_no,-10);
        $this->db->begin();
        $leave->setLReceiptNo("$leaveReceiptNo");
        $leave->setLLeaveType($l_leave_type);
        $leave->setLFromdate($l_fromdate);
        $leave->setLEnddate($l_enddate);
        $leave->setLSelfComment($l_self_comment);
        $leave->setLRequestTime($l_request_time);
        $leave->setLTotalSeconds($l_total_seconds);
        $leave->setLFlowid($l_flowid);
        $leave->setLPmUser(null);
        $leave->setLPmComment(null);
        $leave->setLAdminUser(null);
        $leave->setLAdminComment(null);

        if ($leave->save()) {
            $this->db->commit();
            return $this->apiResponse->withItem($leave, new LeaveReceiptTransformer());
        } else {
            $this->db->rollback();
            return $this->apiResponse->errorInternalError();
        }
    }

    /**
     * @Post("/delete")
     * @Limit({"increment": "-1 hour", "limit": 50});
     */
    public function deleteAction()
    {
        $user_id = $this->request->getHeader("HTTP_USER_ID");
        $l_id = $this->request->get("leave_id");
        $l_receipt = AtLeaveReceipt::find(
            array(
                "user_id = '{$user_id}' AND l_id = '{$l_id}' AND l_flowid = 0"
            )
        );
        if (!$l_receipt) {
            return $this->apiResponse->errorInternalError();
        }

        if (!$l_receipt->delete()) {
            return $this->apiResponse->errorInternalError();
        }

        return $this->apiResponse->withArray(["deleted" => true]);
    }

    /**
     * @Post("/approve")
     * @Limit({"increment": "-1 hour", "limit": 50});
     */
    public function approveAction()
    {
        $user_id = (int) trim($this->request->getHeader("HTTP_USER_ID"));
        $l_id = (string) trim($this->request->getPost("leave_id"));

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

        $leave = AtLeaveReceipt::find("l_id IN ({$l_id})");

        if(in_array('1', $ids)){
            // PM approved

            $groups =  AmGroup::find(
                array(
                    "leader_id_list LIKE '%,{$user_id},%'"
                )
            );
            foreach ($groups as $row){
                $group_ids[] = $row->group_id;
            }
            foreach ($leave as $row)
            {
                $user_ids[] = $row->getUserId();
                // check if the currect user is supvisor
                if(!in_array($row->User->group_id,$group_ids)){
                    return $this->apiResponse->errorUnauthorized();
                }
            }

            $data = array(
                'l_pm_user' => $user_id,
                'l_flowid' => 3
            );
            if($leave->update($data))
            {
                return $this->apiResponse->withArray(["data" =>["success" => true,"message"=>'Done!']]);
            }else{
                return $this->apiResponse->errorInternalError();
            }
        }else{
            return $this->apiResponse->errorUnauthorized();
        }
    }

    /**
     * @Post("/reject")
     * @Limit({"increment": "-1 hour", "limit": 50});
     */
    public function rejectAction()
    {
        $user_id = (int) trim($this->request->getHeader("HTTP_USER_ID"));
        $l_id = (string) trim($this->request->getPost("leave_id"));
        $comment = trim($this->request->getPost("comment"));
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

        $leave = AtLeaveReceipt::find("l_id IN ({$l_id})");

        if(in_array('1', $ids)){
            // PM approved

            $groups =  AmGroup::find(
                array(
                    "leader_id_list LIKE '%,{$user_id},%'"
                )
            );
            foreach ($groups as $row){
                $group_ids[] = $row->group_id;
            }
            foreach ($leave as $row)
            {
                $user_ids[] = $row->getUserId();
                // check if the currect user is supvisor
                if(!in_array($row->User->group_id,$group_ids)){
                    return $this->apiResponse->errorUnauthorized();
                }
            }
            $data = array(
                'l_pm_user' => $user_id,
                'l_pm_comment' => $comment,
                'l_flowid' => 0
            );
            if($leave->update($data))
            {
                return $this->apiResponse->withArray(["data" =>["success" => true,"message"=>'Done!']]);
            }else{
                return $this->apiResponse->errorInternalError();
            }
        }else{
            return $this->apiResponse->errorUnauthorized();
        }
    }


    private function tranIntToString($val, $len)
    {
        $temp = strval($val);

        for ($i = strlen($temp); $i < $len; $i++)
        {
            $temp = "0".$temp;
        }
        return $temp;
    }

    private function leaveValidationRulesAction($startDate, $endDate, $leaveType){
        $user_id = $this->request->getHeader("HTTP_USER_ID");

        $startDateTime = strtotime($startDate);
        $endDateTime = strtotime($endDate);
        $startDateYear = date("Y",$startDateTime);
        // Rule 1: Begin date cannot be after end date!
        if ($startDate > $endDate)
        {
            return 'Begin date cannot be after end date!';
        }
        
        /*
         * Leave application is not allow to across two years.
         * Please split it into two parts in difference year's if you want!
         *
         * */          
        if ($startDateTime < strtotime($startDateYear."-04-01") && $endDateTime >= strtotime($startDateYear."-04-01"))
        {
            return 'Leave application is not allow to across two years.'.'<br/> Please split it into two parts in difference year\'s if you want!';
        }

        /*
         * Personal Leave's days cann't be more then 10 work days.
         * */

        /*
         * Invalid from/to date. Please check whether invalid days are included according to your leave type.
         * */
        if(date('w',$startDateTime) == 0 || date('w',$startDateTime) == 6 || date('w',$endDateTime) == 0 || date('w',$endDateTime) == 6){
            return 'Invalid start or end date. Please check whether invalid days are included according to your leave type.';
        }


        switch ($leaveType)
        {
            case 0:
                return $this->leaveAnnualVacationValidation($startDateYear, $user_id);
                break;
            case 1:
                break;
            case 3:
                return $this->leaveMarriageValidation($user_id);
                break;
            case 4:
                return $this->leaveMaternityValidation($user_id);
                break;
            case 5:
                return $this->leavePaternityValidation($user_id);
                break;
            case 6:
                break;
            case 7:                
                break;
            case 8:
                break;
            case 9:
                break;
            case 10:
                return $this->leaveBirthdayValidation($startDate, $endDate, $user_id);
                break;
            case 11:
                return $this->leaveMarriageAnniversaryValidation($startDate, $endDate, $startDateYear, $user_id);
                break;
            case 12:
                break;
            case 13:
                return $this->leaveWomendayValidation($startDate, $endDate, $user_id);
                break;
            case 14:
                return $this->leaveYoungdayValidation($startDate, $endDate, $user_id);
                break;
            case 15:
                break;
            default:
                break;
        }
        return true;
    }

    function leaveAnnualVacationValidation($year, $user_id){
        /*
         * You can't ask leave bacause your vacation balance hours are not correct,
         * please contact timesheet administrator to resolve it then ask leave again.
         *
         * */
        // Get User Has
        $userHas = AtVacationHistory::sum(
            array(
                "column"     => "v_seconds",
                "conditions" => "(v_oper_type = 0 OR v_oper_type = 2) 
        AND get_vacation_year(v_oper_time) = $year AND user_id = $user_id"
            )
        );
        // Get User Has Taken
        $userHasTaken = AtVacationHistory::sum(
            array(
                "column"     => "v_seconds",
                "conditions" => "(v_oper_type = 1) AND (v_receipt_no IS NULL OR v_receipt_no = '') 
        AND get_vacation_year(v_oper_time) = $year AND user_id = $user_id"
            )
        );
        // Get User remainder
        $userVacationSeconds = AmUser::findFirst(
                array(
                    "user_id = '{$user_id}'"
                )
            )->getVacationSeconds();
        // Check if user vacation balance = the value from Vacation History
        if($userVacationSeconds != $userHas - intval($userHasTaken)){
            return 'You can\'t ask leave bacause your vacation balance hours are not correct, please contact timesheet administrator to resolve it then ask leave again.';
        }

        return true;
    }

    function leaveMarriageValidation($user_id){
        // Marriage Leave can not be more then 3 days.
        if($this->getBeLeftDays($user_id, 3, 3)){
            return "Marriage Leave can not be more then 3 days.";
        }
        return true;
    }
    
    function leaveMaternityValidation($user_id){
        // You can not apply Additional Maternity Leave before the Maternity Leave is approved!
        if($this->checkBeforeLeaveNotDone($user_id, 4)){
            return 'You can not apply Additional Maternity Leave before the Maternity Leave is approved!';
        }
        // Maternity Leave can not be more then 128 days.
        if($this->getBeLeftDays($user_id, 4, 128)){
            return 'Maternity Leave can not be more then 128 days.';
        }
        //  Only Female have the Maternity Leave
        if(!$this->checkGender(1, $user_id)){
            return 'Only Female have the Maternity Leave.';
        }
        return true;
    }

    function leavePaternityValidation($user_id){
        //  Paternity Leave is only for male
        if(!$this->checkGender(0, $user_id)){
            return 'Paternity Leave is only for male';
        }
        // Paternity Leave can not be more then 15 days.
        if($this->getBeLeftDays($user_id, 5, 15)){
            return 'Paternity Leave can not be more then 15 days.';
        }
        return true;
    }

    function leaveBirthdayValidation($startDate, $endDate, $user_id){        
        // Birthday leave and marriage anniversary leave can only take one day.
        if(!$this->checkOnlyOneDay($startDate, $endDate)){
            return 'Birthday leave can only take one day.';
        }
        
        // YYYY-MM-DD is not your birthday! Please contact HR admin if something is wrong.
        $date = $this->getYearMonthDay($startDate);
        $lunar_birthday = $date['month'].$date['day'];
        $solar_birthday = $date['month'].'-'.$date['day'];        
        $birthday = AmUser::findFirst(
            array(
                "conditions" => "user_id = $user_id AND 
                (
                    (lunar_favored=1 AND SUBSTRING(lunar_birthday,5)='$lunar_birthday') 
                    OR
                    (lunar_favored=0 AND SUBSTRING(solar_birthday,6)='$solar_birthday')
                )"
            )
        );
        if(!$birthday){
            return "$startDate is not your birthday! Please contact HR admin if something is wrong.";
        }

        // You have taken the kind of leave in the fiscal year of April $year to March $year1 !
        if(!$this->checkMarriageOrBirthdayHasTaken($startDate, $endDate, $user_id)){
            $date = $this->getYearMonthDay($startDate);            
            if($date['year']>2014 && $date['month']<4){
                $year1 = $date['year'];
                $year = $date['year'] - 1;
            }else{
                $year = $date['year'];
                $year1 = $date['year'] + 1;
            }
            return "You have taken the kind of leave in the fiscal year of April $year to March $year1 !";
        }
        return true;
    }

    function leaveMarriageAnniversaryValidation($startDate, $endDate, $user_id){
        // Birthday leave and marriage anniversary leave can only take one day.
        if(!$this->checkOnlyOneDay($startDate, $endDate)){
            return "Marriage anniversary leave can only take one day.";
        }

        // You have taken the kind of leave in the fiscal year of April $year to March $year1 !
        if(!$this->checkMarriageOrBirthdayHasTaken($startDate, $endDate, $user_id)){
            $date = $this->getYearMonthDay($startDate);            
            if($date['year']>2014 && $date['month']<4){
                $year1 = $date['year'];
                $year = $date['year'] - 1;
            }else{
                $year = $date['year'];
                $year1 = $date['year'] + 1;
            }
            return "You have taken the kind of leave in the fiscal year of April $year to March $year1 !";
        }
        return true;
    }

    function leaveWomendayValidation($startDate, $endDate, $user_id){
        // You don't have the kind of leave!        
        if(!$this->checkGender(1, $user_id)){
            return "You don't have the kind of leave!";
        }

        if(!$this->checkOnlyOneDay($startDate, $endDate)){
            return "Women's day can only take one day.";
        }
        return true;
    }

    function leaveYoungdayValidation($startDate, $endDate, $user_id){
        // Your age is more than 28!
        $solar_birthday = AmUser::findFirst(
            array(
                    "user_id = '{$user_id}'",                    
                )
        )->getSolarBirthday();
        $age = $this->getAge($solar_birthday);
        if($age>28){
            return "Your age is more than 28!";
        }

        if(!$this->checkOnlyOneDay($startDate, $endDate)){
            return "Youth's day can only take one day.";
        }

        // You have taken the kind of leave this year!
        $this_year = date("Y");
        $hasTaken = AtLeaveReceipt::findFirst(
            array(
                "conditions" => "substring(l_fromdate,1,10) = '$this_year-05-04' AND user_id = $user_id AND l_leave_type = 14"
            )
        );
        if($hasTaken){
            return "You have taken the kind of leave this year!";
        }
        return true;
    }

    function checkMarriageOrBirthdayHasTaken($startDate, $endDate, $user_id){
        // $l_id = AtLeaveReceipt::findFirst(
        //     array(
        //         "conditions" => "l_fromdate = $startDate AND l_enddate = $endDate AND user_id = $user_id"
        //     )
        // )->getLId();
        $hasTaken = AtLeaveReceipt::findFirst(
            array(
                // "conditions" => "l_id <> $l_id AND get_vacation_year($startDate) = get_vacation_year(l_fromdate) AND user_id = $user_id AND l_leave_type IN(3, 10)"
                // "conditions" => "get_vacation_year('2008-01-28') = get_vacation_year(l_fromdate) AND user_id = 123 AND l_leave_type IN(3, 10)"
                "conditions" => "get_vacation_year($startDate) = get_vacation_year(l_fromdate) AND user_id = $user_id AND l_leave_type IN(3, 10)"
            )
        );
        if($hasTaken){
            return FALSE;
        }
        return true;
    }

    function getVacationYear($date){
        $date = $this->getYearMonthDay($date);
        if($date['year']>2014 && $date['month']<4){
            $date['year']--;
        }
        return $date['year'];
    }

    //$date is like 2016-01-01    
    function getYearMonthDay($date){        
        return array('year'=>date("Y",strtotime($date)),'month'=>date("m",strtotime($date)),'day'=>date("d",strtotime($date)));
    }

    function checkOnlyOneDay($startDate, $endDate){
        $date1 = explode(" ", $startDate);
        $date2 = explode(" ", $endDate);
        if($date1[0] != $date2[0]){
            return FALSE;
        }
        return TRUE;
    }

    function getAge($birth){         
        list($by,$bm,$bd)=explode('-',$birth);
        $cm=date('n');
        $cd=date('j');
        $age=date('Y')-$by-1;
        if ($cm>$bm || ($cm==$bm && $cd>$bd)) $age++;
        return $age;
    }

    function checkGender($gender, $user_id){
        $rightGender = AmUser::findFirst(
            array(
                    "user_id = '{$user_id}'",
                    "gender = $gender"
                )
        );
        if($rightGender->getGender() == $gender){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function getBeLeftDays($user_id, $type, $maxDays){
        $maxSeconds = $maxDays*3600*24;
        $l_total_seconds = AtLeaveReceipt::sum(
            array(
                "column"     => "l_total_seconds",
                "conditions" => "user_id = $user_id AND l_leave_type = $type"
            )
        );
        if($l_total_seconds >= $maxSeconds){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function checkBeforeLeaveNotDone($user_id, $type){
        $hasNotDone = AtLeaveReceipt::findFirst(
            array(                
                "conditions" => "user_id = $user_id AND l_leave_type = $type AND l_flowid BETWEEN 1 AND 3"
            )
        );
        if($hasNotDone){
            return TRUE;
        }else{
            return FALSE;
        }
    }
}

// EOF
