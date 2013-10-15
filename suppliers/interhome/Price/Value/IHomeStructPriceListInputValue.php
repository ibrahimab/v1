<?php
/**
 * File for class IHomeStructPriceListInputValue
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructPriceListInputValue originally named PriceListInputValue
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructPriceListInputValue extends IHomeWsdlClass
{
	/**
	 * The AccommodationCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $AccommodationCode;
	/**
	 * The Currency
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Currency;
	/**
	 * The SalesOffice
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $SalesOffice;
	/**
	 * Constructor method for PriceListInputValue
	 * @see parent::__construct()
	 * @param string $_accommodationCode
	 * @param string $_currency
	 * @param string $_salesOffice
	 * @return IHomeStructPriceListInputValue
	 */
	public function __construct($_accommodationCode = NULL,$_currency = NULL,$_salesOffice = NULL)
	{
		parent::__construct(array('AccommodationCode'=>$_accommodationCode,'Currency'=>$_currency,'SalesOffice'=>$_salesOffice));
	}
	/**
	 * Get AccommodationCode value
	 * @return string|null
	 */
	public function getAccommodationCode()
	{
		return $this->AccommodationCode;
	}
	/**
	 * Set AccommodationCode value
	 * @param string the AccommodationCode
	 * @return string
	 */
	public function setAccommodationCode($_accommodationCode)
	{
		return ($this->AccommodationCode = $_accommodationCode);
	}
	/**
	 * Get Currency value
	 * @return string|null
	 */
	public function getCurrency()
	{
		return $this->Currency;
	}
	/**
	 * Set Currency value
	 * @param string the Currency
	 * @return string
	 */
	public function setCurrency($_currency)
	{
		return ($this->Currency = $_currency);
	}
	/**
	 * Get SalesOffice value
	 * @return string|null
	 */
	public function getSalesOffice()
	{
		return $this->SalesOffice;
	}
	/**
	 * Set SalesOffice value
	 * @param string the SalesOffice
	 * @return string
	 */
	public function setSalesOffice($_salesOffice)
	{
		return ($this->SalesOffice = $_salesOffice);
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