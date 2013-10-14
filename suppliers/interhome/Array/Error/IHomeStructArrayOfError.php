<?php
/**
 * File for class IHomeStructArrayOfError
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructArrayOfError originally named ArrayOfError
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructArrayOfError extends IHomeWsdlClass
{
	/**
	 * The Error
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : unbounded
	 * - minOccurs : 0
	 * - nillable : true
	 * @var IHomeStructError
	 */
	public $Error;
	/**
	 * Constructor method for ArrayOfError
	 * @see parent::__construct()
	 * @param IHomeStructError $_error
	 * @return IHomeStructArrayOfError
	 */
	public function __construct($_error = NULL)
	{
		parent::__construct(array('Error'=>$_error));
	}
	/**
	 * Get Error value
	 * @return IHomeStructError|null
	 */
	public function getError()
	{
		return $this->Error;
	}
	/**
	 * Set Error value
	 * @param IHomeStructError the Error
	 * @return IHomeStructError
	 */
	public function setError($_error)
	{
		return ($this->Error = $_error);
	}
	/**
	 * Returns the current element
	 * @see IHomeWsdlClass::current()
	 * @return IHomeStructError
	 */
	public function current()
	{
		return parent::current();
	}
	/**
	 * Returns the indexed element
	 * @see IHomeWsdlClass::item()
	 * @param int $_index
	 * @return IHomeStructError
	 */
	public function item($_index)
	{
		return parent::item($_index);
	}
	/**
	 * Returns the first element
	 * @see IHomeWsdlClass::first()
	 * @return IHomeStructError
	 */
	public function first()
	{
		return parent::first();
	}
	/**
	 * Returns the last element
	 * @see IHomeWsdlClass::last()
	 * @return IHomeStructError
	 */
	public function last()
	{
		return parent::last();
	}
	/**
	 * Returns the element at the offset
	 * @see IHomeWsdlClass::last()
	 * @param int $_offset
	 * @return IHomeStructError
	 */
	public function offsetGet($_offset)
	{
		return parent::offsetGet($_offset);
	}
	/**
	 * Returns the attribute name
	 * @see IHomeWsdlClass::getAttributeName()
	 * @return string Error
	 */
	public function getAttributeName()
	{
		return 'Error';
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