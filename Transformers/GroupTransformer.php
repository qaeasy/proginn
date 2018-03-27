<?php


namespace hitachi\Phrest\Transformers;

use hitachi\Phrest\Models\AtLeaveReceipt;
use League\Fractal\TransformerAbstract;

class GroupTransformer extends TransformerAbstract
{
    /*
     * @Author: Larry Yang
     * @Date: 2016-05-19
     * @Description: Please add API Response data in to array
     * @return: array
     *
     */
    public function transform($datas)
    {
        foreach($datas as $data) {
            $return[]= [
                "group_id" => $data->getGroupId(),
                "group_name" => $data->getGroupName(),
            ];
        }
        return [
            "success"=> "true",
            "message"=> "ntest",
            "data"=>$return
        ];
    }
}

// EOF
