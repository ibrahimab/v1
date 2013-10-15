<?php
/**
 * File for class IHomeStructSearch
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructSearch originally named Search
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructSearch extends IHomeWsdlClass
{
	/**
	 * The inputValue
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructSearchInputValue
	 */
	public $inputValue;
	/**
	 * Constructor method for Search
	 * @see parent::__construct()
	 * @param IHomeStructSearchInputValue $_inputValue
	 * @return IHomeStructSearch
	 */
	public function __construct($_inputValue = NULL)
	{
		parent::__construct(array('inputValue'=>$_inputValue));
	}
	/**
	 * Get inputValue value
	 * @return IHomeStructSearchInputValue|null
	 */
	public function getInputValue()
	{
		return $this->inputValue;
	}
	/**
	 * Set inputValue value
	 * @param IHomeStructSearchInputValue the inputValue
	 * @return IHomeStructSearchInputValue
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