<?php
/**
 * File for class NewyseServiceStructSpecialCodes
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructSpecialCodes originally named SpecialCodes
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructSpecialCodes extends NewyseServiceWsdlClass
{
    /**
     * The SpecialCode
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var string
     */
    public $SpecialCode;
    /**
     * Constructor method for SpecialCodes
     * @see parent::__construct()
     * @param string $_specialCode
     * @return NewyseServiceStructSpecialCodes
     */
    public function __construct($_specialCode = NULL)
    {
        parent::__construct(array('SpecialCode'=>$_specialCode),false);
    }
    /**
     * Get SpecialCode value
     * @return string|null
     */
    public function getSpecialCode()
    {
        return $this->SpecialCode;
    }
    /**
     * Set SpecialCode value
     * @param string $_specialCode the SpecialCode
     * @return string
     */
    public function setSpecialCode($_specialCode)
    {
        return ($this->SpecialCode = $_specialCode);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructSpecialCodes
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
