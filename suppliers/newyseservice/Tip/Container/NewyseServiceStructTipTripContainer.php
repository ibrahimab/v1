<?php
/**
 * File for class NewyseServiceStructTipTripContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructTipTripContainer originally named TipTripContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructTipTripContainer extends NewyseServiceWsdlClass
{
    /**
     * The TipTrips
     * @var NewyseServiceStructTipTrips
     */
    public $TipTrips;
    /**
     * Constructor method for TipTripContainer
     * @see parent::__construct()
     * @param NewyseServiceStructTipTrips $_tipTrips
     * @return NewyseServiceStructTipTripContainer
     */
    public function __construct($_tipTrips = NULL)
    {
        parent::__construct(array('TipTrips'=>$_tipTrips),false);
    }
    /**
     * Get TipTrips value
     * @return NewyseServiceStructTipTrips|null
     */
    public function getTipTrips()
    {
        return $this->TipTrips;
    }
    /**
     * Set TipTrips value
     * @param NewyseServiceStructTipTrips $_tipTrips the TipTrips
     * @return NewyseServiceStructTipTrips
     */
    public function setTipTrips($_tipTrips)
    {
        return ($this->TipTrips = $_tipTrips);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructTipTripContainer
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
