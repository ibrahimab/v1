<?php
/**
 * File for class IHomeStructPricesPriceItem
 * @package IHome
 * @subpackage Structs
 */
/**
 * This class stands for IHomeStructPricesPriceItem originally named PricesPriceItem
 * @package IHome
 * @subpackage Structs
 */
class IHomeStructPricesPriceItem extends IHomeWsdlClass
{
	/**
	 * The Price1
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var decimal
	 */
	public $Price1;
	/**
	 * The Price2
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var decimal
	 */
	public $Price2;
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
	 * The CurrencyCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $CurrencyCode;
	/**
	 * The AccommodationCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $AccommodationCode;
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
	 * Constructor method for PricesPriceItem
	 * @see parent::__construct()
	 * @param decimal $_price1
	 * @param decimal $_price2
	 * @param string $_checkIn
	 * @param string $_checkOut
	 * @param string $_currencyCode
	 * @param string $_accommodationCode
	 * @param string $_specialCode
	 * @param string $_specialDescription
	 * @return IHomeStructPricesPriceItem
	 */
	public function __construct($_price1,$_price2,$_checkIn = NULL,$_checkOut = NULL,$_currencyCode = NULL,$_accommodationCode = NULL,$_specialCode = NULL,$_specialDescription = NULL)
	{
		parent::__construct(array('Price1'=>$_price1,'Price2'=>$_price2,'CheckIn'=>$_checkIn,'CheckOut'=>$_checkOut,'CurrencyCode'=>$_currencyCode,'AccommodationCode'=>$_accommodationCode,'SpecialCode'=>$_specialCode,'SpecialDescription'=>$_specialDescription));
	}
	/**
	 * Get Price1 value
	 * @return decimal
	 */
	public function getPrice1()
	{
		return $this->Price1;
	}
	/**
	 * Set Price1 value
	 * @param decimal the Price1
	 * @return decimal
	 */
	public function setPrice1($_price1)
	{
		return ($this->Price1 = $_price1);
	}
	/**
	 * Get Price2 value
	 * @return decimal
	 */
	public function getPrice2()
	{
		return $this->Price2;
	}
	/**
	 * Set Price2 value
	 * @param decimal the Price2
	 * @return decimal
	 */
	public function setPrice2($_price2)
	{
		return ($this->Price2 = $_price2);
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