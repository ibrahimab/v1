<?php
/**
 * File for class NewyseServiceStructObjectAvailabilityContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructObjectAvailabilityContainer originally named ObjectAvailabilityContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructObjectAvailabilityContainer extends NewyseServiceWsdlClass
{
    /**
     * The ObjectAvailabilities
     * @var NewyseServiceStructObjectAvailabilities
     */
    public $ObjectAvailabilities;
    /**
     * Constructor method for ObjectAvailabilityContainer
     * @see parent::__construct()
     * @param NewyseServiceStructObjectAvailabilities $_objectAvailabilities
     * @return NewyseServiceStructObjectAvailabilityContainer
     */
    public function __construct($_objectAvailabilities = NULL)
    {
        parent::__construct(array('ObjectAvailabilities'=>$_objectAvailabilities),false);
    }
    /**
     * Get ObjectAvailabilities value
     * @return NewyseServiceStructObjectAvailabilities|null
     */
    public function getObjectAvailabilities()
    {
        return $this->ObjectAvailabilities;
    }
    /**
     * Set ObjectAvailabilities value
     * @param NewyseServiceStructObjectAvailabilities $_objectAvailabilities the ObjectAvailabilities
     * @return NewyseServiceStructObjectAvailabilities
     */
    public function setObjectAvailabilities($_objectAvailabilities)
    {
        return ($this->ObjectAvailabilities = $_objectAvailabilities);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructObjectAvailabilityContainer
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
