<?php
/**
 * File for class NewyseServiceStructPayment
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructPayment originally named Payment
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructPayment extends NewyseServiceWsdlClass
{
    /**
     * The Status
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Status;
    /**
     * Constructor method for Payment
     * @see parent::__construct()
     * @param string $_status
     * @return NewyseServiceStructPayment
     */
    public function __construct($_status = NULL)
    {
        parent::__construct(array('Status'=>$_status),false);
    }
    /**
     * Get Status value
     * @return string|null
     */
    public function getStatus()
    {
        return $this->Status;
    }
    /**
     * Set Status value
     * @param string $_status the Status
     * @return string
     */
    public function setStatus($_status)
    {
        return ($this->Status = $_status);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructPayment
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
