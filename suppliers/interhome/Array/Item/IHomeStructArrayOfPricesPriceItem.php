<?php
/**
 * File for class IHomeStructArrayOfPricesPriceItem
 * @package IHome
 * @subpackage Structs
 */
/**
 * This class stands for IHomeStructArrayOfPricesPriceItem originally named ArrayOfPricesPriceItem
 * @package IHome
 * @subpackage Structs
 */
class IHomeStructArrayOfPricesPriceItem extends IHomeWsdlClass
{
	/**
	 * The PricesPriceItem
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : unbounded
	 * - minOccurs : 0
	 * - nillable : true
	 * @var IHomeStructPricesPriceItem
	 */
	public $PricesPriceItem;
	/**
	 * Constructor method for ArrayOfPricesPriceItem
	 * @see parent::__construct()
	 * @param IHomeStructPricesPriceItem $_pricesPriceItem
	 * @return IHomeStructArrayOfPricesPriceItem
	 */
	public function __construct($_pricesPriceItem = NULL)
	{
		parent::__construct(array('PricesPriceItem'=>$_pricesPriceItem));
	}
	/**
	 * Get PricesPriceItem value
	 * @return IHomeStructPricesPriceItem|null
	 */
	public function getPricesPriceItem()
	{
		return $this->PricesPriceItem;
	}
	/**
	 * Set PricesPriceItem value
	 * @param IHomeStructPricesPriceItem the PricesPriceItem
	 * @return IHomeStructPricesPriceItem
	 */
	public function setPricesPriceItem($_pricesPriceItem)
	{
		return ($this->PricesPriceItem = $_pricesPriceItem);
	}
	/**
	 * Returns the current element
	 * @see IHomeWsdlClass::current()
	 * @return IHomeStructPricesPriceItem
	 */
	public function current()
	{
		return parent::current();
	}
	/**
	 * Returns the indexed element
	 * @see IHomeWsdlClass::item()
	 * @param int $_index
	 * @return IHomeStructPricesPriceItem
	 */
	public function item($_index)
	{
		return parent::item($_index);
	}
	/**
	 * Returns the first element
	 * @see IHomeWsdlClass::first()
	 * @return IHomeStructPricesPriceItem
	 */
	public function first()
	{
		return parent::first();
	}
	/**
	 * Returns the last element
	 * @see IHomeWsdlClass::last()
	 * @return IHomeStructPricesPriceItem
	 */
	public function last()
	{
		return parent::last();
	}
	/**
	 * Returns the element at the offset
	 * @see IHomeWsdlClass::last()
	 * @param int $_offset
	 * @return IHomeStructPricesPriceItem
	 */
	public function offsetGet($_offset)
	{
		return parent::offsetGet($_offset);
	}
	/**
	 * Returns the attribute name
	 * @see IHomeWsdlClass::getAttributeName()
	 * @return string PricesPriceItem
	 */
	public function getAttributeName()
	{
		return 'PricesPriceItem';
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