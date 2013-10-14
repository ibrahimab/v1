<?php
/**
 * File for class IHomeStructSearchReturnValue
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructSearchReturnValue originally named SearchReturnValue
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructSearchReturnValue extends IHomeStructReturnValue
{
	/**
	 * The ResultCount
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $ResultCount;
	/**
	 * The Items
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfSearchResultItem
	 */
	public $Items;
	/**
	 * Constructor method for SearchReturnValue
	 * @see parent::__construct()
	 * @param int $_resultCount
	 * @param IHomeStructArrayOfSearchResultItem $_items
	 * @return IHomeStructSearchReturnValue
	 */
	public function __construct($_resultCount,$_items = NULL)
	{
		IHomeWsdlClass::__construct(array('ResultCount'=>$_resultCount,'Items'=>($_items instanceof IHomeStructArrayOfSearchResultItem)?$_items:new IHomeStructArrayOfSearchResultItem($_items)));
	}
	/**
	 * Get ResultCount value
	 * @return int
	 */
	public function getResultCount()
	{
		return $this->ResultCount;
	}
	/**
	 * Set ResultCount value
	 * @param int the ResultCount
	 * @return int
	 */
	public function setResultCount($_resultCount)
	{
		return ($this->ResultCount = $_resultCount);
	}
	/**
	 * Get Items value
	 * @return IHomeStructArrayOfSearchResultItem|null
	 */
	public function getItems()
	{
		return $this->Items;
	}
	/**
	 * Set Items value
	 * @param IHomeStructArrayOfSearchResultItem the Items
	 * @return IHomeStructArrayOfSearchResultItem
	 */
	public function setItems($_items)
	{
		return ($this->Items = $_items);
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