<?php


namespace hitachi\Phrest\Transformers;

use hitachi\Phrest\Models\AtLeaveReceipt;
use League\Fractal\TransformerAbstract;

class LeaveReceiptTransformer extends TransformerAbstract
{
    /*
     * @Author: Larry Yang
     * @Date: 2016-05-13
     * @Description: Please add API Response data in to array
     * @return: array
     *
     */
    public function transform(AtLeaveReceipt $data)
    {
            return [
                "l_id" => $data->getLId(),
                "l_receipt_no" => $data->getLReceiptNo(),
                "l_leave_type" => $data->getLLeaveType(),
                "l_fromdate" => $data->getLFromdate(),
                "l_enddate" => $data->getLEnddate(),
                "l_self_comment" => $data->getLSelfComment(),
                "l_year" => $data->getLYear(),
                "l_request_time" => $data->getLRequestTime(),
                "l_total_seconds" => $data->getLTotalSeconds(),
                "l_pm_user" => $data->pmuser->real_name,
                "l_pm_comment" => $data->getLPmComment(),
                "l_admin_user" => $data->adminuser->real_name,
                "l_admin_comment" => $data->getLAdminComment(),
                "l_flowid" => $data->getLFlowid()
            ];
    }
}

// EOF
