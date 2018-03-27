<?php
namespace hitachi\Phrest\Models;

class EmployeeSkills extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $employee_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $skill_id;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field employee_id
     *
     * @param integer $employee_id
     * @return $this
     */
    public function setEmployeeId($employee_id)
    {
        $this->employee_id = $employee_id;

        return $this;
    }

    /**
     * Method to set the value of field skill_id
     *
     * @param integer $skill_id
     * @return $this
     */
    public function setSkillId($skill_id)
    {
        $this->skill_id = $skill_id;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field employee_id
     *
     * @return integer
     */
    public function getEmployeeId()
    {
        return $this->employee_id;
    }

    /**
     * Returns the value of field skill_id
     *
     * @return integer
     */
    public function getSkillId()
    {
        return $this->skill_id;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'employee_skills';
    }

    public function initialize()
    {
        $this->belongsTo(
            "skill_id",
            "hitachi\\Phrest\\Models\\SkillSet",
            "skill_id",
            array(
                'alias' => 'SkillSets'
            )
        );
    }
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return EmployeeSkills[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return EmployeeSkills
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
