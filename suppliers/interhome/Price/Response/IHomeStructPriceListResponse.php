<?php
/**
 * File for class IHomeStructPriceListResponse
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructPriceListResponse originally named PriceListResponse
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructPriceListResponse extends IHomeWsdlClass
{
	/**
	 * The PriceListResult
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructPriceListReturnValue
	 */
	public $PriceListResult;
	/**
	 * Constructor method for PriceListResponse
	 * @see parent::__construct()
	 * @param IHomeStructPriceListReturnValue $_priceListResult
	 * @return IHomeStructPriceListResponse
	 */
	public function __construct($_priceListResult = NULL)
	{
		parent::__construct(array('PriceListResult'=>$_priceListResult));
	}
	/**
	 * Get PriceListResult value
	 * @return IHomeStructPriceListReturnValue|null
	 */
	public function getPriceListResult()
	{
		return $this->PriceListResult;
	}
	/**
	 * Set PriceListResult value
	 * @param IHomeStructPriceListReturnValue the PriceListResult
	 * @return IHomeStructPriceListReturnValue
	 */
	public function setPriceListResult($_priceListResult)
	{
		return ($this->PriceListResult = $_priceListResult);
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