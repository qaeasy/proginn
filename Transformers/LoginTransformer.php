<?php


namespace hitachi\Phrest\Transformers;

use hitachi\Phrest\Models\Users;
use League\Fractal\TransformerAbstract;

class LoginTransformer extends TransformerAbstract
{
    /*
     * @Author: Larry Yang
     * @Date: 2016-04-29
     * @Description: Please add API Response data in to array
     * @return: array
     *
     */
    public function transform(Users $data)
    {
        return [
            "user_id" => (int)$data->getUserId(),
            "username" => $data->getUsername(),
            "full_name" => $data->getFullName(),
            "group_id" => $data->getGroup(),
            "last_visit" => $data->getLastVisit()
        ];
    }
}

// EOF
