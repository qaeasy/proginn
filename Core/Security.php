<?php


namespace hitachi\Phrest\Core;


use Phalcon\Dispatcher;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use hitachi\Phrest\Core\Utils;
use hitachi\Phrest\Core\Engine as SecurityEngine;
use Phalcon\Exception as PhalconException;

class Security extends Plugin
{
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        try {
            // read class annotation
            $class_annotation = $this->annotations->get($dispatcher->getHandlerClass())->getClassAnnotations();
            $api_annotation = $class_annotation->get("Api");

            // read method annotation
            $method_annotation = $this->annotations->getMethod(
                $dispatcher->getHandlerClass(),
                $dispatcher->getActiveMethod()
            );

            // Get request method name from http request, eg: Post
            $api_request_method = ucfirst(strtolower($this->request->getMethod()));

            // Get request uri
            $api_uri = $method_annotation->get($api_request_method)->getArgument(0);
            // always return true due to we dont use api key

            if($api_uri == '/login'){
                return true; // if http request URI is login, then return true without key validation
            };
            return true;
            $engine = new SecurityEngine();
            $user = $engine->checkSession($this->session);
            if ($user) {
                return true;
            }

        } catch (PhalconException $e) {
            $this->apiResponse->withError($e->getMessage(), $e->getCode());
            return false;
        }

        return true;
    }
}

// EOF
