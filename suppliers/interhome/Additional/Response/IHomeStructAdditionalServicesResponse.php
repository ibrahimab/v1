<?php
/**
 * File for class IHomeStructAdditionalServicesResponse
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructAdditionalServicesResponse originally named AdditionalServicesResponse
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructAdditionalServicesResponse extends IHomeWsdlClass
{
	/**
	 * The AdditionalServicesResult
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructAdditionalServicesReturnValue
	 */
	public $AdditionalServicesResult;
	/**
	 * Constructor method for AdditionalServicesResponse
	 * @see parent::__construct()
	 * @param IHomeStructAdditionalServicesReturnValue $_additionalServicesResult
	 * @return IHomeStructAdditionalServicesResponse
	 */
	public function __construct($_additionalServicesResult = NULL)
	{
		parent::__construct(array('AdditionalServicesResult'=>$_additionalServicesResult));
	}
	/**
	 * Get AdditionalServicesResult value
	 * @return IHomeStructAdditionalServicesReturnValue|null
	 */
	public function getAdditionalServicesResult()
	{
		return $this->AdditionalServicesResult;
	}
	/**
	 * Set AdditionalServicesResult value
	 * @param IHomeStructAdditionalServicesReturnValue the AdditionalServicesResult
	 * @return IHomeStructAdditionalServicesReturnValue
	 */
	public function setAdditionalServicesResult($_additionalServicesResult)
	{
		return ($this->AdditionalServicesResult = $_additionalServicesResult);
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