<?php
/**
 * File for class NewyseServiceStructCodes
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructCodes originally named Codes
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructCodes extends NewyseServiceWsdlClass
{
    /**
     * The Code
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var string
     */
    public $Code;
    /**
     * Constructor method for Codes
     * @see parent::__construct()
     * @param string $_code
     * @return NewyseServiceStructCodes
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
     * @return NewyseServiceStructCodes
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
