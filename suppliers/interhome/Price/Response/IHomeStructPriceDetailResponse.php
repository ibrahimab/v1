<?php
/**
 * File for class IHomeStructPriceDetailResponse
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructPriceDetailResponse originally named PriceDetailResponse
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructPriceDetailResponse extends IHomeWsdlClass
{
	/**
	 * The PriceDetailResult
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructPriceDetailRetunValue
	 */
	public $PriceDetailResult;
	/**
	 * Constructor method for PriceDetailResponse
	 * @see parent::__construct()
	 * @param IHomeStructPriceDetailRetunValue $_priceDetailResult
	 * @return IHomeStructPriceDetailResponse
	 */
	public function __construct($_priceDetailResult = NULL)
	{
		parent::__construct(array('PriceDetailResult'=>$_priceDetailResult));
	}
	/**
	 * Get PriceDetailResult value
	 * @return IHomeStructPriceDetailRetunValue|null
	 */
	public function getPriceDetailResult()
	{
		return $this->PriceDetailResult;
	}
	/**
	 * Set PriceDetailResult value
	 * @param IHomeStructPriceDetailRetunValue the PriceDetailResult
	 * @return IHomeStructPriceDetailRetunValue
	 */
	public function setPriceDetailResult($_priceDetailResult)
	{
		return ($this->PriceDetailResult = $_priceDetailResult);
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