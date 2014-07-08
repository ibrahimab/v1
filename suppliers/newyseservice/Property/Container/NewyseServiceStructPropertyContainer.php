<?php
/**
 * File for class NewyseServiceStructPropertyContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructPropertyContainer originally named PropertyContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructPropertyContainer extends NewyseServiceWsdlClass
{
    /**
     * The Properties
     * @var NewyseServiceStructProperties
     */
    public $Properties;
    /**
     * Constructor method for PropertyContainer
     * @see parent::__construct()
     * @param NewyseServiceStructProperties $_properties
     * @return NewyseServiceStructPropertyContainer
     */
    public function __construct($_properties = NULL)
    {
        parent::__construct(array('Properties'=>$_properties),false);
    }
    /**
     * Get Properties value
     * @return NewyseServiceStructProperties|null
     */
    public function getProperties()
    {
        return $this->Properties;
    }
    /**
     * Set Properties value
     * @param NewyseServiceStructProperties $_properties the Properties
     * @return NewyseServiceStructProperties
     */
    public function setProperties($_properties)
    {
        return ($this->Properties = $_properties);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructPropertyContainer
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
