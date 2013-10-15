<?php
/**
 * File for class IHomeStructPriceListReturnValue
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructPriceListReturnValue originally named PriceListReturnValue
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructPriceListReturnValue extends IHomeStructReturnValue
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
	 * The Items
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfPriceListItem
	 */
	public $Items;
	/**
	 * Constructor method for PriceListReturnValue
	 * @see parent::__construct()
	 * @param string $_accommodationCode
	 * @param string $_currency
	 * @param IHomeStructArrayOfPriceListItem $_items
	 * @return IHomeStructPriceListReturnValue
	 */
	public function __construct($_accommodationCode = NULL,$_currency = NULL,$_items = NULL)
	{
		IHomeWsdlClass::__construct(array('AccommodationCode'=>$_accommodationCode,'Currency'=>$_currency,'Items'=>($_items instanceof IHomeStructArrayOfPriceListItem)?$_items:new IHomeStructArrayOfPriceListItem($_items)));
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
	 * Get Items value
	 * @return IHomeStructArrayOfPriceListItem|null
	 */
	public function getItems()
	{
		return $this->Items;
	}
	/**
	 * Set Items value
	 * @param IHomeStructArrayOfPriceListItem the Items
	 * @return IHomeStructArrayOfPriceListItem
	 */
	public function setItems($_items)
	{
		return ($this->Items = $_items);
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