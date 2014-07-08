<?php
/**
 * File for class NewyseServiceStructObjectAvailabilities
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructObjectAvailabilities originally named ObjectAvailabilities
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructObjectAvailabilities extends NewyseServiceWsdlClass
{
    /**
     * The ObjectAvailabilityItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructObjectAvailability
     */
    public $ObjectAvailabilityItem;
    /**
     * Constructor method for ObjectAvailabilities
     * @see parent::__construct()
     * @param NewyseServiceStructObjectAvailability $_objectAvailabilityItem
     * @return NewyseServiceStructObjectAvailabilities
     */
    public function __construct($_objectAvailabilityItem = NULL)
    {
        parent::__construct(array('ObjectAvailabilityItem'=>$_objectAvailabilityItem),false);
    }
    /**
     * Get ObjectAvailabilityItem value
     * @return NewyseServiceStructObjectAvailability|null
     */
    public function getObjectAvailabilityItem()
    {
        return $this->ObjectAvailabilityItem;
    }
    /**
     * Set ObjectAvailabilityItem value
     * @param NewyseServiceStructObjectAvailability $_objectAvailabilityItem the ObjectAvailabilityItem
     * @return NewyseServiceStructObjectAvailability
     */
    public function setObjectAvailabilityItem($_objectAvailabilityItem)
    {
        return ($this->ObjectAvailabilityItem = $_objectAvailabilityItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructObjectAvailabilities
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
