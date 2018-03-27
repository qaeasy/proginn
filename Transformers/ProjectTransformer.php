<?php


namespace hitachi\Phrest\Transformers;

use League\Fractal\TransformerAbstract;

class ProjectTransformer extends TransformerAbstract
{
    /*
     * @Author: Sefton Lin
     * @Date: 2016-11-17
     * @Description: Please add API Response data in to array
     * @return: array
     *
     */
    public function transform($data)
    {
        foreach($data as $row) {
            /*
            unset($projects);
            unset($skills);
            foreach($row->getEmployeeProjects() as $r) {
                $projects[] = $r->getProjects()->getProjectName();
            }
            foreach($row->getEmployeeSkills() as $skill) {
                $skills[] = $skill->getSkillSets()->getSkill();
            }*/

            $return[]= [
                "project_id" => (int) $row->getProjectId(),
                "project_nshore" => $row->getProjectNshore(),
                "project_name" => $row->getProjectName(),
                "project_manager" => $row->getProjectManager(),
                "project_solution_domain" => $row->getProjectSolutionDomain(),
                "project_code_hccn" => $row->getProjectCodeHccn(),
                "oracle_code" => $row->getOracleCode(),
            ];
        }
        return [
            "success"=> "true",
            "message"=> "ntest",
            "count" => $data->count(),
            "meta" => $data->meta,
            "data"=>$return
        ];
    }
}

// EOF
