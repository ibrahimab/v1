<?php
/**
 * File for class IHomeStructPricesResponse
 * @package IHome
 * @subpackage Structs
 */
/**
 * This class stands for IHomeStructPricesResponse originally named PricesResponse
 * @package IHome
 * @subpackage Structs
 */
class IHomeStructPricesResponse extends IHomeWsdlClass
{
	/**
	 * The PricesResult
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructPricesRetunValue
	 */
	public $PricesResult;
	/**
	 * Constructor method for PricesResponse
	 * @see parent::__construct()
	 * @param IHomeStructPricesRetunValue $_pricesResult
	 * @return IHomeStructPricesResponse
	 */
	public function __construct($_pricesResult = NULL)
	{
		parent::__construct(array('PricesResult'=>$_pricesResult));
	}
	/**
	 * Get PricesResult value
	 * @return IHomeStructPricesRetunValue|null
	 */
	public function getPricesResult()
	{
		return $this->PricesResult;
	}
	/**
	 * Set PricesResult value
	 * @param IHomeStructPricesRetunValue the PricesResult
	 * @return IHomeStructPricesRetunValue
	 */
	public function setPricesResult($_pricesResult)
	{
		return ($this->PricesResult = $_pricesResult);
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