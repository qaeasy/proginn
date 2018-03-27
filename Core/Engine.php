<?php


namespace hitachi\Phrest\Core;


use Phalcon\DI\Injectable;
use hitachi\Phrest\Models\ApiKeysModel;
use hitachi\Phrest\Models\ApiLogsModel;
use hitachi\Phrest\Core\Whitelist as WhitelistSecurity;
use hitachi\Phrest\Core\Limits\Key;
use hitachi\Phrest\Core\Limits\Method;
use hitachi\Phrest\Core\Exception\InvalidApiKey;
use hitachi\Phrest\Core\Exception\InvalidAuthException;

class Engine extends Injectable
{
    public function checkSession($session)
    {
        if ($session->has("user")) {
            return true;
        }else{
            throw new InvalidAuthException("Session time out", 401);
        }

    }
    public function checkKeyLevel($api_key, $api_annotation)
    {
        $key = ApiKeysModel::findFirst("key = '{$api_key}'");

        // check if api key exist and it has sufficent level to access resource
        if (!$key || $key->getLevel() < $api_annotation->getNamedArgument("level")) {
            throw new InvalidApiKey("Invalid API key", 403);
        }

        return $key;
    }

    public function checkKey($api_key, $user_id,$device_id)
    {
        $key = ApiKeysModel::findFirst("key = '{$api_key}' AND user_id = '{$user_id}' AND device_id = '{$device_id}'");

        // check if api key exist and it has sufficent level to access resource
        if (!$key) {
            throw new InvalidApiKey("Invalid API key", 403);
        }

        return $key;
    }

    public function checkKeyLimitOnClass($key, $limit_annotation)
    {
        // check limit for key to access all resources
        Key::get($key, $limit_annotation["key"]["increment"], $limit_annotation["key"]["limit"])->checkLimit();
    }

    public function checkMethodLimitByKey($key, $method_annotation)
    {
        // check key has exceed to access resource
        Method::get(
            $key,
            $this->request->get("_url"),
            $this->request->getMethod(),
            $method_annotation[0]["increment"],
            $method_annotation[0]["limit"]
        )->checkLimit();
    }

    public function log($key_id, $ip_address, $http_method, $route)
    {
        $params = [];
        if ($this->request->isGet() || $this->request->isDelete()) {
            $params = $this->request->get();
        } elseif ($this->request->isPost()) {
            $params = $this->request->getPost();
        } elseif ($this->request->isPut()) {
            $params = $this->request->getPut();
        }

        $logs = new ApiLogsModel();
        $logs->setApiKeyId($key_id);
        $logs->setIpAddress($ip_address);
        $logs->setMethod($http_method);
        $logs->setRoute($route);
        $logs->setParam(serialize($params));
        $logs->save();
    }
}

// EOF
