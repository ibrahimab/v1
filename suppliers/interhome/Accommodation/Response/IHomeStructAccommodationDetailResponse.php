<?php
/**
 * File for class IHomeStructAccommodationDetailResponse
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructAccommodationDetailResponse originally named AccommodationDetailResponse
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructAccommodationDetailResponse extends IHomeWsdlClass
{
	/**
	 * The AccommodationDetailResult
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructAccommodationDetailReturnValue
	 */
	public $AccommodationDetailResult;
	/**
	 * Constructor method for AccommodationDetailResponse
	 * @see parent::__construct()
	 * @param IHomeStructAccommodationDetailReturnValue $_accommodationDetailResult
	 * @return IHomeStructAccommodationDetailResponse
	 */
	public function __construct($_accommodationDetailResult = NULL)
	{
		parent::__construct(array('AccommodationDetailResult'=>$_accommodationDetailResult));
	}
	/**
	 * Get AccommodationDetailResult value
	 * @return IHomeStructAccommodationDetailReturnValue|null
	 */
	public function getAccommodationDetailResult()
	{
		return $this->AccommodationDetailResult;
	}
	/**
	 * Set AccommodationDetailResult value
	 * @param IHomeStructAccommodationDetailReturnValue the AccommodationDetailResult
	 * @return IHomeStructAccommodationDetailReturnValue
	 */
	public function setAccommodationDetailResult($_accommodationDetailResult)
	{
		return ($this->AccommodationDetailResult = $_accommodationDetailResult);
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