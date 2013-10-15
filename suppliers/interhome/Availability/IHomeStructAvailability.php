<?php
/**
 * File for class IHomeStructAvailability
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructAvailability originally named Availability
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructAvailability extends IHomeWsdlClass
{
	/**
	 * The inputValue
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructAvailabilityInputValue
	 */
	public $inputValue;
	/**
	 * Constructor method for Availability
	 * @see parent::__construct()
	 * @param IHomeStructAvailabilityInputValue $_inputValue
	 * @return IHomeStructAvailability
	 */
	public function __construct($_inputValue = NULL)
	{
		parent::__construct(array('inputValue'=>$_inputValue));
	}
	/**
	 * Get inputValue value
	 * @return IHomeStructAvailabilityInputValue|null
	 */
	public function getInputValue()
	{
		return $this->inputValue;
	}
	/**
	 * Set inputValue value
	 * @param IHomeStructAvailabilityInputValue the inputValue
	 * @return IHomeStructAvailabilityInputValue
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