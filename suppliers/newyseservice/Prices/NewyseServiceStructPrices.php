<?php
/**
 * File for class NewyseServiceStructPrices
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructPrices originally named Prices
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructPrices extends NewyseServiceWsdlClass
{
    /**
     * The PriceItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructPrice
     */
    public $PriceItem;
    /**
     * Constructor method for Prices
     * @see parent::__construct()
     * @param NewyseServiceStructPrice $_priceItem
     * @return NewyseServiceStructPrices
     */
    public function __construct($_priceItem = NULL)
    {
        parent::__construct(array('PriceItem'=>$_priceItem),false);
    }
    /**
     * Get PriceItem value
     * @return NewyseServiceStructPrice|null
     */
    public function getPriceItem()
    {
        return $this->PriceItem;
    }
    /**
     * Set PriceItem value
     * @param NewyseServiceStructPrice $_priceItem the PriceItem
     * @return NewyseServiceStructPrice
     */
    public function setPriceItem($_priceItem)
    {
        return ($this->PriceItem = $_priceItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructPrices
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
