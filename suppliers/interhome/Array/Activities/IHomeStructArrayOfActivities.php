<?php
/**
 * File for class IHomeStructArrayOfActivities
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructArrayOfActivities originally named ArrayOfActivities
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructArrayOfActivities extends IHomeWsdlClass
{
	/**
	 * The Activities
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : unbounded
	 * - minOccurs : 0
	 * @var IHomeEnumActivities
	 */
	public $Activities;
	/**
	 * Constructor method for ArrayOfActivities
	 * @see parent::__construct()
	 * @param IHomeEnumActivities $_activities
	 * @return IHomeStructArrayOfActivities
	 */
	public function __construct($_activities = NULL)
	{
		parent::__construct(array('Activities'=>$_activities));
	}
	/**
	 * Get Activities value
	 * @return IHomeEnumActivities|null
	 */
	public function getActivities()
	{
		return $this->Activities;
	}
	/**
	 * Set Activities value
	 * @param IHomeEnumActivities the Activities
	 * @return IHomeEnumActivities
	 */
	public function setActivities($_activities)
	{
		return ($this->Activities = $_activities);
	}
	/**
	 * Returns the current element
	 * @see IHomeWsdlClass::current()
	 * @return IHomeEnumActivities
	 */
	public function current()
	{
		return parent::current();
	}
	/**
	 * Returns the indexed element
	 * @see IHomeWsdlClass::item()
	 * @param int $_index
	 * @return IHomeEnumActivities
	 */
	public function item($_index)
	{
		return parent::item($_index);
	}
	/**
	 * Returns the first element
	 * @see IHomeWsdlClass::first()
	 * @return IHomeEnumActivities
	 */
	public function first()
	{
		return parent::first();
	}
	/**
	 * Returns the last element
	 * @see IHomeWsdlClass::last()
	 * @return IHomeEnumActivities
	 */
	public function last()
	{
		return parent::last();
	}
	/**
	 * Returns the element at the offset
	 * @see IHomeWsdlClass::last()
	 * @param int $_offset
	 * @return IHomeEnumActivities
	 */
	public function offsetGet($_offset)
	{
		return parent::offsetGet($_offset);
	}
	/**
	 * Add element to array
	 * @see IHomeWsdlClass::add()
	 * @uses IHomeEnumActivities::valueIsValid()
	 * @param IHomeEnumActivities $_item
	 * @return IHomeEnumActivities
	 */
	public function add($_item)
	{
		return IHomeEnumActivities::valueIsValid($_item)?parent::add($_item):false;
	}
	/**
	 * Returns the attribute name
	 * @see IHomeWsdlClass::getAttributeName()
	 * @return string Activities
	 */
	public function getAttributeName()
	{
		return 'Activities';
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