<?php
/**
 * File for class IHomeStructArrayOfPriceListItem
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructArrayOfPriceListItem originally named ArrayOfPriceListItem
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructArrayOfPriceListItem extends IHomeWsdlClass
{
	/**
	 * The PriceListItem
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : unbounded
	 * - minOccurs : 0
	 * - nillable : true
	 * @var IHomeStructPriceListItem
	 */
	public $PriceListItem;
	/**
	 * Constructor method for ArrayOfPriceListItem
	 * @see parent::__construct()
	 * @param IHomeStructPriceListItem $_priceListItem
	 * @return IHomeStructArrayOfPriceListItem
	 */
	public function __construct($_priceListItem = NULL)
	{
		parent::__construct(array('PriceListItem'=>$_priceListItem));
	}
	/**
	 * Get PriceListItem value
	 * @return IHomeStructPriceListItem|null
	 */
	public function getPriceListItem()
	{
		return $this->PriceListItem;
	}
	/**
	 * Set PriceListItem value
	 * @param IHomeStructPriceListItem the PriceListItem
	 * @return IHomeStructPriceListItem
	 */
	public function setPriceListItem($_priceListItem)
	{
		return ($this->PriceListItem = $_priceListItem);
	}
	/**
	 * Returns the current element
	 * @see IHomeWsdlClass::current()
	 * @return IHomeStructPriceListItem
	 */
	public function current()
	{
		return parent::current();
	}
	/**
	 * Returns the indexed element
	 * @see IHomeWsdlClass::item()
	 * @param int $_index
	 * @return IHomeStructPriceListItem
	 */
	public function item($_index)
	{
		return parent::item($_index);
	}
	/**
	 * Returns the first element
	 * @see IHomeWsdlClass::first()
	 * @return IHomeStructPriceListItem
	 */
	public function first()
	{
		return parent::first();
	}
	/**
	 * Returns the last element
	 * @see IHomeWsdlClass::last()
	 * @return IHomeStructPriceListItem
	 */
	public function last()
	{
		return parent::last();
	}
	/**
	 * Returns the element at the offset
	 * @see IHomeWsdlClass::last()
	 * @param int $_offset
	 * @return IHomeStructPriceListItem
	 */
	public function offsetGet($_offset)
	{
		return parent::offsetGet($_offset);
	}
	/**
	 * Returns the attribute name
	 * @see IHomeWsdlClass::getAttributeName()
	 * @return string PriceListItem
	 */
	public function getAttributeName()
	{
		return 'PriceListItem';
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