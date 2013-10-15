<?php
/**
 * File for class IHomeStructReturnValue
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructReturnValue originally named ReturnValue
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructReturnValue extends IHomeWsdlClass
{
	/**
	 * The Ok
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $Ok;
	/**
	 * The Errors
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfError
	 */
	public $Errors;
	/**
	 * Constructor method for ReturnValue
	 * @see parent::__construct()
	 * @param boolean $_ok
	 * @param IHomeStructArrayOfError $_errors
	 * @return IHomeStructReturnValue
	 */
	public function __construct($_ok,$_errors = NULL)
	{
		parent::__construct(array('Ok'=>$_ok,'Errors'=>($_errors instanceof IHomeStructArrayOfError)?$_errors:new IHomeStructArrayOfError($_errors)));
	}
	/**
	 * Get Ok value
	 * @return boolean
	 */
	public function getOk()
	{
		return $this->Ok;
	}
	/**
	 * Set Ok value
	 * @param boolean the Ok
	 * @return boolean
	 */
	public function setOk($_ok)
	{
		return ($this->Ok = $_ok);
	}
	/**
	 * Get Errors value
	 * @return IHomeStructArrayOfError|null
	 */
	public function getErrors()
	{
		return $this->Errors;
	}
	/**
	 * Set Errors value
	 * @param IHomeStructArrayOfError the Errors
	 * @return IHomeStructArrayOfError
	 */
	public function setErrors($_errors)
	{
		return ($this->Errors = $_errors);
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