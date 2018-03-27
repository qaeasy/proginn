<?php
namespace hitachi\Phrest\Models;

class Employees extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $employee_id;

    /**
     *
     * @var string
     * @Column(type="string", length=45, nullable=true)
     */
    protected $person_number;

    /**
     *
     * @var string
     * @Column(type="string", length=45, nullable=true)
     */
    protected $english_name;

    /**
     *
     * @var string
     * @Column(type="string", length=45, nullable=true)
     */
    protected $nick_name;

    /**
     *
     * @var string
     * @Column(type="string", length=45, nullable=true)
     */
    protected $chinese_name;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $level;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $solution_domain;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $reporting_manager;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $line_manager;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $career_advisor;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $work_location;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $base_location;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $joining_date;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $terminate_date;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    protected $gender;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $department;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $resource_type;

    /**
     *
     * @var string
     * @Column(type="string", length=200, nullable=true)
     */
    protected $comment;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setEmployeeId($employee_id)
    {
        $this->employee_id = $employee_id;

        return $this;
    }

    /**
     * Method to set the value of field person_number
     *
     * @param string $person_number
     * @return $this
     */
    public function setPersonNumber($person_number)
    {
        $this->person_number = $person_number;

        return $this;
    }

    /**
     * Method to set the value of field english_name
     *
     * @param string $english_name
     * @return $this
     */
    public function setEnglishName($english_name)
    {
        $this->english_name = $english_name;

        return $this;
    }

    /**
     * Method to set the value of field nick_name
     *
     * @param string $nick_name
     * @return $this
     */
    public function setNickName($nick_name)
    {
        $this->nick_name = $nick_name;

        return $this;
    }

    /**
     * Method to set the value of field chinese_name
     *
     * @param string $chinese_name
     * @return $this
     */
    public function setChineseName($chinese_name)
    {
        $this->chinese_name = $chinese_name;

        return $this;
    }

    /**
     * Method to set the value of field level
     *
     * @param integer $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Method to set the value of field solution_domain
     *
     * @param integer $solution_domain
     * @return $this
     */
    public function setSolutionDomain($solution_domain)
    {
        $this->solution_domain = $solution_domain;

        return $this;
    }

    /**
     * Method to set the value of field reporting_manager
     *
     * @param integer $reporting_manager
     * @return $this
     */
    public function setReportingManager($reporting_manager)
    {
        $this->reporting_manager = $reporting_manager;

        return $this;
    }

    /**
     * Method to set the value of field line_manager
     *
     * @param integer $line_manager
     * @return $this
     */
    public function setLineManager($line_manager)
    {
        $this->line_manager = $line_manager;

        return $this;
    }

    /**
     * Method to set the value of field career_advisor
     *
     * @param integer $career_advisor
     * @return $this
     */
    public function setCareerAdvisor($career_advisor)
    {
        $this->career_advisor = $career_advisor;

        return $this;
    }

    /**
     * Method to set the value of field work_location
     *
     * @param integer $work_location
     * @return $this
     */
    public function setWorkLocation($work_location)
    {
        $this->work_location = $work_location;

        return $this;
    }

    /**
     * Method to set the value of field base_location
     *
     * @param integer $base_location
     * @return $this
     */
    public function setBaseLocation($base_location)
    {
        $this->base_location = $base_location;

        return $this;
    }

    /**
     * Method to set the value of field joining_date
     *
     * @param string $joining_date
     * @return $this
     */
    public function setJoiningDate($joining_date)
    {
        $this->joining_date = $joining_date;

        return $this;
    }

    /**
     * Method to set the value of field terminate_date
     *
     * @param string $terminate_date
     * @return $this
     */
    public function setTerminateDate($terminate_date)
    {
        $this->terminate_date = $terminate_date;

        return $this;
    }

    /**
     * Method to set the value of field gender
     *
     * @param integer $gender
     * @return $this
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Method to set the value of field department_jinzai
     *
     * @param integer $department_jinzai
     * @return $this
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Method to set the value of field resource_type
     *
     * @param integer $resource_type
     * @return $this
     */
    public function setResourceType($resource_type)
    {
        $this->resource_type = $resource_type;

        return $this;
    }

    /**
     * Method to set the value of field comment
     *
     * @param string $comment
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getEmployeeId()
    {
        return $this->employee_id;
    }

    /**
     * Returns the value of field person_number
     *
     * @return string
     */
    public function getPersonNumber()
    {
        return $this->person_number;
    }

    /**
     * Returns the value of field english_name
     *
     * @return string
     */
    public function getEnglishName()
    {
        return $this->english_name;
    }

    /**
     * Returns the value of field nick_name
     *
     * @return string
     */
    public function getNickName()
    {
        return $this->nick_name;
    }

    /**
     * Returns the value of field chinese_name
     *
     * @return string
     */
    public function getChineseName()
    {
        return $this->chinese_name;
    }

    /**
     * Returns the value of field level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Returns the value of field solution_domain
     *
     * @return integer
     */
    public function getSolutionDomain()
    {
        return $this->solution_domain;
    }

    /**
     * Returns the value of field reporting_manager
     *
     * @return integer
     */
    public function getReportingManager()
    {
        return $this->reporting_manager;
    }

    /**
     * Returns the value of field line_manager
     *
     * @return integer
     */
    public function getLineManager()
    {
        return $this->line_manager;
    }

    /**
     * Returns the value of field career_advisor
     *
     * @return integer
     */
    public function getCareerAdvisor()
    {
        return $this->career_advisor;
    }

    /**
     * Returns the value of field work_location
     *
     * @return integer
     */
    public function getWorkLocation()
    {
        return $this->work_location;
    }

    /**
     * Returns the value of field base_location
     *
     * @return integer
     */
    public function getBaseLocation()
    {
        return $this->base_location;
    }

    /**
     * Returns the value of field joining_date
     *
     * @return string
     */
    public function getJoiningDate()
    {
        return $this->joining_date;
    }

    /**
     * Returns the value of field terminate_date
     *
     * @return string
     */
    public function getTerminateDate()
    {
        return $this->terminate_date;
    }

    /**
     * Returns the value of field gender
     *
     * @return integer
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Returns the value of field department_jinzai
     *
     * @return integer
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Returns the value of field resource_type
     *
     * @return integer
     */
    public function getResourceType()
    {
        return $this->resource_type;
    }

    /**
     * Returns the value of field comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'employees';
    }

    public function initialize()
    {
        $this->belongsto(
            "group_id",
            "hitachi\\Phrest\\Models\\UserGroup",
            "group_id",
            array(
                'alias' => 'Group'
            )
        );
        $this->belongsto(
            "solution_domain",
            "hitachi\\Phrest\\Models\\SolutionDomain",
            "solution_id",
            array(
                'alias' => 'Solution'
            )
        );
        $this->belongsto(
            "work_location",
            "hitachi\\Phrest\\Models\\Location",
            "location_id",
            array(
                'alias' => 'EmployeeWorkLocation'
            )
        );
        $this->belongsto(
            "base_location",
            "hitachi\\Phrest\\Models\\Location",
            "location_id",
            array(
                'alias' => 'EmployeeBaseLocation'
            )
        );
        $this->belongsto(
            "department",
            "hitachi\\Phrest\\Models\\Department",
            "id",
            array(
                'alias' => 'Deptmt'
            )
        );
        $this->belongsto(
            "level",
            "hitachi\\Phrest\\Models\\EmployeeLevel",
            "id",
            array(
                'alias' => 'Grade'
            )
        );

        $this->belongsto(
            "reporting_manager",
            "hitachi\\Phrest\\Models\\ReportingManager",
            "id",
            array(
                'alias' => 'RM'
            )
        );
        $this->belongsto(
            "line_manager",
            "hitachi\\Phrest\\Models\\ReportingManager",
            "id",
            array(
                'alias' => 'LM'
            )
        );
        $this->belongsto(
            "career_advisor",
            "hitachi\\Phrest\\Models\\ReportingManager",
            "id",
            array(
                'alias' => 'CA'
            )
        );
        $this->belongsto(
            "resource_type",
            "hitachi\\Phrest\\Models\\ResourceTypes",
            "id",
            array(
                'alias' => 'ResType'
            )
        );
        $this->hasMany(
            "employee_id",
            "hitachi\\Phrest\\Models\\EmployeeProject",
            "employee_id",
            array(
                'alias' => 'EmployeeProjects'
            )
        );
        $this->hasManyToMany(
            "employee_id",
            "hitachi\\Phrest\\Models\\EmployeeProject",
            "employee_id",
            "project_id",
            "hitachi\\Phrest\\Models\\Projects",
            "project_id",
            array(
                'alias' => 'EmployeeProjectData'
            )
        );

        $this->hasMany(
            "employee_id",
            "hitachi\\Phrest\\Models\\EmployeeSkills",
            "employee_id",
            array(
                'alias' => 'EmployeeSkills'
            )
        );
        $this->hasManyToMany(
            "employee_id",
            "hitachi\\Phrest\\Models\\EmployeeSkills",
            "employee_id",
            "skill_id",
            "hitachi\\Phrest\\Models\\SkillSet",
            "skill_id",
            array(
                'alias' => 'EmployeeSkillData'
            )
        );

    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Employees[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Employees
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
