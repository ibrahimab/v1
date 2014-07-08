<?php
/**
 * File for class NewyseServiceStructDebitCardCustomer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructDebitCardCustomer originally named DebitCardCustomer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructDebitCardCustomer extends NewyseServiceWsdlClass
{
    /**
     * The CustomerInfo
     * @var NewyseServiceStructCustomerInfo
     */
    public $CustomerInfo;
    /**
     * The CustomerReservationInfo
     * @var NewyseServiceStructCustomerReservationInfo
     */
    public $CustomerReservationInfo;
    /**
     * Constructor method for DebitCardCustomer
     * @see parent::__construct()
     * @param NewyseServiceStructCustomerInfo $_customerInfo
     * @param NewyseServiceStructCustomerReservationInfo $_customerReservationInfo
     * @return NewyseServiceStructDebitCardCustomer
     */
    public function __construct($_customerInfo = NULL,$_customerReservationInfo = NULL)
    {
        parent::__construct(array('CustomerInfo'=>$_customerInfo,'CustomerReservationInfo'=>$_customerReservationInfo),false);
    }
    /**
     * Get CustomerInfo value
     * @return NewyseServiceStructCustomerInfo|null
     */
    public function getCustomerInfo()
    {
        return $this->CustomerInfo;
    }
    /**
     * Set CustomerInfo value
     * @param NewyseServiceStructCustomerInfo $_customerInfo the CustomerInfo
     * @return NewyseServiceStructCustomerInfo
     */
    public function setCustomerInfo($_customerInfo)
    {
        return ($this->CustomerInfo = $_customerInfo);
    }
    /**
     * Get CustomerReservationInfo value
     * @return NewyseServiceStructCustomerReservationInfo|null
     */
    public function getCustomerReservationInfo()
    {
        return $this->CustomerReservationInfo;
    }
    /**
     * Set CustomerReservationInfo value
     * @param NewyseServiceStructCustomerReservationInfo $_customerReservationInfo the CustomerReservationInfo
     * @return NewyseServiceStructCustomerReservationInfo
     */
    public function setCustomerReservationInfo($_customerReservationInfo)
    {
        return ($this->CustomerReservationInfo = $_customerReservationInfo);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructDebitCardCustomer
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
