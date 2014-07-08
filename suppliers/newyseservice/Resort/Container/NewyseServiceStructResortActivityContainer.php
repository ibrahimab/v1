<?php
/**
 * File for class NewyseServiceStructResortActivityContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructResortActivityContainer originally named ResortActivityContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructResortActivityContainer extends NewyseServiceWsdlClass
{
    /**
     * The ResortActivities
     * @var NewyseServiceStructResortActivities
     */
    public $ResortActivities;
    /**
     * Constructor method for ResortActivityContainer
     * @see parent::__construct()
     * @param NewyseServiceStructResortActivities $_resortActivities
     * @return NewyseServiceStructResortActivityContainer
     */
    public function __construct($_resortActivities = NULL)
    {
        parent::__construct(array('ResortActivities'=>$_resortActivities),false);
    }
    /**
     * Get ResortActivities value
     * @return NewyseServiceStructResortActivities|null
     */
    public function getResortActivities()
    {
        return $this->ResortActivities;
    }
    /**
     * Set ResortActivities value
     * @param NewyseServiceStructResortActivities $_resortActivities the ResortActivities
     * @return NewyseServiceStructResortActivities
     */
    public function setResortActivities($_resortActivities)
    {
        return ($this->ResortActivities = $_resortActivities);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructResortActivityContainer
     */
    public static function __set_state(array $_array,$_className = __CLASS__)
    {
        return parent::__set_state($_array,$_className);
    }
    /**
     * Method returning the class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
