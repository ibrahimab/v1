<?php
/**
 * File for class NewyseServiceStructPrice
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructPrice originally named Price
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructPrice extends NewyseServiceWsdlClass
{
    /**
     * The Price
     * @var double
     */
    public $Price;
    /**
     * The PriceInclusive
     * @var double
     */
    public $PriceInclusive;
    /**
     * The ArrivalDate
     * @var dateTime
     */
    public $ArrivalDate;
    /**
     * The DepartureDate
     * @var dateTime
     */
    public $DepartureDate;
    /**
     * The CalculationDate
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var dateTime
     */
    public $CalculationDate;
    /**
     * The Quantity
     * @var int
     */
    public $Quantity;
    /**
     * The Special
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var NewyseServiceStructSpecial
     */
    public $Special;
    /**
     * Constructor method for Price
     * @see parent::__construct()
     * @param double $_price
     * @param double $_priceInclusive
     * @param dateTime $_arrivalDate
     * @param dateTime $_departureDate
     * @param dateTime $_calculationDate
     * @param int $_quantity
     * @param NewyseServiceStructSpecial $_special
     * @return NewyseServiceStructPrice
     */
    public function __construct($_price = NULL,$_priceInclusive = NULL,$_arrivalDate = NULL,$_departureDate = NULL,$_calculationDate = NULL,$_quantity = NULL,$_special = NULL)
    {
        parent::__construct(array('Price'=>$_price,'PriceInclusive'=>$_priceInclusive,'ArrivalDate'=>$_arrivalDate,'DepartureDate'=>$_departureDate,'CalculationDate'=>$_calculationDate,'Quantity'=>$_quantity,'Special'=>$_special),false);
    }
    /**
     * Get Price value
     * @return double|null
     */
    public function getPrice()
    {
        return $this->Price;
    }
    /**
     * Set Price value
     * @param double $_price the Price
     * @return double
     */
    public function setPrice($_price)
    {
        return ($this->Price = $_price);
    }
    /**
     * Get PriceInclusive value
     * @return double|null
     */
    public function getPriceInclusive()
    {
        return $this->PriceInclusive;
    }
    /**
     * Set PriceInclusive value
     * @param double $_priceInclusive the PriceInclusive
     * @return double
     */
    public function setPriceInclusive($_priceInclusive)
    {
        return ($this->PriceInclusive = $_priceInclusive);
    }
    /**
     * Get ArrivalDate value
     * @return dateTime|null
     */
    public function getArrivalDate()
    {
        return $this->ArrivalDate;
    }
    /**
     * Set ArrivalDate value
     * @param dateTime $_arrivalDate the ArrivalDate
     * @return dateTime
     */
    public function setArrivalDate($_arrivalDate)
    {
        return ($this->ArrivalDate = $_arrivalDate);
    }
    /**
     * Get DepartureDate value
     * @return dateTime|null
     */
    public function getDepartureDate()
    {
        return $this->DepartureDate;
    }
    /**
     * Set DepartureDate value
     * @param dateTime $_departureDate the DepartureDate
     * @return dateTime
     */
    public function setDepartureDate($_departureDate)
    {
        return ($this->DepartureDate = $_departureDate);
    }
    /**
     * Get CalculationDate value
     * @return dateTime|null
     */
    public function getCalculationDate()
    {
        return $this->CalculationDate;
    }
    /**
     * Set CalculationDate value
     * @param dateTime $_calculationDate the CalculationDate
     * @return dateTime
     */
    public function setCalculationDate($_calculationDate)
    {
        return ($this->CalculationDate = $_calculationDate);
    }
    /**
     * Get Quantity value
     * @return int|null
     */
    public function getQuantity()
    {
        return $this->Quantity;
    }
    /**
     * Set Quantity value
     * @param int $_quantity the Quantity
     * @return int
     */
    public function setQuantity($_quantity)
    {
        return ($this->Quantity = $_quantity);
    }
    /**
     * Get Special value
     * @return NewyseServiceStructSpecial|null
     */
    public function getSpecial()
    {
        return $this->Special;
    }
    /**
     * Set Special value
     * @param NewyseServiceStructSpecial $_special the Special
     * @return NewyseServiceStructSpecial
     */
    public function setSpecial($_special)
    {
        return ($this->Special = $_special);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructPrice
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
