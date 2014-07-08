<?php
/**
 * File for class NewyseServiceStructBrochureCodes
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructBrochureCodes originally named BrochureCodes
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructBrochureCodes extends NewyseServiceWsdlClass
{
    /**
     * The Code
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * @var string
     */
    public $Code;
    /**
     * Constructor method for BrochureCodes
     * @see parent::__construct()
     * @param string $_code
     * @return NewyseServiceStructBrochureCodes
     */
    public function __construct($_code = NULL)
    {
        parent::__construct(array('Code'=>$_code),false);
    }
    /**
     * Get Code value
     * @return string|null
     */
    public function getCode()
    {
        return $this->Code;
    }
    /**
     * Set Code value
     * @param string $_code the Code
     * @return string
     */
    public function setCode($_code)
    {
        return ($this->Code = $_code);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructBrochureCodes
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
