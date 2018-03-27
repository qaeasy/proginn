<?php
/**
 * Created by PhpStorm.
 * User: sefton
 * Date: 2016/11/16
 * Time: 15:38
 */
namespace hitachi\Phrest\Controllers\v1;
use Phalcon\Mvc\Controller;
use Phalcon\tag;
use hitachi\Phrest\Core\Library;

use hitachi\Phrest\Models\Projects;
use hitachi\Phrest\Models\SolutionDomain;
use hitachi\Phrest\Models\ReportingManager;

use hitachi\Phrest\Transformers\ProjectTransformer;

/**
 * @RoutePrefix("/v1/project")
 * @Api(level=1,
 *   limits={
 *   "key" : {
 *   "increment" : "-1 day", "limit" : 1000}
 *   }
 * )
 * Project controller
 */
class ProjectController extends Controller
{
    /**
     * @Post("/form")
     * @Limit({"increment": "-1 hour", "limit": 50});
     * @desc:
     *
     * @Parameters:
     *
     *
     */
    public function formAction()
    {
        // get permission, only users who are in group of administrator
        /*$user = $this->session->get("user");
        if(!$user){
            $message = 'User not logged in!';
            return $this->apiResponse->withArray(["data" =>["success" => false,"message"=>$message]]);
        }
        $group_id = $this->getEmployees($user[group_id]);
        if($group_id>2){
            $message = 'Forbidden, only administrators have permission to do this action!';
            return $this->apiResponse->withArray(["data" =>["success" => false,"message"=>$message]]);
        }*/
        // form inputs

        $form = $this->getFormData($this->request->getPost());

        return $this->apiResponse->withArray($form);

    }

    private function getFormData($posts){
        $id = $posts[id];

        if($id){
            $conditions = "project_id = '{$id}'";
            $res = Projects::findFirst($conditions);
        }else{
            $res = new Projects();
        }
        $solutions = SolutionDomain::find();
        $managers = ReportingManager::find();
        $form['Project name'] = $this->tag->textField(
            [
                "project_name",
                "size"        => 20,
                "maxlength"   => 30,
                "placeholder" => "Enter a project name",
                "class" => "form-control",
                "style" => "width:100%;",
                "value" =>  !is_null($res->getProjectName())?$res->getProjectName():''
            ]
        );
        $form['Project manager'] = $this->tag->select(
            [
                "project_manager",
                $managers,
                "using"      => [
                    "id",
                    "reporting_manager_name",
                ],
                "useEmpty"   => true,
                "data-placeholder" => !empty($res->getProjectManager())?implode(",",$res->getProjectManager()):'Please choose projects',
                "class" => "form-control",
                "style" => "width:100%;",
                "value" =>  !is_null($res->getProjectManager())?$res->getProjectManager():''
            ]
        );

        $form['Solution domain'] = $this->tag->select(
            [
                "solution_domain",
                $solutions,
                "using"      => [
                    "solution_id",
                    "solution_name",
                ],
                "useEmpty"   => true,
                "emptyText"  => "Please, choose one...",
                "emptyValue" => "@",
                "class" => "form-control",
                "style" => "width:100%;",
                "value" =>  !is_null($res->getProjectSolutionDomain())?$res->getProjectSolutionDomain():''
            ]
        );
        if (!empty($id)) {
            $form[''] = $this->tag->hiddenField(
                [
                    "id",
                    "value" => "$id"
                ]
            );
        }
        return $form;
    }

    /**
     * @Post("/list")
     * @Limit({"increment": "-1 hour", "limit": 50});
     * @desc:
     *
     * @Parameters:
     *
     *
     */
    public function projectListAction()
    {
        $user = $this->session->get("user");
        if(!$user){
            // TODO
            $message = 'User not logged in!';
            //return $this->apiResponse->withArray(["data" =>["success" => false,"message"=>$message]]);
        }

        //$res = $this->getEmployees($user[group_id]);
        $projects = $this->getProjects(1);
        $meta = Library::getMetaDataAttributes(new Projects());
        foreach ($meta as $key=>$value)
        {
            $metadata[] = ucfirst(str_replace('_',' ',$value));
        }
        $projects->meta = $metadata;
        if(!$projects){
            return $this->apiResponse->errorUnauthorized();
        }else{
            return $this->apiResponse->withItem($projects, new ProjectTransformer());
        }
    }

    private function getProjects($group)
    {
        switch ($group)
        {
            case 1:
                // Super administrator
                $data = Projects::find();
                break;
            case 2:
                $data = Projects::find(
                    array(
                        'columns' => '*'
                    )
                );
                break;
            case 3:
                $data = Projects::find(
                    array(
                        'columns' => '*'
                    )
                );
                break;
            case 4:
                $data = Projects::find(
                    array(
                        'columns' => '*'
                    )
                );
                break;
            case 5:
                $data = Projects::find(
                    array(
                        'columns' => '*'
                    )
                );
                break;
            default:
                return $this->apiResponse->errorUnauthorized();
                break;
        }
        return $data;
    }

    /**
     * @Post("/save")
     * @Limit({"increment": "-1 hour", "limit": 50});
     * @desc:create/update Employee data
     *
     * @Parameters:
     *
     *
     */

    public function saveProjectsAction()
    {
        $posts = $this->request->getPost();
        if($posts[id]){
            $projects = Projects::findFirst("project_id = '{$posts[id]}'");
        }else if (!$posts[id]){
            $projects = new Projects();
        }
        if (!$projects ){
            $message = 'Error!';
            return $this->apiResponse->withArray(["data" =>["success" => false,"message"=>$message]]);
        }
        $metadata = Library::getMetaDataAttributes($projects);
        if( !$projects->save($posts,$metadata)){
            $message = 'Error!';
            return $this->apiResponse->withArray(["data" =>["success" => false,"message"=>$message]]);
        }else{
            $message = 'Saved data!';
            return $this->apiResponse->withArray(["data" =>["success" => true,"message"=>$message]]);
        }

    }

    /**
     * @Post("/delete")
     * @Limit({"increment": "-1 hour", "limit": 50});
     * @desc:create/upate Employee data
     *
     * @Parameters:
     *
     *
     */

    public function deleteProjectsAction()
    {
        $project_id = $this->request->getPost("id");
        // Allow to delete employee one by one
        $project = Projects::findFirst("project_id = '{$project_id}'");
        if( !$project->delete()){
            $message = 'Error!';
            return $this->apiResponse->withArray(["data" =>["success" => false,"message"=>$message]]);
        }else{
            $message = 'Deleted data!';
            return $this->apiResponse->withArray(["data" =>["success" => true,"message"=>$message]]);
        }
    }

}

