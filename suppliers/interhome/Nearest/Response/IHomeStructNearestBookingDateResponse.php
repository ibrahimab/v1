<?php
/**
 * File for class IHomeStructNearestBookingDateResponse
 * @package IHome
 * @subpackage Structs
 */
/**
 * This class stands for IHomeStructNearestBookingDateResponse originally named NearestBookingDateResponse
 * @package IHome
 * @subpackage Structs
 */
class IHomeStructNearestBookingDateResponse extends IHomeWsdlClass
{
	/**
	 * The NearestBookingDateResult
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructNearestBookingDateReturnValue
	 */
	public $NearestBookingDateResult;
	/**
	 * Constructor method for NearestBookingDateResponse
	 * @see parent::__construct()
	 * @param IHomeStructNearestBookingDateReturnValue $_nearestBookingDateResult
	 * @return IHomeStructNearestBookingDateResponse
	 */
	public function __construct($_nearestBookingDateResult = NULL)
	{
		parent::__construct(array('NearestBookingDateResult'=>$_nearestBookingDateResult));
	}
	/**
	 * Get NearestBookingDateResult value
	 * @return IHomeStructNearestBookingDateReturnValue|null
	 */
	public function getNearestBookingDateResult()
	{
		return $this->NearestBookingDateResult;
	}
	/**
	 * Set NearestBookingDateResult value
	 * @param IHomeStructNearestBookingDateReturnValue the NearestBookingDateResult
	 * @return IHomeStructNearestBookingDateReturnValue
	 */
	public function setNearestBookingDateResult($_nearestBookingDateResult)
	{
		return ($this->NearestBookingDateResult = $_nearestBookingDateResult);
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