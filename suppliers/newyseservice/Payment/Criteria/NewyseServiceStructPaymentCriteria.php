<?php
/**
 * File for class NewyseServiceStructPaymentCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructPaymentCriteria originally named PaymentCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructPaymentCriteria extends NewyseServiceWsdlClass
{
    /**
     * The ReservationId
     * @var long
     */
    public $ReservationId;
    /**
     * The PaymentMethodCode
     * @var string
     */
    public $PaymentMethodCode;
    /**
     * The Amount
     * @var double
     */
    public $Amount;
    /**
     * Constructor method for PaymentCriteria
     * @see parent::__construct()
     * @param long $_reservationId
     * @param string $_paymentMethodCode
     * @param double $_amount
     * @return NewyseServiceStructPaymentCriteria
     */
    public function __construct($_reservationId = NULL,$_paymentMethodCode = NULL,$_amount = NULL)
    {
        parent::__construct(array('ReservationId'=>$_reservationId,'PaymentMethodCode'=>$_paymentMethodCode,'Amount'=>$_amount),false);
    }
    /**
     * Get ReservationId value
     * @return long|null
     */
    public function getReservationId()
    {
        return $this->ReservationId;
    }
    /**
     * Set ReservationId value
     * @param long $_reservationId the ReservationId
     * @return long
     */
    public function setReservationId($_reservationId)
    {
        return ($this->ReservationId = $_reservationId);
    }
    /**
     * Get PaymentMethodCode value
     * @return string|null
     */
    public function getPaymentMethodCode()
    {
        return $this->PaymentMethodCode;
    }
    /**
     * Set PaymentMethodCode value
     * @param string $_paymentMethodCode the PaymentMethodCode
     * @return string
     */
    public function setPaymentMethodCode($_paymentMethodCode)
    {
        return ($this->PaymentMethodCode = $_paymentMethodCode);
    }
    /**
     * Get Amount value
     * @return double|null
     */
    public function getAmount()
    {
        return $this->Amount;
    }
    /**
     * Set Amount value
     * @param double $_amount the Amount
     * @return double
     */
    public function setAmount($_amount)
    {
        return ($this->Amount = $_amount);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructPaymentCriteria
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
