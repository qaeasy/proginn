<?php


namespace hitachi\Phrest\Transformers;

use League\Fractal\TransformerAbstract;

class SkillsetTransformer extends TransformerAbstract
{
    /*
     * @Author: Ace chen
     * @Date: 2016-12-14
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
                "skill_id" => (int) $row->getSkillId(),
                "skill" => $row->getSkill(),
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
