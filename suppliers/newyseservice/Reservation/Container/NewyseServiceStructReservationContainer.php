<?php
/**
 * File for class NewyseServiceStructReservationContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructReservationContainer originally named ReservationContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructReservationContainer extends NewyseServiceWsdlClass
{
    /**
     * The Reservations
     * @var NewyseServiceStructReservations
     */
    public $Reservations;
    /**
     * Constructor method for ReservationContainer
     * @see parent::__construct()
     * @param NewyseServiceStructReservations $_reservations
     * @return NewyseServiceStructReservationContainer
     */
    public function __construct($_reservations = NULL)
    {
        parent::__construct(array('Reservations'=>$_reservations),false);
    }
    /**
     * Get Reservations value
     * @return NewyseServiceStructReservations|null
     */
    public function getReservations()
    {
        return $this->Reservations;
    }
    /**
     * Set Reservations value
     * @param NewyseServiceStructReservations $_reservations the Reservations
     * @return NewyseServiceStructReservations
     */
    public function setReservations($_reservations)
    {
        return ($this->Reservations = $_reservations);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructReservationContainer
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
