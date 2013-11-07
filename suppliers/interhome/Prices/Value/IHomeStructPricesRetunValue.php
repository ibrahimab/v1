<?php
/**
 * File for class IHomeStructPricesRetunValue
 * @package IHome
 * @subpackage Structs
 */
/**
 * This class stands for IHomeStructPricesRetunValue originally named PricesRetunValue
 * @package IHome
 * @subpackage Structs
 */
class IHomeStructPricesRetunValue extends IHomeStructReturnValue
{
	/**
	 * The Prices
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfPricesPriceItem
	 */
	public $Prices;
	/**
	 * Constructor method for PricesRetunValue
	 * @see parent::__construct()
	 * @param IHomeStructArrayOfPricesPriceItem $_prices
	 * @return IHomeStructPricesRetunValue
	 */
	public function __construct($_prices = NULL)
	{
		IHomeWsdlClass::__construct(array('Prices'=>($_prices instanceof IHomeStructArrayOfPricesPriceItem)?$_prices:new IHomeStructArrayOfPricesPriceItem($_prices)));
	}
	/**
	 * Get Prices value
	 * @return IHomeStructArrayOfPricesPriceItem|null
	 */
	public function getPrices()
	{
		return $this->Prices;
	}
	/**
	 * Set Prices value
	 * @param IHomeStructArrayOfPricesPriceItem the Prices
	 * @return IHomeStructArrayOfPricesPriceItem
	 */
	public function setPrices($_prices)
	{
		return ($this->Prices = $_prices);
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
?>