<?php
/**
 * File for class IHomeStructAdditionalServicesReturnValue
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructAdditionalServicesReturnValue originally named AdditionalServicesReturnValue
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructAdditionalServicesReturnValue extends IHomeStructReturnValue
{
	/**
	 * The AdditionalServices
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfAdditionalServiceItem
	 */
	public $AdditionalServices;
	/**
	 * Constructor method for AdditionalServicesReturnValue
	 * @see parent::__construct()
	 * @param IHomeStructArrayOfAdditionalServiceItem $_additionalServices
	 * @return IHomeStructAdditionalServicesReturnValue
	 */
	public function __construct($_additionalServices = NULL)
	{
		IHomeWsdlClass::__construct(array('AdditionalServices'=>($_additionalServices instanceof IHomeStructArrayOfAdditionalServiceItem)?$_additionalServices:new IHomeStructArrayOfAdditionalServiceItem($_additionalServices)));
	}
	/**
	 * Get AdditionalServices value
	 * @return IHomeStructArrayOfAdditionalServiceItem|null
	 */
	public function getAdditionalServices()
	{
		return $this->AdditionalServices;
	}
	/**
	 * Set AdditionalServices value
	 * @param IHomeStructArrayOfAdditionalServiceItem the AdditionalServices
	 * @return IHomeStructArrayOfAdditionalServiceItem
	 */
	public function setAdditionalServices($_additionalServices)
	{
		return ($this->AdditionalServices = $_additionalServices);
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