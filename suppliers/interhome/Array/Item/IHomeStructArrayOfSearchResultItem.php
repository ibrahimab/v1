<?php
/**
 * File for class IHomeStructArrayOfSearchResultItem
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructArrayOfSearchResultItem originally named ArrayOfSearchResultItem
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructArrayOfSearchResultItem extends IHomeWsdlClass
{
	/**
	 * The SearchResultItem
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : unbounded
	 * - minOccurs : 0
	 * - nillable : true
	 * @var IHomeStructSearchResultItem
	 */
	public $SearchResultItem;
	/**
	 * Constructor method for ArrayOfSearchResultItem
	 * @see parent::__construct()
	 * @param IHomeStructSearchResultItem $_searchResultItem
	 * @return IHomeStructArrayOfSearchResultItem
	 */
	public function __construct($_searchResultItem = NULL)
	{
		parent::__construct(array('SearchResultItem'=>$_searchResultItem));
	}
	/**
	 * Get SearchResultItem value
	 * @return IHomeStructSearchResultItem|null
	 */
	public function getSearchResultItem()
	{
		return $this->SearchResultItem;
	}
	/**
	 * Set SearchResultItem value
	 * @param IHomeStructSearchResultItem the SearchResultItem
	 * @return IHomeStructSearchResultItem
	 */
	public function setSearchResultItem($_searchResultItem)
	{
		return ($this->SearchResultItem = $_searchResultItem);
	}
	/**
	 * Returns the current element
	 * @see IHomeWsdlClass::current()
	 * @return IHomeStructSearchResultItem
	 */
	public function current()
	{
		return parent::current();
	}
	/**
	 * Returns the indexed element
	 * @see IHomeWsdlClass::item()
	 * @param int $_index
	 * @return IHomeStructSearchResultItem
	 */
	public function item($_index)
	{
		return parent::item($_index);
	}
	/**
	 * Returns the first element
	 * @see IHomeWsdlClass::first()
	 * @return IHomeStructSearchResultItem
	 */
	public function first()
	{
		return parent::first();
	}
	/**
	 * Returns the last element
	 * @see IHomeWsdlClass::last()
	 * @return IHomeStructSearchResultItem
	 */
	public function last()
	{
		return parent::last();
	}
	/**
	 * Returns the element at the offset
	 * @see IHomeWsdlClass::last()
	 * @param int $_offset
	 * @return IHomeStructSearchResultItem
	 */
	public function offsetGet($_offset)
	{
		return parent::offsetGet($_offset);
	}
	/**
	 * Returns the attribute name
	 * @see IHomeWsdlClass::getAttributeName()
	 * @return string SearchResultItem
	 */
	public function getAttributeName()
	{
		return 'SearchResultItem';
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