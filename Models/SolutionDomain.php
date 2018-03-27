<?php
namespace hitachi\Phrest\Models;

class SolutionDomain extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $solution_id;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=true)
     */
    protected $solution_name;

    /**
     * Method to set the value of field solution_id
     *
     * @param integer $solution_id
     * @return $this
     */
    public function setSolutionId($solution_id)
    {
        $this->solution_id = $solution_id;

        return $this;
    }

    /**
     * Method to set the value of field solution_name
     *
     * @param string $solution_name
     * @return $this
     */
    public function setSolutionName($solution_name)
    {
        $this->solution_name = $solution_name;

        return $this;
    }

    /**
     * Returns the value of field solution_id
     *
     * @return integer
     */
    public function getSolutionId()
    {
        return $this->solution_id;
    }

    /**
     * Returns the value of field solution_name
     *
     * @return string
     */
    public function getSolutionName()
    {
        return $this->solution_name;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'solution_domain';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return SolutionDomain[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return SolutionDomain
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
