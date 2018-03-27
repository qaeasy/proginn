<?php
namespace hitachi\Phrest\Models;

class CareerAdvisor extends \Phalcon\Mvc\Model
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
     * @Column(type="string", length=100, nullable=true)
     */
    protected $career_advisor_name;

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
     * Method to set the value of field career_advisor_name
     *
     * @param string $career_advisor_name
     * @return $this
     */
    public function setCareerAdvisorName($career_advisor_name)
    {
        $this->career_advisor_name = $career_advisor_name;

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
     * Returns the value of field career_advisor_name
     *
     * @return string
     */
    public function getCareerAdvisorName()
    {
        return $this->career_advisor_name;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'career_advisor';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return CareerAdvisor[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return CareerAdvisor
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
