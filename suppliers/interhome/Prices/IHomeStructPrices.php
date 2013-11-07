<?php
/**
 * File for class IHomeStructPrices
 * @package IHome
 * @subpackage Structs
 */
/**
 * This class stands for IHomeStructPrices originally named Prices
 * @package IHome
 * @subpackage Structs
 */
class IHomeStructPrices extends IHomeWsdlClass
{
	/**
	 * The inputValue
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructPricesInputValue
	 */
	public $inputValue;
	/**
	 * Constructor method for Prices
	 * @see parent::__construct()
	 * @param IHomeStructPricesInputValue $_inputValue
	 * @return IHomeStructPrices
	 */
	public function __construct($_inputValue = NULL)
	{
		parent::__construct(array('inputValue'=>$_inputValue));
	}
	/**
	 * Get inputValue value
	 * @return IHomeStructPricesInputValue|null
	 */
	public function getInputValue()
	{
		return $this->inputValue;
	}
	/**
	 * Set inputValue value
	 * @param IHomeStructPricesInputValue the inputValue
	 * @return IHomeStructPricesInputValue
	 */
	public function setInputValue($_inputValue)
	{
		return ($this->inputValue = $_inputValue);
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