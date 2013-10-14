<?php
/**
 * File for class IHomeStructArrayOfAdditionalServiceItem
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructArrayOfAdditionalServiceItem originally named ArrayOfAdditionalServiceItem
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructArrayOfAdditionalServiceItem extends IHomeWsdlClass
{
	/**
	 * The AdditionalServiceItem
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : unbounded
	 * - minOccurs : 0
	 * - nillable : true
	 * @var IHomeStructAdditionalServiceItem
	 */
	public $AdditionalServiceItem;
	/**
	 * Constructor method for ArrayOfAdditionalServiceItem
	 * @see parent::__construct()
	 * @param IHomeStructAdditionalServiceItem $_additionalServiceItem
	 * @return IHomeStructArrayOfAdditionalServiceItem
	 */
	public function __construct($_additionalServiceItem = NULL)
	{
		parent::__construct(array('AdditionalServiceItem'=>$_additionalServiceItem));
	}
	/**
	 * Get AdditionalServiceItem value
	 * @return IHomeStructAdditionalServiceItem|null
	 */
	public function getAdditionalServiceItem()
	{
		return $this->AdditionalServiceItem;
	}
	/**
	 * Set AdditionalServiceItem value
	 * @param IHomeStructAdditionalServiceItem the AdditionalServiceItem
	 * @return IHomeStructAdditionalServiceItem
	 */
	public function setAdditionalServiceItem($_additionalServiceItem)
	{
		return ($this->AdditionalServiceItem = $_additionalServiceItem);
	}
	/**
	 * Returns the current element
	 * @see IHomeWsdlClass::current()
	 * @return IHomeStructAdditionalServiceItem
	 */
	public function current()
	{
		return parent::current();
	}
	/**
	 * Returns the indexed element
	 * @see IHomeWsdlClass::item()
	 * @param int $_index
	 * @return IHomeStructAdditionalServiceItem
	 */
	public function item($_index)
	{
		return parent::item($_index);
	}
	/**
	 * Returns the first element
	 * @see IHomeWsdlClass::first()
	 * @return IHomeStructAdditionalServiceItem
	 */
	public function first()
	{
		return parent::first();
	}
	/**
	 * Returns the last element
	 * @see IHomeWsdlClass::last()
	 * @return IHomeStructAdditionalServiceItem
	 */
	public function last()
	{
		return parent::last();
	}
	/**
	 * Returns the element at the offset
	 * @see IHomeWsdlClass::last()
	 * @param int $_offset
	 * @return IHomeStructAdditionalServiceItem
	 */
	public function offsetGet($_offset)
	{
		return parent::offsetGet($_offset);
	}
	/**
	 * Returns the attribute name
	 * @see IHomeWsdlClass::getAttributeName()
	 * @return string AdditionalServiceItem
	 */
	public function getAttributeName()
	{
		return 'AdditionalServiceItem';
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