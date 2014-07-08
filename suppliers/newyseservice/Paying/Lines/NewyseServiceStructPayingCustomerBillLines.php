<?php
/**
 * File for class NewyseServiceStructPayingCustomerBillLines
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructPayingCustomerBillLines originally named PayingCustomerBillLines
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructPayingCustomerBillLines extends NewyseServiceWsdlClass
{
    /**
     * The PayingCustomerBillLineItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructReservationBillLine
     */
    public $PayingCustomerBillLineItem;
    /**
     * Constructor method for PayingCustomerBillLines
     * @see parent::__construct()
     * @param NewyseServiceStructReservationBillLine $_payingCustomerBillLineItem
     * @return NewyseServiceStructPayingCustomerBillLines
     */
    public function __construct($_payingCustomerBillLineItem = NULL)
    {
        parent::__construct(array('PayingCustomerBillLineItem'=>$_payingCustomerBillLineItem),false);
    }
    /**
     * Get PayingCustomerBillLineItem value
     * @return NewyseServiceStructReservationBillLine|null
     */
    public function getPayingCustomerBillLineItem()
    {
        return $this->PayingCustomerBillLineItem;
    }
    /**
     * Set PayingCustomerBillLineItem value
     * @param NewyseServiceStructReservationBillLine $_payingCustomerBillLineItem the PayingCustomerBillLineItem
     * @return NewyseServiceStructReservationBillLine
     */
    public function setPayingCustomerBillLineItem($_payingCustomerBillLineItem)
    {
        return ($this->PayingCustomerBillLineItem = $_payingCustomerBillLineItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructPayingCustomerBillLines
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
