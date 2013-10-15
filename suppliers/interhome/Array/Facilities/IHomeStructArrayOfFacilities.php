<?php
/**
 * File for class IHomeStructArrayOfFacilities
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructArrayOfFacilities originally named ArrayOfFacilities
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructArrayOfFacilities extends IHomeWsdlClass
{
	/**
	 * The Facilities
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : unbounded
	 * - minOccurs : 0
	 * @var IHomeEnumFacilities
	 */
	public $Facilities;
	/**
	 * Constructor method for ArrayOfFacilities
	 * @see parent::__construct()
	 * @param IHomeEnumFacilities $_facilities
	 * @return IHomeStructArrayOfFacilities
	 */
	public function __construct($_facilities = NULL)
	{
		parent::__construct(array('Facilities'=>$_facilities));
	}
	/**
	 * Get Facilities value
	 * @return IHomeEnumFacilities|null
	 */
	public function getFacilities()
	{
		return $this->Facilities;
	}
	/**
	 * Set Facilities value
	 * @param IHomeEnumFacilities the Facilities
	 * @return IHomeEnumFacilities
	 */
	public function setFacilities($_facilities)
	{
		return ($this->Facilities = $_facilities);
	}
	/**
	 * Returns the current element
	 * @see IHomeWsdlClass::current()
	 * @return IHomeEnumFacilities
	 */
	public function current()
	{
		return parent::current();
	}
	/**
	 * Returns the indexed element
	 * @see IHomeWsdlClass::item()
	 * @param int $_index
	 * @return IHomeEnumFacilities
	 */
	public function item($_index)
	{
		return parent::item($_index);
	}
	/**
	 * Returns the first element
	 * @see IHomeWsdlClass::first()
	 * @return IHomeEnumFacilities
	 */
	public function first()
	{
		return parent::first();
	}
	/**
	 * Returns the last element
	 * @see IHomeWsdlClass::last()
	 * @return IHomeEnumFacilities
	 */
	public function last()
	{
		return parent::last();
	}
	/**
	 * Returns the element at the offset
	 * @see IHomeWsdlClass::last()
	 * @param int $_offset
	 * @return IHomeEnumFacilities
	 */
	public function offsetGet($_offset)
	{
		return parent::offsetGet($_offset);
	}
	/**
	 * Add element to array
	 * @see IHomeWsdlClass::add()
	 * @uses IHomeEnumFacilities::valueIsValid()
	 * @param IHomeEnumFacilities $_item
	 * @return IHomeEnumFacilities
	 */
	public function add($_item)
	{
		return IHomeEnumFacilities::valueIsValid($_item)?parent::add($_item):false;
	}
	/**
	 * Returns the attribute name
	 * @see IHomeWsdlClass::getAttributeName()
	 * @return string Facilities
	 */
	public function getAttributeName()
	{
		return 'Facilities';
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