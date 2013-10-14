<?php
/**
 * File for class IHomeStructPriceDetail
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructPriceDetail originally named PriceDetail
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructPriceDetail extends IHomeWsdlClass
{
	/**
	 * The inputValue
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructPriceDetailInputValue
	 */
	public $inputValue;
	/**
	 * Constructor method for PriceDetail
	 * @see parent::__construct()
	 * @param IHomeStructPriceDetailInputValue $_inputValue
	 * @return IHomeStructPriceDetail
	 */
	public function __construct($_inputValue = NULL)
	{
		parent::__construct(array('inputValue'=>$_inputValue));
	}
	/**
	 * Get inputValue value
	 * @return IHomeStructPriceDetailInputValue|null
	 */
	public function getInputValue()
	{
		return $this->inputValue;
	}
	/**
	 * Set inputValue value
	 * @param IHomeStructPriceDetailInputValue the inputValue
	 * @return IHomeStructPriceDetailInputValue
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