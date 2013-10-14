<?php
/**
 * File for class IHomeStructPriceListItem
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructPriceListItem originally named PriceListItem
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructPriceListItem extends IHomeWsdlClass
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
	 * The ShortbreakDays
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $ShortbreakDays;
	/**
	 * The ShortbreakPrice
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var decimal
	 */
	public $ShortbreakPrice;
	/**
	 * The StartDate
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $StartDate;
	/**
	 * The EndDate
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $EndDate;
	/**
	 * Constructor method for PriceListItem
	 * @see parent::__construct()
	 * @param decimal $_price
	 * @param int $_shortbreakDays
	 * @param decimal $_shortbreakPrice
	 * @param string $_startDate
	 * @param string $_endDate
	 * @return IHomeStructPriceListItem
	 */
	public function __construct($_price,$_shortbreakDays,$_shortbreakPrice,$_startDate = NULL,$_endDate = NULL)
	{
		parent::__construct(array('Price'=>$_price,'ShortbreakDays'=>$_shortbreakDays,'ShortbreakPrice'=>$_shortbreakPrice,'StartDate'=>$_startDate,'EndDate'=>$_endDate));
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
	 * Get ShortbreakDays value
	 * @return int
	 */
	public function getShortbreakDays()
	{
		return $this->ShortbreakDays;
	}
	/**
	 * Set ShortbreakDays value
	 * @param int the ShortbreakDays
	 * @return int
	 */
	public function setShortbreakDays($_shortbreakDays)
	{
		return ($this->ShortbreakDays = $_shortbreakDays);
	}
	/**
	 * Get ShortbreakPrice value
	 * @return decimal
	 */
	public function getShortbreakPrice()
	{
		return $this->ShortbreakPrice;
	}
	/**
	 * Set ShortbreakPrice value
	 * @param decimal the ShortbreakPrice
	 * @return decimal
	 */
	public function setShortbreakPrice($_shortbreakPrice)
	{
		return ($this->ShortbreakPrice = $_shortbreakPrice);
	}
	/**
	 * Get StartDate value
	 * @return string|null
	 */
	public function getStartDate()
	{
		return $this->StartDate;
	}
	/**
	 * Set StartDate value
	 * @param string the StartDate
	 * @return string
	 */
	public function setStartDate($_startDate)
	{
		return ($this->StartDate = $_startDate);
	}
	/**
	 * Get EndDate value
	 * @return string|null
	 */
	public function getEndDate()
	{
		return $this->EndDate;
	}
	/**
	 * Set EndDate value
	 * @param string the EndDate
	 * @return string
	 */
	public function setEndDate($_endDate)
	{
		return ($this->EndDate = $_endDate);
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