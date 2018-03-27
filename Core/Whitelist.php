<?php


namespace hitachi\Phrest\Core;


use hitachi\Phrest\Core\Exception\InvalidAuthException;
use Phalcon\Mvc\User\Component;

class Whitelist extends Component
{
    protected $ip;

    public function __construct($ip)
    {
        $this->ip = $ip;
    }

    public function check()
    {
        $config = $this->di->get("config");

        if (!in_array($this->ip, (array)$config["whitelist"])) {
            throw new InvalidAuthException("Unauthorized", 401);
        }

        return true;
    }

    public static function get($ip)
    {
        return new self($ip);
    }
}

// EOF
