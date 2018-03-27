<?php


namespace hitachi\Phrest\Transformers;

use hitachi\Phrest\Models\ApiKeysModel;
use League\Fractal\TransformerAbstract;

class KeyTransformer extends TransformerAbstract
{
    public function transform(ApiKeysModel $data)
    {
        return [
            "id" => (int)$data->getApiKeyId(),
            "user_id" => $data->getUserId(),
            "device_id" => $data->getDeviceId(),
            "key" => $data->getKey(),
            "level" => $data->getLevel(),
            "ignore_limit" => $data->getIgnoreLimit(),
            "created_at" => [
                "date" => $data->getCreatedAt(),
                "timezone" => date_default_timezone_get()
            ],
            "updated_at" => [
                "date" => $data->getUpdatedAt(),
                "timezone" => date_default_timezone_get()
            ]
        ];
    }
}

// EOF
