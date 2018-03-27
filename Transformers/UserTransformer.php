<?php


namespace hitachi\Phrest\Transformers;

use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /*
     * @Author: Larry Yang
     * @Date: 2016-04-29
     * @Description: Please add API Response data in to array
     * @return: array
     *
     */
    public function transform($datas)
    {
        foreach($datas as $data) {
            $return[]= [
                "user_id" => (int) $data->getUserId(),
                "english_name" => $data->getEnglishName(),
                "real_name" => $data->getRealName(),
                "group_id" => $data->getGroupId()
            ];
        }
        return [
            "success"=> "true",
            "message"=> "ntest",
            "count" => $datas->count,
            "data"=>$return
        ];
    }
}

// EOF
