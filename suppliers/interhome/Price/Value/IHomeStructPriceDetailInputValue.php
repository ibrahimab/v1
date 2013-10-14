<?php
/**
 * File for class IHomeStructPriceDetailInputValue
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructPriceDetailInputValue originally named PriceDetailInputValue
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructPriceDetailInputValue extends IHomeWsdlClass
{
	/**
	 * The Adults
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Adults;
	/**
	 * The Children
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Children;
	/**
	 * The Babies
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Babies;
	/**
	 * The AccommodationCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $AccommodationCode;
	/**
	 * The AdditionalServices
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfAdditionalServiceInputItem
	 */
	public $AdditionalServices;
	/**
	 * The CheckIn
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $CheckIn;
	/**
	 * The CheckOut
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $CheckOut;
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
	 * The RetailerCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $RetailerCode;
	/**
	 * Constructor method for PriceDetailInputValue
	 * @see parent::__construct()
	 * @param int $_adults
	 * @param int $_children
	 * @param int $_babies
	 * @param string $_accommodationCode
	 * @param IHomeStructArrayOfAdditionalServiceInputItem $_additionalServices
	 * @param string $_checkIn
	 * @param string $_checkOut
	 * @param string $_salesOfficeCode
	 * @param string $_currencyCode
	 * @param string $_languageCode
	 * @param string $_retailerCode
	 * @return IHomeStructPriceDetailInputValue
	 */
	public function __construct($_adults,$_children,$_babies,$_accommodationCode = NULL,$_additionalServices = NULL,$_checkIn = NULL,$_checkOut = NULL,$_salesOfficeCode = NULL,$_currencyCode = NULL,$_languageCode = NULL,$_retailerCode = NULL)
	{
		parent::__construct(array('Adults'=>$_adults,'Children'=>$_children,'Babies'=>$_babies,'AccommodationCode'=>$_accommodationCode,'AdditionalServices'=>($_additionalServices instanceof IHomeStructArrayOfAdditionalServiceInputItem)?$_additionalServices:new IHomeStructArrayOfAdditionalServiceInputItem($_additionalServices),'CheckIn'=>$_checkIn,'CheckOut'=>$_checkOut,'SalesOfficeCode'=>$_salesOfficeCode,'CurrencyCode'=>$_currencyCode,'LanguageCode'=>$_languageCode,'RetailerCode'=>$_retailerCode));
	}
	/**
	 * Get Adults value
	 * @return int
	 */
	public function getAdults()
	{
		return $this->Adults;
	}
	/**
	 * Set Adults value
	 * @param int the Adults
	 * @return int
	 */
	public function setAdults($_adults)
	{
		return ($this->Adults = $_adults);
	}
	/**
	 * Get Children value
	 * @return int
	 */
	public function getChildren()
	{
		return $this->Children;
	}
	/**
	 * Set Children value
	 * @param int the Children
	 * @return int
	 */
	public function setChildren($_children)
	{
		return ($this->Children = $_children);
	}
	/**
	 * Get Babies value
	 * @return int
	 */
	public function getBabies()
	{
		return $this->Babies;
	}
	/**
	 * Set Babies value
	 * @param int the Babies
	 * @return int
	 */
	public function setBabies($_babies)
	{
		return ($this->Babies = $_babies);
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
	 * Get AdditionalServices value
	 * @return IHomeStructArrayOfAdditionalServiceInputItem|null
	 */
	public function getAdditionalServices()
	{
		return $this->AdditionalServices;
	}
	/**
	 * Set AdditionalServices value
	 * @param IHomeStructArrayOfAdditionalServiceInputItem the AdditionalServices
	 * @return IHomeStructArrayOfAdditionalServiceInputItem
	 */
	public function setAdditionalServices($_additionalServices)
	{
		return ($this->AdditionalServices = $_additionalServices);
	}
	/**
	 * Get CheckIn value
	 * @return string|null
	 */
	public function getCheckIn()
	{
		return $this->CheckIn;
	}
	/**
	 * Set CheckIn value
	 * @param string the CheckIn
	 * @return string
	 */
	public function setCheckIn($_checkIn)
	{
		return ($this->CheckIn = $_checkIn);
	}
	/**
	 * Get CheckOut value
	 * @return string|null
	 */
	public function getCheckOut()
	{
		return $this->CheckOut;
	}
	/**
	 * Set CheckOut value
	 * @param string the CheckOut
	 * @return string
	 */
	public function setCheckOut($_checkOut)
	{
		return ($this->CheckOut = $_checkOut);
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
	 * Get RetailerCode value
	 * @return string|null
	 */
	public function getRetailerCode()
	{
		return $this->RetailerCode;
	}
	/**
	 * Set RetailerCode value
	 * @param string the RetailerCode
	 * @return string
	 */
	public function setRetailerCode($_retailerCode)
	{
		return ($this->RetailerCode = $_retailerCode);
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