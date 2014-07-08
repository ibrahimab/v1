<?php
/**
 * File for class NewyseServiceStructGetReservation
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructGetReservation originally named GetReservation
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructGetReservation extends NewyseServiceWsdlClass
{
    /**
     * The ReservationNumber
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $ReservationNumber;
    /**
     * The ReservationId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $ReservationId;
    /**
     * The ReturnBill
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $ReturnBill;
    /**
     * The ReturnCustomer
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $ReturnCustomer;
    /**
     * The ReturnResource
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $ReturnResource;
    /**
     * The ReturnInfotext
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var boolean
     */
    public $ReturnInfotext;
    /**
     * Constructor method for GetReservation
     * @see parent::__construct()
     * @param string $_reservationNumber
     * @param long $_reservationId
     * @param boolean $_returnBill
     * @param boolean $_returnCustomer
     * @param boolean $_returnResource
     * @param boolean $_returnInfotext
     * @return NewyseServiceStructGetReservation
     */
    public function __construct($_reservationNumber = NULL,$_reservationId = NULL,$_returnBill = NULL,$_returnCustomer = NULL,$_returnResource = NULL,$_returnInfotext = NULL)
    {
        parent::__construct(array('ReservationNumber'=>$_reservationNumber,'ReservationId'=>$_reservationId,'ReturnBill'=>$_returnBill,'ReturnCustomer'=>$_returnCustomer,'ReturnResource'=>$_returnResource,'ReturnInfotext'=>$_returnInfotext),false);
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
     * Get ReturnBill value
     * @return boolean|null
     */
    public function getReturnBill()
    {
        return $this->ReturnBill;
    }
    /**
     * Set ReturnBill value
     * @param boolean $_returnBill the ReturnBill
     * @return boolean
     */
    public function setReturnBill($_returnBill)
    {
        return ($this->ReturnBill = $_returnBill);
    }
    /**
     * Get ReturnCustomer value
     * @return boolean|null
     */
    public function getReturnCustomer()
    {
        return $this->ReturnCustomer;
    }
    /**
     * Set ReturnCustomer value
     * @param boolean $_returnCustomer the ReturnCustomer
     * @return boolean
     */
    public function setReturnCustomer($_returnCustomer)
    {
        return ($this->ReturnCustomer = $_returnCustomer);
    }
    /**
     * Get ReturnResource value
     * @return boolean|null
     */
    public function getReturnResource()
    {
        return $this->ReturnResource;
    }
    /**
     * Set ReturnResource value
     * @param boolean $_returnResource the ReturnResource
     * @return boolean
     */
    public function setReturnResource($_returnResource)
    {
        return ($this->ReturnResource = $_returnResource);
    }
    /**
     * Get ReturnInfotext value
     * @return boolean|null
     */
    public function getReturnInfotext()
    {
        return $this->ReturnInfotext;
    }
    /**
     * Set ReturnInfotext value
     * @param boolean $_returnInfotext the ReturnInfotext
     * @return boolean
     */
    public function setReturnInfotext($_returnInfotext)
    {
        return ($this->ReturnInfotext = $_returnInfotext);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructGetReservation
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
