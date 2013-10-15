<?php
/**
 * File for class IHomeStructAvailabilityResponse
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructAvailabilityResponse originally named AvailabilityResponse
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructAvailabilityResponse extends IHomeWsdlClass
{
	/**
	 * The AvailabilityResult
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructAvailabilityRetunValue
	 */
	public $AvailabilityResult;
	/**
	 * Constructor method for AvailabilityResponse
	 * @see parent::__construct()
	 * @param IHomeStructAvailabilityRetunValue $_availabilityResult
	 * @return IHomeStructAvailabilityResponse
	 */
	public function __construct($_availabilityResult = NULL)
	{
		parent::__construct(array('AvailabilityResult'=>$_availabilityResult));
	}
	/**
	 * Get AvailabilityResult value
	 * @return IHomeStructAvailabilityRetunValue|null
	 */
	public function getAvailabilityResult()
	{
		return $this->AvailabilityResult;
	}
	/**
	 * Set AvailabilityResult value
	 * @param IHomeStructAvailabilityRetunValue the AvailabilityResult
	 * @return IHomeStructAvailabilityRetunValue
	 */
	public function setAvailabilityResult($_availabilityResult)
	{
		return ($this->AvailabilityResult = $_availabilityResult);
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