<?php
/**
 * File for class IHomeStructPriceList
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructPriceList originally named PriceList
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructPriceList extends IHomeWsdlClass
{
	/**
	 * The inputValue
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructPriceListInputValue
	 */
	public $inputValue;
	/**
	 * Constructor method for PriceList
	 * @see parent::__construct()
	 * @param IHomeStructPriceListInputValue $_inputValue
	 * @return IHomeStructPriceList
	 */
	public function __construct($_inputValue = NULL)
	{
		parent::__construct(array('inputValue'=>$_inputValue));
	}
	/**
	 * Get inputValue value
	 * @return IHomeStructPriceListInputValue|null
	 */
	public function getInputValue()
	{
		return $this->inputValue;
	}
	/**
	 * Set inputValue value
	 * @param IHomeStructPriceListInputValue the inputValue
	 * @return IHomeStructPriceListInputValue
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