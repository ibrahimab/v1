<?php
/**
 * File for class IHomeStructSearchResponse
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructSearchResponse originally named SearchResponse
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructSearchResponse extends IHomeWsdlClass
{
	/**
	 * The SearchResult
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructSearchReturnValue
	 */
	public $SearchResult;
	/**
	 * Constructor method for SearchResponse
	 * @see parent::__construct()
	 * @param IHomeStructSearchReturnValue $_searchResult
	 * @return IHomeStructSearchResponse
	 */
	public function __construct($_searchResult = NULL)
	{
		parent::__construct(array('SearchResult'=>$_searchResult));
	}
	/**
	 * Get SearchResult value
	 * @return IHomeStructSearchReturnValue|null
	 */
	public function getSearchResult()
	{
		return $this->SearchResult;
	}
	/**
	 * Set SearchResult value
	 * @param IHomeStructSearchReturnValue the SearchResult
	 * @return IHomeStructSearchReturnValue
	 */
	public function setSearchResult($_searchResult)
	{
		return ($this->SearchResult = $_searchResult);
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