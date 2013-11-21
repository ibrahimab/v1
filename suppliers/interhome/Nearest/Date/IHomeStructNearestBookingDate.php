<?php
/**
 * File for class IHomeStructNearestBookingDate
 * @package IHome
 * @subpackage Structs
 */
/**
 * This class stands for IHomeStructNearestBookingDate originally named NearestBookingDate
 * @package IHome
 * @subpackage Structs
 */
class IHomeStructNearestBookingDate extends IHomeWsdlClass
{
	/**
	 * The inputValue
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructNearestBookingDateInputValue
	 */
	public $inputValue;
	/**
	 * Constructor method for NearestBookingDate
	 * @see parent::__construct()
	 * @param IHomeStructNearestBookingDateInputValue $_inputValue
	 * @return IHomeStructNearestBookingDate
	 */
	public function __construct($_inputValue = NULL)
	{
		parent::__construct(array('inputValue'=>$_inputValue));
	}
	/**
	 * Get inputValue value
	 * @return IHomeStructNearestBookingDateInputValue|null
	 */
	public function getInputValue()
	{
		return $this->inputValue;
	}
	/**
	 * Set inputValue value
	 * @param IHomeStructNearestBookingDateInputValue the inputValue
	 * @return IHomeStructNearestBookingDateInputValue
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