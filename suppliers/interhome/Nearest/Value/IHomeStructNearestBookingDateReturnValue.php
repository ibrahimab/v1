<?php
/**
 * File for class IHomeStructNearestBookingDateReturnValue
 * @package IHome
 * @subpackage Structs
 */
/**
 * This class stands for IHomeStructNearestBookingDateReturnValue originally named NearestBookingDateReturnValue
 * @package IHome
 * @subpackage Structs
 */
class IHomeStructNearestBookingDateReturnValue extends IHomeStructReturnValue
{
	/**
	 * The CheckIn
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $CheckIn;
	/**
	 * The Change
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Change;
	/**
	 * The MinimumStay
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $MinimumStay;
	/**
	 * The State
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $State;
	/**
	 * Constructor method for NearestBookingDateReturnValue
	 * @see parent::__construct()
	 * @param string $_checkIn
	 * @param string $_change
	 * @param string $_minimumStay
	 * @param string $_state
	 * @return IHomeStructNearestBookingDateReturnValue
	 */
	public function __construct($_checkIn = NULL,$_change = NULL,$_minimumStay = NULL,$_state = NULL)
	{
		IHomeWsdlClass::__construct(array('CheckIn'=>$_checkIn,'Change'=>$_change,'MinimumStay'=>$_minimumStay,'State'=>$_state));
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
	 * Get Change value
	 * @return string|null
	 */
	public function getChange()
	{
		return $this->Change;
	}
	/**
	 * Set Change value
	 * @param string the Change
	 * @return string
	 */
	public function setChange($_change)
	{
		return ($this->Change = $_change);
	}
	/**
	 * Get MinimumStay value
	 * @return string|null
	 */
	public function getMinimumStay()
	{
		return $this->MinimumStay;
	}
	/**
	 * Set MinimumStay value
	 * @param string the MinimumStay
	 * @return string
	 */
	public function setMinimumStay($_minimumStay)
	{
		return ($this->MinimumStay = $_minimumStay);
	}
	/**
	 * Get State value
	 * @return string|null
	 */
	public function getState()
	{
		return $this->State;
	}
	/**
	 * Set State value
	 * @param string the State
	 * @return string
	 */
	public function setState($_state)
	{
		return ($this->State = $_state);
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