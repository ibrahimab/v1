<?php
/**
 * File for class NewyseServiceStructDebitCardItemCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructDebitCardItemCriteria originally named DebitCardItemCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructDebitCardItemCriteria extends NewyseServiceWsdlClass
{
    /**
     * The DebitCardNumber
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var string
     */
    public $DebitCardNumber;
    /**
     * The ReservationId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $ReservationId;
    /**
     * The CustomerId
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var long
     */
    public $CustomerId;
    /**
     * Constructor method for DebitCardItemCriteria
     * @see parent::__construct()
     * @param string $_debitCardNumber
     * @param long $_reservationId
     * @param long $_customerId
     * @return NewyseServiceStructDebitCardItemCriteria
     */
    public function __construct($_debitCardNumber = NULL,$_reservationId = NULL,$_customerId = NULL)
    {
        parent::__construct(array('DebitCardNumber'=>$_debitCardNumber,'ReservationId'=>$_reservationId,'CustomerId'=>$_customerId),false);
    }
    /**
     * Get DebitCardNumber value
     * @return string|null
     */
    public function getDebitCardNumber()
    {
        return $this->DebitCardNumber;
    }
    /**
     * Set DebitCardNumber value
     * @param string $_debitCardNumber the DebitCardNumber
     * @return string
     */
    public function setDebitCardNumber($_debitCardNumber)
    {
        return ($this->DebitCardNumber = $_debitCardNumber);
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
     * Get CustomerId value
     * @return long|null
     */
    public function getCustomerId()
    {
        return $this->CustomerId;
    }
    /**
     * Set CustomerId value
     * @param long $_customerId the CustomerId
     * @return long
     */
    public function setCustomerId($_customerId)
    {
        return ($this->CustomerId = $_customerId);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructDebitCardItemCriteria
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
