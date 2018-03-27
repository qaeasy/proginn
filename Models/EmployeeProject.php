<?php
namespace hitachi\Phrest\Models;

class EmployeeProject extends \Phalcon\Mvc\Model
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
    protected $project_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $status;

    /**
     *
     * @var double
     * @Column(type="double", length=3, nullable=true)
     */
    protected $billing_percentage;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $join_date;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $release_date;

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
     * Method to set the value of field status
     *
     * @param integer $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Method to set the value of field billing_percentage
     *
     * @param double $billing_percentage
     * @return $this
     */
    public function setBillingPercentage($billing_percentage)
    {
        $this->billing_percentage = $billing_percentage;

        return $this;
    }

    /**
     * Method to set the value of field join_date
     *
     * @param string $join_date
     * @return $this
     */
    public function setJoinDate($join_date)
    {
        $this->join_date = $join_date;

        return $this;
    }

    /**
     * Method to set the value of field release_date
     *
     * @param string $release_date
     * @return $this
     */
    public function setReleaseDate($release_date)
    {
        $this->release_date = $release_date;

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
     * Returns the value of field project_id
     *
     * @return integer
     */
    public function getProjectId()
    {
        return $this->project_id;
    }

    /**
     * Returns the value of field status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the value of field billing_percentage
     *
     * @return double
     */
    public function getBillingPercentage()
    {
        return $this->billing_percentage;
    }

    /**
     * Returns the value of field join_date
     *
     * @return string
     */
    public function getJoinDate()
    {
        return $this->join_date;
    }

    /**
     * Returns the value of field release_date
     *
     * @return string
     */
    public function getReleaseDate()
    {
        return $this->release_date;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'employee_project';
    }

    public function initialize()
    {
        $this->belongsTo(
            "project_id",
            "hitachi\\Phrest\\Models\\Projects",
            "project_id",
            array(
                'alias' => 'Projects'
            )
        );
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return EmployeeProject[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return EmployeeProject
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
