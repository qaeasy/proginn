<?php

/*
     * @Author: Larry Yang
     * @DCreated ate: 2016-04-29
     * @Description: User API
     *
     */
namespace hitachi\Phrest\Controllers\v1;

use Phalcon\Mvc\Controller;
use hitachi\Phrest\Models\Users;
use hitachi\Phrest\Models\UserGroup;
use hitachi\Phrest\Core\Utils;
use hitachi\Phrest\Core\Engine as SecurityEngine;
use hitachi\Phrest\Transformers\LoginTransformer;

/**
 * @RoutePrefix("/v1/user")
 * @Api(level=1,
 *   limits={
 *   "key" : {
 *   "increment" : "-1 day", "limit" : 1000}
 *   }
 * )
 */
class UserController extends Controller
{
    /**
     * @Post("/login")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */
    public function loginAction()
    {
        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");

        $login = Users::findFirst("(username = '{$username}' OR email = '{$username}') AND password='{$password}' AND active = '1'");

        if(!$login){
            $data = [
                "success"=> "false",
                "message"=> "Failed to login, please try again!"
            ];
            return $this->apiResponse->withArray($data);
        }
        $user = $this->_registerSession($login); // session
        return $this->apiResponse->withItem($user, new LoginTransformer());
    }

    /**
     * @Post("/checkUser")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */
    public function checkUserAction()
    {
        $username = $this->request->getPost("username");

        $checkUser = Users::findFirst("username = '{$username}'");
        if($checkUser){
            $data = [
                "success"=> "false",
                "message"=> "User already exist!"
            ];
            return $this->apiResponse->withArray($data);

        }
          
    }
    /**
     * @Post("/add")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */
    public function addAction()
    {
        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");
        $email = $this->request->getPost("email");
        /*
        $active = $this->request->getPost("active");
        $group = $this->request->getPost("group");
        $last_visit = $this->request->getPost("last_visit");
        $user_comment = $this->request->getPost("user_comment");*/
        $full_name = $this->request->getPost("full_name");

        $user = new Users();
        $checkUser = Users::findFirst("username = '{$username}'");
        if($checkUser){
            $data = [
                "success"=> "false",
                "message"=> "User already exist!"
            ];
            return $this->apiResponse->withArray($data);

        }else{
            $user->setUsername("$username");
            $user->setPassword("$password");
            $user->setEmail("$email");/*
            $user->setActive("$active");
            $user->setGroup("$group");
            $user->setLastVisit("$last_visit");
            $user->setComment("$user_comment");*/
            $user->setFullName("$full_name");
        }
        
        if($user->create()) {
            $data = [
                "success"=> "true",
                "message"=> "user has been created!"
            ];
            return $this->apiResponse->withArray($data);
        }else{
            $data = [
                "success"=> "false",
                "message"=> "Failed to create new user, please try again!"
            ];
            return $this->apiResponse->withArray($data);
        }
          
    }

    /**
     * @Post("/select")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */
    public function selectAction()
    { 
        
        $user_id = trim($this->request->getPost("user_id"));
        if($user_id){

            $where = "user_id IN ('{$user_id}')";
        }else{
            $where = "";
        }
        $conditions = $where;
        $data = Users::find(
            array(
                "conditions" => $conditions
            )
        );
        

        foreach($data as $row) {

            $return[]= [
                "user_id" => $row->user_id,
                "username" => $row->username,
                "password" => '',
                "email" => $row->email,
                "locale" => $row->user_loc,
                "active" => $row->active,
                "group" => $row->group,
                "theme" => $row->theme,
                "cookies" => $row->cookies,
                "last_visit"  => $row->last_visit,
                "comment" => $row->comment,
                "full_name" => $row->full_name,
            ];
        };

        $data = [
            "success"=> "true",
            "message"=> "users list",
            "data"=> [$return]
        ];

        if(!$data){
            return $this->apiResponse->errorUnauthorized();
        }else{
            return $this->apiResponse->withArray($data);
        }
        
          
    }
    /**
     * @Post("/update")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */
    public function updateAction()
    {

        $user_id = $this->request->getPost("user_id");
        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");
        $email = $this->request->getPost("email");
        $active = $this->request->getPost("user_active");
        $group = $this->request->getPost("user_group");
        $user = Users::findFirst("user_id = '{$user_id}'");

        if(!$user){
            $data = [
                "success"=> "false",
                "message"=> "user is not exist!"
            ];
            return $this->apiResponse->withArray($data);

        }else{
            if($username){
                $user->setUsername("$username");
            }
            if($password)$user->setPassword("$password");
            if($password){
                //$user->setPassword("$password");
            }
            if($email){
                $user->setEmail("$email");
            }
            if($active){
                $user->setActive("$active");
            }
            if($group){
                $user->setGroup("$group");
            }

        }
        
        if($user->save()) {
            $data = [
                "success"=> "true",
                "message"=> "User information updated!"
            ];
            return $this->apiResponse->withArray($data);
        }else{
            $data = [
                "success"=> "false",
                "message"=> "Failed to update user!"
            ];
            return $this->apiResponse->withArray($data);

        }
          
    }
    /**
     * @Post("/delete")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */
    public function deleteAction()
    {
        $user_id = $this->request->getPost("user_id");

        $user = Users::findFirst("user_id = '{$user_id}'");

        if(!$user){
            $data = [
                "success"=> "false",
                "message"=> "user is not exist!"
            ];
            return $this->apiResponse->withArray($data);

        }
        
        if(!$user->delete()) {
            return $this->apiResponse->errorInternalError();
        }

        $data = [
            "success"=> "true",
            "message"=> "user has been deleted!"
        ];
    
        return $this->apiResponse->withArray($data);
          
    }

