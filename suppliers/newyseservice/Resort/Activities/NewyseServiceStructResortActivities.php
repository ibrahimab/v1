<?php
/**
 * File for class NewyseServiceStructResortActivities
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructResortActivities originally named ResortActivities
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructResortActivities extends NewyseServiceWsdlClass
{
    /**
     * The ResortActivityItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructResortActivity
     */
    public $ResortActivityItem;
    /**
     * Constructor method for ResortActivities
     * @see parent::__construct()
     * @param NewyseServiceStructResortActivity $_resortActivityItem
     * @return NewyseServiceStructResortActivities
     */
    public function __construct($_resortActivityItem = NULL)
    {
        parent::__construct(array('ResortActivityItem'=>$_resortActivityItem),false);
    }
    /**
     * Get ResortActivityItem value
     * @return NewyseServiceStructResortActivity|null
     */
    public function getResortActivityItem()
    {
        return $this->ResortActivityItem;
    }
    /**
     * Set ResortActivityItem value
     * @param NewyseServiceStructResortActivity $_resortActivityItem the ResortActivityItem
     * @return NewyseServiceStructResortActivity
     */
    public function setResortActivityItem($_resortActivityItem)
    {
        return ($this->ResortActivityItem = $_resortActivityItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructResortActivities
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
