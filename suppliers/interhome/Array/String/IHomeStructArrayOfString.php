<?php
/**
 * File for class IHomeStructArrayOfString
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructArrayOfString originally named ArrayOfString
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructArrayOfString extends IHomeWsdlClass
{
	/**
	 * The string
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : unbounded
	 * - minOccurs : 0
	 * - nillable : true
	 * @var string
	 */
	public $string;
	/**
	 * Constructor method for ArrayOfString
	 * @see parent::__construct()
	 * @param string $_string
	 * @return IHomeStructArrayOfString
	 */
	public function __construct($_string = NULL)
	{
		parent::__construct(array('string'=>$_string));
	}
	/**
	 * Get string value
	 * @return string|null
	 */
	public function getString()
	{
		return $this->string;
	}
	/**
	 * Set string value
	 * @param string the string
	 * @return string
	 */
	public function setString($_string)
	{
		return ($this->string = $_string);
	}
	/**
	 * Returns the current element
	 * @see IHomeWsdlClass::current()
	 * @return string
	 */
	public function current()
	{
		return parent::current();
	}
	/**
	 * Returns the indexed element
	 * @see IHomeWsdlClass::item()
	 * @param int $_index
	 * @return string
	 */
	public function item($_index)
	{
		return parent::item($_index);
	}
	/**
	 * Returns the first element
	 * @see IHomeWsdlClass::first()
	 * @return string
	 */
	public function first()
	{
		return parent::first();
	}
	/**
	 * Returns the last element
	 * @see IHomeWsdlClass::last()
	 * @return string
	 */
	public function last()
	{
		return parent::last();
	}
	/**
	 * Returns the element at the offset
	 * @see IHomeWsdlClass::last()
	 * @param int $_offset
	 * @return string
	 */
	public function offsetGet($_offset)
	{
		return parent::offsetGet($_offset);
	}
	/**
	 * Returns the attribute name
	 * @see IHomeWsdlClass::getAttributeName()
	 * @return string string
	 */
	public function getAttributeName()
	{
		return 'string';
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