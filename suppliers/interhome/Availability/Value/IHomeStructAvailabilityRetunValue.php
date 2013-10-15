<?php
/**
 * File for class IHomeStructAvailabilityRetunValue
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructAvailabilityRetunValue originally named AvailabilityRetunValue
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructAvailabilityRetunValue extends IHomeStructReturnValue
{
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
	 * The State
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $State;
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
	 * Constructor method for AvailabilityRetunValue
	 * @see parent::__construct()
	 * @param string $_startDate
	 * @param string $_endDate
	 * @param string $_state
	 * @param string $_change
	 * @param string $_minimumStay
	 * @return IHomeStructAvailabilityRetunValue
	 */
	public function __construct($_startDate = NULL,$_endDate = NULL,$_state = NULL,$_change = NULL,$_minimumStay = NULL)
	{
		IHomeWsdlClass::__construct(array('StartDate'=>$_startDate,'EndDate'=>$_endDate,'State'=>$_state,'Change'=>$_change,'MinimumStay'=>$_minimumStay));
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
	 * Method returning the class name
	 * @return string __CLASS__
	 */
	public function __toString()
	{
		return __CLASS__;
	}
}
?>