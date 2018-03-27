<?php
namespace hitachi\Phrest\Models;

class SkillSet extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $skill_id;

    /**
     *
     * @var string
     * @Column(type="string", length=45, nullable=true)
     */
    protected $skill;

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
     * Method to set the value of field skill
     *
     * @param string $skill
     * @return $this
     */
    public function setSkill($skill)
    {
        $this->skill = $skill;

        return $this;
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
     * Returns the value of field skill
     *
     * @return string
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'skill_set';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return SkillSet[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return SkillSet
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
