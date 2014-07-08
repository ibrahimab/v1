<?php
/**
 * File for class NewyseServiceStructReservations
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructReservations originally named Reservations
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructReservations extends NewyseServiceWsdlClass
{
    /**
     * The ReservationItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructReservation
     */
    public $ReservationItem;
    /**
     * Constructor method for Reservations
     * @see parent::__construct()
     * @param NewyseServiceStructReservation $_reservationItem
     * @return NewyseServiceStructReservations
     */
    public function __construct($_reservationItem = NULL)
    {
        parent::__construct(array('ReservationItem'=>$_reservationItem),false);
    }
    /**
     * Get ReservationItem value
     * @return NewyseServiceStructReservation|null
     */
    public function getReservationItem()
    {
        return $this->ReservationItem;
    }
    /**
     * Set ReservationItem value
     * @param NewyseServiceStructReservation $_reservationItem the ReservationItem
     * @return NewyseServiceStructReservation
     */
    public function setReservationItem($_reservationItem)
    {
        return ($this->ReservationItem = $_reservationItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructReservations
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
