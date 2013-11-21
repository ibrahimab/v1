<?php
/**
 * File for class IHomeStructNearestBookingDateInputValue
 * @package IHome
 * @subpackage Structs
 */
/**
 * This class stands for IHomeStructNearestBookingDateInputValue originally named NearestBookingDateInputValue
 * @package IHome
 * @subpackage Structs
 */
class IHomeStructNearestBookingDateInputValue extends IHomeWsdlClass
{
	/**
	 * The Duration
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Duration;
	/**
	 * The AccomodationCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $AccomodationCode;
	/**
	 * The CheckIn
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $CheckIn;
	/**
	 * Constructor method for NearestBookingDateInputValue
	 * @see parent::__construct()
	 * @param int $_duration
	 * @param string $_accomodationCode
	 * @param string $_checkIn
	 * @return IHomeStructNearestBookingDateInputValue
	 */
	public function __construct($_duration,$_accomodationCode = NULL,$_checkIn = NULL)
	{
		parent::__construct(array('Duration'=>$_duration,'AccomodationCode'=>$_accomodationCode,'CheckIn'=>$_checkIn));
	}
	/**
	 * Get Duration value
	 * @return int
	 */
	public function getDuration()
	{
		return $this->Duration;
	}
	/**
	 * Set Duration value
	 * @param int the Duration
	 * @return int
	 */
	public function setDuration($_duration)
	{
		return ($this->Duration = $_duration);
	}
	/**
	 * Get AccomodationCode value
	 * @return string|null
	 */
	public function getAccomodationCode()
	{
		return $this->AccomodationCode;
	}
	/**
	 * Set AccomodationCode value
	 * @param string the AccomodationCode
	 * @return string
	 */
	public function setAccomodationCode($_accomodationCode)
	{
		return ($this->AccomodationCode = $_accomodationCode);
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
	 * Method returning the class name
	 * @return string __CLASS__
	 */
	public function __toString()
	{
		return __CLASS__;
	}
}
?>