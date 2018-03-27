<?php
namespace hitachi\Phrest\Models;

class ReportingManager extends \Phalcon\Mvc\Model
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
     * @var string
     * @Column(type="string", length=45, nullable=true)
     */
    protected $reporting_manager_name;

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
     * Method to set the value of field reporting_manager_name
     *
     * @param string $reporting_manager_name
     * @return $this
     */
    public function setReportingManagerName($reporting_manager_name)
    {
        $this->reporting_manager_name = $reporting_manager_name;

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
     * Returns the value of field reporting_manager_name
     *
     * @return string
     */
    public function getReportingManagerName()
    {
        return $this->reporting_manager_name;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'reporting_manager';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ReportingManager[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ReportingManager
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
