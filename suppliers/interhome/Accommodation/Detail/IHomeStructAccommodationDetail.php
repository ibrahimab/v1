<?php
/**
 * File for class IHomeStructAccommodationDetail
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructAccommodationDetail originally named AccommodationDetail
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructAccommodationDetail extends IHomeWsdlClass
{
	/**
	 * The inputValue
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructAccommodationDetailInputValue
	 */
	public $inputValue;
	/**
	 * Constructor method for AccommodationDetail
	 * @see parent::__construct()
	 * @param IHomeStructAccommodationDetailInputValue $_inputValue
	 * @return IHomeStructAccommodationDetail
	 */
	public function __construct($_inputValue = NULL)
	{
		parent::__construct(array('inputValue'=>$_inputValue));
	}
	/**
	 * Get inputValue value
	 * @return IHomeStructAccommodationDetailInputValue|null
	 */
	public function getInputValue()
	{
		return $this->inputValue;
	}
	/**
	 * Set inputValue value
	 * @param IHomeStructAccommodationDetailInputValue the inputValue
	 * @return IHomeStructAccommodationDetailInputValue
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