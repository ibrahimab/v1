<?php
/**
 * File for class IHomeStructPriceDetailRetunValue
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructPriceDetailRetunValue originally named PriceDetailRetunValue
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructPriceDetailRetunValue extends IHomeStructReturnValue
{
	/**
	 * The Price
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var decimal
	 */
	public $Price;
	/**
	 * The Total
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var decimal
	 */
	public $Total;
	/**
	 * The Prepayment
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var decimal
	 */
	public $Prepayment;
	/**
	 * The SpecialPrice
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var decimal
	 */
	public $SpecialPrice;
	/**
	 * The CurrencyCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $CurrencyCode;
	/**
	 * The AdditionalServices
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfAdditionalServiceItem
	 */
	public $AdditionalServices;
	/**
	 * The ExpirationPrePayment
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $ExpirationPrePayment;
	/**
	 * The ExpirationResidue
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $ExpirationResidue;
	/**
	 * The SpecialCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $SpecialCode;
	/**
	 * The SpecialDescription
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $SpecialDescription;
	/**
	 * Constructor method for PriceDetailRetunValue
	 * @see parent::__construct()
	 * @param decimal $_price
	 * @param decimal $_total
	 * @param decimal $_prepayment
	 * @param decimal $_specialPrice
	 * @param string $_currencyCode
	 * @param IHomeStructArrayOfAdditionalServiceItem $_additionalServices
	 * @param string $_expirationPrePayment
	 * @param string $_expirationResidue
	 * @param string $_specialCode
	 * @param string $_specialDescription
	 * @return IHomeStructPriceDetailRetunValue
	 */
	public function __construct($_price,$_total,$_prepayment,$_specialPrice,$_currencyCode = NULL,$_additionalServices = NULL,$_expirationPrePayment = NULL,$_expirationResidue = NULL,$_specialCode = NULL,$_specialDescription = NULL)
	{
		IHomeWsdlClass::__construct(array('Price'=>$_price,'Total'=>$_total,'Prepayment'=>$_prepayment,'SpecialPrice'=>$_specialPrice,'CurrencyCode'=>$_currencyCode,'AdditionalServices'=>($_additionalServices instanceof IHomeStructArrayOfAdditionalServiceItem)?$_additionalServices:new IHomeStructArrayOfAdditionalServiceItem($_additionalServices),'ExpirationPrePayment'=>$_expirationPrePayment,'ExpirationResidue'=>$_expirationResidue,'SpecialCode'=>$_specialCode,'SpecialDescription'=>$_specialDescription));
	}
	/**
	 * Get Price value
	 * @return decimal
	 */
	public function getPrice()
	{
		return $this->Price;
	}
	/**
	 * Set Price value
	 * @param decimal the Price
	 * @return decimal
	 */
	public function setPrice($_price)
	{
		return ($this->Price = $_price);
	}
	/**
	 * Get Total value
	 * @return decimal
	 */
	public function getTotal()
	{
		return $this->Total;
	}
	/**
	 * Set Total value
	 * @param decimal the Total
	 * @return decimal
	 */
	public function setTotal($_total)
	{
		return ($this->Total = $_total);
	}
	/**
	 * Get Prepayment value
	 * @return decimal
	 */
	public function getPrepayment()
	{
		return $this->Prepayment;
	}
	/**
	 * Set Prepayment value
	 * @param decimal the Prepayment
	 * @return decimal
	 */
	public function setPrepayment($_prepayment)
	{
		return ($this->Prepayment = $_prepayment);
	}
	/**
	 * Get SpecialPrice value
	 * @return decimal
	 */
	public function getSpecialPrice()
	{
		return $this->SpecialPrice;
	}
	/**
	 * Set SpecialPrice value
	 * @param decimal the SpecialPrice
	 * @return decimal
	 */
	public function setSpecialPrice($_specialPrice)
	{
		return ($this->SpecialPrice = $_specialPrice);
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
	 * Get AdditionalServices value
	 * @return IHomeStructArrayOfAdditionalServiceItem|null
	 */
	public function getAdditionalServices()
	{
		return $this->AdditionalServices;
	}
	/**
	 * Set AdditionalServices value
	 * @param IHomeStructArrayOfAdditionalServiceItem the AdditionalServices
	 * @return IHomeStructArrayOfAdditionalServiceItem
	 */
	public function setAdditionalServices($_additionalServices)
	{
		return ($this->AdditionalServices = $_additionalServices);
	}
	/**
	 * Get ExpirationPrePayment value
	 * @return string|null
	 */
	public function getExpirationPrePayment()
	{
		return $this->ExpirationPrePayment;
	}
	/**
	 * Set ExpirationPrePayment value
	 * @param string the ExpirationPrePayment
	 * @return string
	 */
	public function setExpirationPrePayment($_expirationPrePayment)
	{
		return ($this->ExpirationPrePayment = $_expirationPrePayment);
	}
	/**
	 * Get ExpirationResidue value
	 * @return string|null
	 */
	public function getExpirationResidue()
	{
		return $this->ExpirationResidue;
	}
	/**
	 * Set ExpirationResidue value
	 * @param string the ExpirationResidue
	 * @return string
	 */
	public function setExpirationResidue($_expirationResidue)
	{
		return ($this->ExpirationResidue = $_expirationResidue);
	}
	/**
	 * Get SpecialCode value
	 * @return string|null
	 */
	public function getSpecialCode()
	{
		return $this->SpecialCode;
	}
	/**
	 * Set SpecialCode value
	 * @param string the SpecialCode
	 * @return string
	 */
	public function setSpecialCode($_specialCode)
	{
		return ($this->SpecialCode = $_specialCode);
	}
	/**
	 * Get SpecialDescription value
	 * @return string|null
	 */
	public function getSpecialDescription()
	{
		return $this->SpecialDescription;
	}
	/**
	 * Set SpecialDescription value
	 * @param string the SpecialDescription
	 * @return string
	 */
	public function setSpecialDescription($_specialDescription)
	{
		return ($this->SpecialDescription = $_specialDescription);
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