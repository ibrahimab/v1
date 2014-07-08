<?php
/**
 * File for class NewyseServiceStructPincodes
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructPincodes originally named Pincodes
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructPincodes extends NewyseServiceWsdlClass
{
    /**
     * The Pincode
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var string
     */
    public $Pincode;
    /**
     * Constructor method for Pincodes
     * @see parent::__construct()
     * @param string $_pincode
     * @return NewyseServiceStructPincodes
     */
    public function __construct($_pincode = NULL)
    {
        parent::__construct(array('Pincode'=>$_pincode),false);
    }
    /**
     * Get Pincode value
     * @return string|null
     */
    public function getPincode()
    {
        return $this->Pincode;
    }
    /**
     * Set Pincode value
     * @param string $_pincode the Pincode
     * @return string
     */
    public function setPincode($_pincode)
    {
        return ($this->Pincode = $_pincode);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructPincodes
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
