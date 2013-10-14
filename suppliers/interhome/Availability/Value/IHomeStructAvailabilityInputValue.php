<?php
/**
 * File for class IHomeStructAvailabilityInputValue
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructAvailabilityInputValue originally named AvailabilityInputValue
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructAvailabilityInputValue extends IHomeWsdlClass
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
	 * Constructor method for AvailabilityInputValue
	 * @see parent::__construct()
	 * @param string $_accommodationCode
	 * @param string $_checkIn
	 * @param string $_checkOut
	 * @return IHomeStructAvailabilityInputValue
	 */
	public function __construct($_accommodationCode = NULL,$_checkIn = NULL,$_checkOut = NULL)
	{
		parent::__construct(array('AccommodationCode'=>$_accommodationCode,'CheckIn'=>$_checkIn,'CheckOut'=>$_checkOut));
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
	 * Method returning the class name
	 * @return string __CLASS__
	 */
	public function __toString()
	{
		return __CLASS__;
	}
}
?>