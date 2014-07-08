<?php
/**
 * File for class NewyseServiceStructTipTrips
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructTipTrips originally named TipTrips
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructTipTrips extends NewyseServiceWsdlClass
{
    /**
     * The TipTripItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructTipTrip
     */
    public $TipTripItem;
    /**
     * Constructor method for TipTrips
     * @see parent::__construct()
     * @param NewyseServiceStructTipTrip $_tipTripItem
     * @return NewyseServiceStructTipTrips
     */
    public function __construct($_tipTripItem = NULL)
    {
        parent::__construct(array('TipTripItem'=>$_tipTripItem),false);
    }
    /**
     * Get TipTripItem value
     * @return NewyseServiceStructTipTrip|null
     */
    public function getTipTripItem()
    {
        return $this->TipTripItem;
    }
    /**
     * Set TipTripItem value
     * @param NewyseServiceStructTipTrip $_tipTripItem the TipTripItem
     * @return NewyseServiceStructTipTrip
     */
    public function setTipTripItem($_tipTripItem)
    {
        return ($this->TipTripItem = $_tipTripItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructTipTrips
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
