<?php
/**
 * File for class NewyseServiceStructBrochureCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructBrochureCriteria originally named BrochureCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructBrochureCriteria extends NewyseServiceWsdlClass
{
    /**
     * The ResortCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $ResortCode;
    /**
     * Constructor method for BrochureCriteria
     * @see parent::__construct()
     * @param string $_resortCode
     * @return NewyseServiceStructBrochureCriteria
     */
    public function __construct($_resortCode = NULL)
    {
        parent::__construct(array('ResortCode'=>$_resortCode),false);
    }
    /**
     * Get ResortCode value
     * @return string|null
     */
    public function getResortCode()
    {
        return $this->ResortCode;
    }
    /**
     * Set ResortCode value
     * @param string $_resortCode the ResortCode
     * @return string
     */
    public function setResortCode($_resortCode)
    {
        return ($this->ResortCode = $_resortCode);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructBrochureCriteria
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
