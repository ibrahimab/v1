<?php
/**
 * File for class IHomeStructPricesInputValue
 * @package IHome
 * @subpackage Structs
 */
/**
 * This class stands for IHomeStructPricesInputValue originally named PricesInputValue
 * @package IHome
 * @subpackage Structs
 */
class IHomeStructPricesInputValue extends IHomeWsdlClass
{
	/**
	 * The SalesOfficeCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $SalesOfficeCode;
	/**
	 * The CurrencyCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $CurrencyCode;
	/**
	 * The LanguageCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $LanguageCode;
	/**
	 * The Stays
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfStayItem
	 */
	public $Stays;
	/**
	 * Constructor method for PricesInputValue
	 * @see parent::__construct()
	 * @param string $_salesOfficeCode
	 * @param string $_currencyCode
	 * @param string $_languageCode
	 * @param IHomeStructArrayOfStayItem $_stays
	 * @return IHomeStructPricesInputValue
	 */
	public function __construct($_salesOfficeCode = NULL,$_currencyCode = NULL,$_languageCode = NULL,$_stays = NULL)
	{
		parent::__construct(array('SalesOfficeCode'=>$_salesOfficeCode,'CurrencyCode'=>$_currencyCode,'LanguageCode'=>$_languageCode,'Stays'=>($_stays instanceof IHomeStructArrayOfStayItem)?$_stays:new IHomeStructArrayOfStayItem($_stays)));
	}
	/**
	 * Get SalesOfficeCode value
	 * @return string|null
	 */
	public function getSalesOfficeCode()
	{
		return $this->SalesOfficeCode;
	}
	/**
	 * Set SalesOfficeCode value
	 * @param string the SalesOfficeCode
	 * @return string
	 */
	public function setSalesOfficeCode($_salesOfficeCode)
	{
		return ($this->SalesOfficeCode = $_salesOfficeCode);
	}
	/**
	 * Get CurrencyCode value
	 * @return string|null
	 */
	public function getCurrencyCode()
	{
		return $this->CurrencyCode;
	}
	/**
	 * Set CurrencyCode value
	 * @param string the CurrencyCode
	 * @return string
	 */
	public function setCurrencyCode($_currencyCode)
	{
		return ($this->CurrencyCode = $_currencyCode);
	}
	/**
	 * Get LanguageCode value
	 * @return string|null
	 */
	public function getLanguageCode()
	{
		return $this->LanguageCode;
	}
	/**
	 * Set LanguageCode value
	 * @param string the LanguageCode
	 * @return string
	 */
	public function setLanguageCode($_languageCode)
	{
		return ($this->LanguageCode = $_languageCode);
	}
	/**
	 * Get Stays value
	 * @return IHomeStructArrayOfStayItem|null
	 */
	public function getStays()
	{
		return $this->Stays;
	}
	/**
	 * Set Stays value
	 * @param IHomeStructArrayOfStayItem the Stays
	 * @return IHomeStructArrayOfStayItem
	 */
	public function setStays($_stays)
	{
		return ($this->Stays = $_stays);
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