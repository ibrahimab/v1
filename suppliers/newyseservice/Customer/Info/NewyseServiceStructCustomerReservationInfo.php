<?php
/**
 * File for class NewyseServiceStructCustomerReservationInfo
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructCustomerReservationInfo originally named CustomerReservationInfo
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructCustomerReservationInfo extends NewyseServiceWsdlClass
{
    /**
     * The ResortId
     * @var long
     */
    public $ResortId;
    /**
     * The ResortCode
     * @var string
     */
    public $ResortCode;
    /**
     * The ResortName
     * @var string
     */
    public $ResortName;
    /**
     * The ReservationNumber
     * @var string
     */
    public $ReservationNumber;
    /**
     * The ReservationId
     * @var long
     */
    public $ReservationId;
    /**
     * Constructor method for CustomerReservationInfo
     * @see parent::__construct()
     * @param long $_resortId
     * @param string $_resortCode
     * @param string $_resortName
     * @param string $_reservationNumber
     * @param long $_reservationId
     * @return NewyseServiceStructCustomerReservationInfo
     */
    public function __construct($_resortId = NULL,$_resortCode = NULL,$_resortName = NULL,$_reservationNumber = NULL,$_reservationId = NULL)
    {
        parent::__construct(array('ResortId'=>$_resortId,'ResortCode'=>$_resortCode,'ResortName'=>$_resortName,'ReservationNumber'=>$_reservationNumber,'ReservationId'=>$_reservationId),false);
    }
    /**
     * Get ResortId value
     * @return long|null
     */
    public function getResortId()
    {
        return $this->ResortId;
    }
    /**
     * Set ResortId value
     * @param long $_resortId the ResortId
     * @return long
     */
    public function setResortId($_resortId)
    {
        return ($this->ResortId = $_resortId);
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
     * Get ResortName value
     * @return string|null
     */
    public function getResortName()
    {
        return $this->ResortName;
    }
    /**
     * Set ResortName value
     * @param string $_resortName the ResortName
     * @return string
     */
    public function setResortName($_resortName)
    {
        return ($this->ResortName = $_resortName);
    }
    /**
     * Get ReservationNumber value
     * @return string|null
     */
    public function getReservationNumber()
    {
        return $this->ReservationNumber;
    }
    /**
     * Set ReservationNumber value
     * @param string $_reservationNumber the ReservationNumber
     * @return string
     */
    public function setReservationNumber($_reservationNumber)
    {
        return ($this->ReservationNumber = $_reservationNumber);
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
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructCustomerReservationInfo
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