    /**
     * @Get("/session")
     * @Post("/session")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */
    public function sessionAction()
    {
        /*$engine = new SecurityEngine();
        $user = $engine->getSession($this->session);*/
        $Utils = new Utils();
        $user = $Utils->getSession($this->session);
        if ($user) {
            // Retrieve its value
            $user_id = $user['user_id'];
            $username = $user['username'];
            return $this->apiResponse->withArray(["data" =>["user_id" => $user_id,"username" => $username]]);
        }else{
            $data = [
                "success"=> "false",
                "message"=> "User has not logged in!"
            ];
            return $this->apiResponse->withArray($data);
        }

    }

    /**
     * @Post("/check/auth")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */
    public function checkauthAction()
    {
        $user_id = $this->request->getHeader("HTTP_USER_ID");
        $auth_id = $this->request->getPost("auth_id");
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
        if(in_array("$auth_id", $ids)){
            return $this->apiResponse->withArray(["data" =>["auth" => true]]);
        }else{
            return $this->apiResponse->withArray(["data" =>["auth" => false]]);
        }

    }

    /**
     * @Post("/logout")
     * @Limit({"increment": "-1 hour", "limit": 50});
     * 
     */
    public function logoutAction()
    {
        $this->session->destroy();
/*        $Utils = new Utils();
        $user = $Utils->getSession($this->session);
        if ($user) {
            $data = [
                "success"=> "false",
                "message"=> "Failed to logout, try again!"
            ];
            return $this->apiResponse->withArray($data);
        }*/
        $data = [
            "success"=> "true",
            "message"=> "User logged out!"
        ];
        return $this->apiResponse->withArray($data);
    }

    /**
     * @Post("/groups")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */

    public function groupsAction()
    {
        $user_id = $this->request->getHeader("HTTP_USER_ID");

        $user = AmUser::findFirst("user_id = '{$user_id}'");
        $user_group_id = $user->getGroupId();
        $groups = AmGroup::find(
            array(
                "group_id = '{$user_group_id}' OR leader_id_list LIKE '%,{$user_id},%'"
            )
        );
        if ($groups) {
            return $this->apiResponse->withItem($groups, new GroupTransformer());
        } else {
            return $this->apiResponse->errorInternalError();
        }
    }

    /**
     * @Post("/ingroup")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */

    public function ingroupAction()
    {
        $user_id = $this->request->getHeader("HTTP_USER_ID");
        $group_id = $this->request->getPost("group_id");
        $user = AmUser::findFirst("user_id = '{$user_id}'");
        $user_group_id = $user->getGroupId();
        if($user_group_id ==$group_id)
        {
            $groups = AmGroup::find(
                array(
                    "group_id = '{$group_id}'"
                )
            )->count();
        }else{
            $groups = AmGroup::find(
                array(
                    "group_id = '{$group_id}' AND leader_id_list LIKE '%,{$user_id},%'"
                )
            )->count();
        }

        if($groups <= 0){
            return $this->apiResponse->errorUnauthorized();
        }

        $users = AmUser::find(
            array(
                "group_id = '{$group_id}'"
            )
        );

        $users->count = $users->count();


        if ($users) {

            return $this->apiResponse->withItem($users, new UserTransformer());
        } else {
            return $this->apiResponse->errorInternalError();
        }

    }

    //  register session data
    private function _registerSession($user)
    {
        $this->session->set(
            'user',
            array(
                'user_id'   => $user->user_id,
                'username'  => $user->username,
                'full_name' => $user->full_name,
                'group_id'  => $user->group,
                'last_visit' => date('Y-m-d H:i:s')
            )
        );
        $time = date('Y-m-d H:i:s');
        $user = Users::findFirst("user_id = '{$user->user_id}'");
        $user->setLastVisit($time);
        if($user->save()) {
            return $user;
        }
    }


}

// EOF
