<?php
/**
 * File for class IHomeStructArrayOfPropertyTypes
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructArrayOfPropertyTypes originally named ArrayOfPropertyTypes
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructArrayOfPropertyTypes extends IHomeWsdlClass
{
	/**
	 * The PropertyTypes
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : unbounded
	 * - minOccurs : 0
	 * @var IHomeEnumPropertyTypes
	 */
	public $PropertyTypes;
	/**
	 * Constructor method for ArrayOfPropertyTypes
	 * @see parent::__construct()
	 * @param IHomeEnumPropertyTypes $_propertyTypes
	 * @return IHomeStructArrayOfPropertyTypes
	 */
	public function __construct($_propertyTypes = NULL)
	{
		parent::__construct(array('PropertyTypes'=>$_propertyTypes));
	}
	/**
	 * Get PropertyTypes value
	 * @return IHomeEnumPropertyTypes|null
	 */
	public function getPropertyTypes()
	{
		return $this->PropertyTypes;
	}
	/**
	 * Set PropertyTypes value
	 * @param IHomeEnumPropertyTypes the PropertyTypes
	 * @return IHomeEnumPropertyTypes
	 */
	public function setPropertyTypes($_propertyTypes)
	{
		return ($this->PropertyTypes = $_propertyTypes);
	}
	/**
	 * Returns the current element
	 * @see IHomeWsdlClass::current()
	 * @return IHomeEnumPropertyTypes
	 */
	public function current()
	{
		return parent::current();
	}
	/**
	 * Returns the indexed element
	 * @see IHomeWsdlClass::item()
	 * @param int $_index
	 * @return IHomeEnumPropertyTypes
	 */
	public function item($_index)
	{
		return parent::item($_index);
	}
	/**
	 * Returns the first element
	 * @see IHomeWsdlClass::first()
	 * @return IHomeEnumPropertyTypes
	 */
	public function first()
	{
		return parent::first();
	}
	/**
	 * Returns the last element
	 * @see IHomeWsdlClass::last()
	 * @return IHomeEnumPropertyTypes
	 */
	public function last()
	{
		return parent::last();
	}
	/**
	 * Returns the element at the offset
	 * @see IHomeWsdlClass::last()
	 * @param int $_offset
	 * @return IHomeEnumPropertyTypes
	 */
	public function offsetGet($_offset)
	{
		return parent::offsetGet($_offset);
	}
	/**
	 * Add element to array
	 * @see IHomeWsdlClass::add()
	 * @uses IHomeEnumPropertyTypes::valueIsValid()
	 * @param IHomeEnumPropertyTypes $_item
	 * @return IHomeEnumPropertyTypes
	 */
	public function add($_item)
	{
		return IHomeEnumPropertyTypes::valueIsValid($_item)?parent::add($_item):false;
	}
	/**
	 * Returns the attribute name
	 * @see IHomeWsdlClass::getAttributeName()
	 * @return string PropertyTypes
	 */
	public function getAttributeName()
	{
		return 'PropertyTypes';
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