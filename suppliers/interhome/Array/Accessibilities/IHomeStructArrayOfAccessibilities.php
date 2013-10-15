<?php
/**
 * File for class IHomeStructArrayOfAccessibilities
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructArrayOfAccessibilities originally named ArrayOfAccessibilities
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructArrayOfAccessibilities extends IHomeWsdlClass
{
	/**
	 * The Accessibilities
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : unbounded
	 * - minOccurs : 0
	 * @var IHomeEnumAccessibilities
	 */
	public $Accessibilities;
	/**
	 * Constructor method for ArrayOfAccessibilities
	 * @see parent::__construct()
	 * @param IHomeEnumAccessibilities $_accessibilities
	 * @return IHomeStructArrayOfAccessibilities
	 */
	public function __construct($_accessibilities = NULL)
	{
		parent::__construct(array('Accessibilities'=>$_accessibilities));
	}
	/**
	 * Get Accessibilities value
	 * @return IHomeEnumAccessibilities|null
	 */
	public function getAccessibilities()
	{
		return $this->Accessibilities;
	}
	/**
	 * Set Accessibilities value
	 * @param IHomeEnumAccessibilities the Accessibilities
	 * @return IHomeEnumAccessibilities
	 */
	public function setAccessibilities($_accessibilities)
	{
		return ($this->Accessibilities = $_accessibilities);
	}
	/**
	 * Returns the current element
	 * @see IHomeWsdlClass::current()
	 * @return IHomeEnumAccessibilities
	 */
	public function current()
	{
		return parent::current();
	}
	/**
	 * Returns the indexed element
	 * @see IHomeWsdlClass::item()
	 * @param int $_index
	 * @return IHomeEnumAccessibilities
	 */
	public function item($_index)
	{
		return parent::item($_index);
	}
	/**
	 * Returns the first element
	 * @see IHomeWsdlClass::first()
	 * @return IHomeEnumAccessibilities
	 */
	public function first()
	{
		return parent::first();
	}
	/**
	 * Returns the last element
	 * @see IHomeWsdlClass::last()
	 * @return IHomeEnumAccessibilities
	 */
	public function last()
	{
		return parent::last();
	}
	/**
	 * Returns the element at the offset
	 * @see IHomeWsdlClass::last()
	 * @param int $_offset
	 * @return IHomeEnumAccessibilities
	 */
	public function offsetGet($_offset)
	{
		return parent::offsetGet($_offset);
	}
	/**
	 * Add element to array
	 * @see IHomeWsdlClass::add()
	 * @uses IHomeEnumAccessibilities::valueIsValid()
	 * @param IHomeEnumAccessibilities $_item
	 * @return IHomeEnumAccessibilities
	 */
	public function add($_item)
	{
		return IHomeEnumAccessibilities::valueIsValid($_item)?parent::add($_item):false;
	}
	/**
	 * Returns the attribute name
	 * @see IHomeWsdlClass::getAttributeName()
	 * @return string Accessibilities
	 */
	public function getAttributeName()
	{
		return 'Accessibilities';
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