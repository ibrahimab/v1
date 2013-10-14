<?php
/**
 * File for class IHomeStructArrayOfAdditionalServiceInputItem
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructArrayOfAdditionalServiceInputItem originally named ArrayOfAdditionalServiceInputItem
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructArrayOfAdditionalServiceInputItem extends IHomeWsdlClass
{
	/**
	 * The AdditionalServiceInputItem
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : unbounded
	 * - minOccurs : 0
	 * - nillable : true
	 * @var IHomeStructAdditionalServiceInputItem
	 */
	public $AdditionalServiceInputItem;
	/**
	 * Constructor method for ArrayOfAdditionalServiceInputItem
	 * @see parent::__construct()
	 * @param IHomeStructAdditionalServiceInputItem $_additionalServiceInputItem
	 * @return IHomeStructArrayOfAdditionalServiceInputItem
	 */
	public function __construct($_additionalServiceInputItem = NULL)
	{
		parent::__construct(array('AdditionalServiceInputItem'=>$_additionalServiceInputItem));
	}
	/**
	 * Get AdditionalServiceInputItem value
	 * @return IHomeStructAdditionalServiceInputItem|null
	 */
	public function getAdditionalServiceInputItem()
	{
		return $this->AdditionalServiceInputItem;
	}
	/**
	 * Set AdditionalServiceInputItem value
	 * @param IHomeStructAdditionalServiceInputItem the AdditionalServiceInputItem
	 * @return IHomeStructAdditionalServiceInputItem
	 */
	public function setAdditionalServiceInputItem($_additionalServiceInputItem)
	{
		return ($this->AdditionalServiceInputItem = $_additionalServiceInputItem);
	}
	/**
	 * Returns the current element
	 * @see IHomeWsdlClass::current()
	 * @return IHomeStructAdditionalServiceInputItem
	 */
	public function current()
	{
		return parent::current();
	}
	/**
	 * Returns the indexed element
	 * @see IHomeWsdlClass::item()
	 * @param int $_index
	 * @return IHomeStructAdditionalServiceInputItem
	 */
	public function item($_index)
	{
		return parent::item($_index);
	}
	/**
	 * Returns the first element
	 * @see IHomeWsdlClass::first()
	 * @return IHomeStructAdditionalServiceInputItem
	 */
	public function first()
	{
		return parent::first();
	}
	/**
	 * Returns the last element
	 * @see IHomeWsdlClass::last()
	 * @return IHomeStructAdditionalServiceInputItem
	 */
	public function last()
	{
		return parent::last();
	}
	/**
	 * Returns the element at the offset
	 * @see IHomeWsdlClass::last()
	 * @param int $_offset
	 * @return IHomeStructAdditionalServiceInputItem
	 */
	public function offsetGet($_offset)
	{
		return parent::offsetGet($_offset);
	}
	/**
	 * Returns the attribute name
	 * @see IHomeWsdlClass::getAttributeName()
	 * @return string AdditionalServiceInputItem
	 */
	public function getAttributeName()
	{
		return 'AdditionalServiceInputItem';
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