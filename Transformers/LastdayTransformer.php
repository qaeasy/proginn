<?php


namespace hitachi\Phrest\Transformers;

use hitachi\Phrest\Models\VwToday;
use League\Fractal\TransformerAbstract;

class LastdayTransformer extends TransformerAbstract
{
    /*
     * @Author: Larry Yang
     * @Date: 2016-04-29
     * @Description: Please add API Response data in to array
     * @return: array
     *
     */
    public function transform($data)
    {
        foreach($data as $row) {
            $return[]= [
                "dateTime" => date("Y-m-d H:i:s",strtotime($row->HourTick."0000")),
                "P" => $row->P
            ];
        }
        return [
            "success"=> "true",
            "message"=> "",
            "count" => $data->count,
            "data"=>$return
        ];
    }
}

// EOF
