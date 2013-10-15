<?php
/**
 * File for class IHomeStructAdditionalServices
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructAdditionalServices originally named AdditionalServices
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructAdditionalServices extends IHomeWsdlClass
{
	/**
	 * The inputValue
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructAdditionalServicesInputValue
	 */
	public $inputValue;
	/**
	 * Constructor method for AdditionalServices
	 * @see parent::__construct()
	 * @param IHomeStructAdditionalServicesInputValue $_inputValue
	 * @return IHomeStructAdditionalServices
	 */
	public function __construct($_inputValue = NULL)
	{
		parent::__construct(array('inputValue'=>$_inputValue));
	}
	/**
	 * Get inputValue value
	 * @return IHomeStructAdditionalServicesInputValue|null
	 */
	public function getInputValue()
	{
		return $this->inputValue;
	}
	/**
	 * Set inputValue value
	 * @param IHomeStructAdditionalServicesInputValue the inputValue
	 * @return IHomeStructAdditionalServicesInputValue
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