<?php
namespace hitachi\Phrest\Models;

class Projects extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $project_id;

    /**
     *
     * @var string
     * @Column(type="string", length=200, nullable=true)
     */
    protected $project_nshore;

    /**
     *
     * @var string
     * @Column(type="string", length=200, nullable=true)
     */
    protected $project_name;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $project_manager;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $project_solution_domain;

    /**
     *
     * @var string
     * @Column(type="string", length=45, nullable=true)
     */
    protected $project_code_hccn;

    /**
     *
     * @var string
     * @Column(type="string", length=45, nullable=true)
     */
    protected $oracle_code;

    /**
     * Method to set the value of field project_id
     *
     * @param integer $project_id
     * @return $this
     */
    public function setProjectId($project_id)
    {
        $this->project_id = $project_id;

        return $this;
    }

    /**
     * Method to set the value of field project_nshore
     *
     * @param string $project_nshore
     * @return $this
     */
    public function setProjectNshore($project_nshore)
    {
        $this->project_nshore = $project_nshore;

        return $this;
    }

    /**
     * Method to set the value of field project_name
     *
     * @param string $project_name
     * @return $this
     */
    public function setProjectName($project_name)
    {
        $this->project_name = $project_name;

        return $this;
    }

    /**
     * Method to set the value of field project_manager
     *
     * @param integer $project_manager
     * @return $this
     */
    public function setProjectManager($project_manager)
    {
        $this->project_manager = $project_manager;

        return $this;
    }

    /**
     * Method to set the value of field project_solution_domain
     *
     * @param integer $project_solution_domain
     * @return $this
     */
    public function setProjectSolutionDomain($project_solution_domain)
    {
        $this->project_solution_domain = $project_solution_domain;

        return $this;
    }

    /**
     * Method to set the value of field project_code_hccn
     *
     * @param string $project_code_hccn
     * @return $this
     */
    public function setProjectCodeHccn($project_code_hccn)
    {
        $this->project_code_hccn = $project_code_hccn;

        return $this;
    }

    /**
     * Method to set the value of field oracle_code
     *
     * @param string $oracle_code
     * @return $this
     */
    public function setOracleCode($oracle_code)
    {
        $this->oracle_code = $oracle_code;

        return $this;
    }

    /**
     * Returns the value of field project_id
     *
     * @return integer
     */
    public function getProjectId()
    {
        return $this->project_id;
    }

    /**
     * Returns the value of field project_nshore
     *
     * @return string
     */
    public function getProjectNshore()
    {
        return $this->project_nshore;
    }

    /**
     * Returns the value of field project_name
     *
     * @return string
     */
    public function getProjectName()
    {
        return $this->project_name;
    }

    /**
     * Returns the value of field project_manager
     *
     * @return integer
     */
    public function getProjectManager()
    {
        return $this->project_manager;
    }

    /**
     * Returns the value of field project_solution_domain
     *
     * @return integer
     */
    public function getProjectSolutionDomain()
    {
        return $this->project_solution_domain;
    }

    /**
     * Returns the value of field project_code_hccn
     *
     * @return string
     */
    public function getProjectCodeHccn()
    {
        return $this->project_code_hccn;
    }

    /**
     * Returns the value of field oracle_code
     *
     * @return string
     */
    public function getOracleCode()
    {
        return $this->oracle_code;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'projects';
    }
    public function initialize()
    {
        $this->belongsto(
            "project_solution_domain",
            "hitachi\\Phrest\\Models\\SolutionDomain",
            "solution_id",
            array(
                'alias' => 'Solution'
            )
        );
        $this->belongsto(
            "project_manager",
            "hitachi\\Phrest\\Models\\ReportingManager",
            "id",
            array(
                'alias' => 'PM'
            )
        );
    }
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Projects[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Projects
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
