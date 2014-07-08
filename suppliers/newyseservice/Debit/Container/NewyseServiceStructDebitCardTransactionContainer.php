<?php
/**
 * File for class NewyseServiceStructDebitCardTransactionContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructDebitCardTransactionContainer originally named DebitCardTransactionContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructDebitCardTransactionContainer extends NewyseServiceWsdlClass
{
    /**
     * The DebitCardConsumptions
     * @var NewyseServiceStructDebitCardConsumptions
     */
    public $DebitCardConsumptions;
    /**
     * The Outlet
     * @var string
     */
    public $Outlet;
    /**
     * The ReservationId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $ReservationId;
    /**
     * The OrderId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $OrderId;
    /**
     * The DebitCardNumber
     * @var string
     */
    public $DebitCardNumber;
    /**
     * The CustomerId
     * @var long
     */
    public $CustomerId;
    /**
     * The ResortCode
     * @var string
     */
    public $ResortCode;
    /**
     * Constructor method for DebitCardTransactionContainer
     * @see parent::__construct()
     * @param NewyseServiceStructDebitCardConsumptions $_debitCardConsumptions
     * @param string $_outlet
     * @param long $_reservationId
     * @param long $_orderId
     * @param string $_debitCardNumber
     * @param long $_customerId
     * @param string $_resortCode
     * @return NewyseServiceStructDebitCardTransactionContainer
     */
    public function __construct($_debitCardConsumptions = NULL,$_outlet = NULL,$_reservationId = NULL,$_orderId = NULL,$_debitCardNumber = NULL,$_customerId = NULL,$_resortCode = NULL)
    {
        parent::__construct(array('DebitCardConsumptions'=>$_debitCardConsumptions,'Outlet'=>$_outlet,'ReservationId'=>$_reservationId,'OrderId'=>$_orderId,'DebitCardNumber'=>$_debitCardNumber,'CustomerId'=>$_customerId,'ResortCode'=>$_resortCode),false);
    }
    /**
     * Get DebitCardConsumptions value
     * @return NewyseServiceStructDebitCardConsumptions|null
     */
    public function getDebitCardConsumptions()
    {
        return $this->DebitCardConsumptions;
    }
    /**
     * Set DebitCardConsumptions value
     * @param NewyseServiceStructDebitCardConsumptions $_debitCardConsumptions the DebitCardConsumptions
     * @return NewyseServiceStructDebitCardConsumptions
     */
    public function setDebitCardConsumptions($_debitCardConsumptions)
    {
        return ($this->DebitCardConsumptions = $_debitCardConsumptions);
    }
    /**
     * Get Outlet value
     * @return string|null
     */
    public function getOutlet()
    {
        return $this->Outlet;
    }
    /**
     * Set Outlet value
     * @param string $_outlet the Outlet
     * @return string
     */
    public function setOutlet($_outlet)
    {
        return ($this->Outlet = $_outlet);
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
     * Get OrderId value
     * @return long|null
     */
    public function getOrderId()
    {
        return $this->OrderId;
    }
    /**
     * Set OrderId value
     * @param long $_orderId the OrderId
     * @return long
     */
    public function setOrderId($_orderId)
    {
        return ($this->OrderId = $_orderId);
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
     * @return NewyseServiceStructDebitCardTransactionContainer
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
