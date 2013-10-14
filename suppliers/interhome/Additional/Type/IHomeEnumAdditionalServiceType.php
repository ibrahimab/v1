<?php
/**
 * File for class IHomeEnumAdditionalServiceType
 * @package IHome
 * @subpackage Enumerations
 *
 */
/**
 * This class stands for IHomeEnumAdditionalServiceType originally named AdditionalServiceType
 * @package IHome
 * @subpackage Enumerations
 *
 */
class IHomeEnumAdditionalServiceType extends IHomeWsdlClass
{
	/**
	 * Constant for value 'NotSet'
	 * @return string 'NotSet'
	 */
	const VALUE_NOTSET = 'NotSet';
	/**
	 * Constant for value 'CostsOnInvoice'
	 * @return string 'CostsOnInvoice'
	 */
	const VALUE_COSTSONINVOICE = 'CostsOnInvoice';
	/**
	 * Constant for value 'InPriceIncluded'
	 * @return string 'InPriceIncluded'
	 */
	const VALUE_INPRICEINCLUDED = 'InPriceIncluded';
	/**
	 * Constant for value 'ExtracostOnPlace'
	 * @return string 'ExtracostOnPlace'
	 */
	const VALUE_EXTRACOSTONPLACE = 'ExtracostOnPlace';
	/**
	 * Constant for value 'BookableServiceOnInvoice'
	 * @return string 'BookableServiceOnInvoice'
	 */
	const VALUE_BOOKABLESERVICEONINVOICE = 'BookableServiceOnInvoice';
	/**
	 * Constant for value 'BookbaleServiceOnPlace'
	 * @return string 'BookbaleServiceOnPlace'
	 */
	const VALUE_BOOKBALESERVICEONPLACE = 'BookbaleServiceOnPlace';
	/**
	 * Constant for value 'BookableOnPlacePayableOnPlace'
	 * @return string 'BookableOnPlacePayableOnPlace'
	 */
	const VALUE_BOOKABLEONPLACEPAYABLEONPLACE = 'BookableOnPlacePayableOnPlace';
	/**
	 * Constant for value 'SelfOrganised'
	 * @return string 'SelfOrganised'
	 */
	const VALUE_SELFORGANISED = 'SelfOrganised';
	/**
	 * Constant for value 'BookableServiceNoCost'
	 * @return string 'BookableServiceNoCost'
	 */
	const VALUE_BOOKABLESERVICENOCOST = 'BookableServiceNoCost';
	/**
	 * Constant for value 'CommissionDiscount'
	 * @return string 'CommissionDiscount'
	 */
	const VALUE_COMMISSIONDISCOUNT = 'CommissionDiscount';
	/**
	 * Return true if value is allowed
	 * @uses IHomeEnumAdditionalServiceType::VALUE_NOTSET
	 * @uses IHomeEnumAdditionalServiceType::VALUE_COSTSONINVOICE
	 * @uses IHomeEnumAdditionalServiceType::VALUE_INPRICEINCLUDED
	 * @uses IHomeEnumAdditionalServiceType::VALUE_EXTRACOSTONPLACE
	 * @uses IHomeEnumAdditionalServiceType::VALUE_BOOKABLESERVICEONINVOICE
	 * @uses IHomeEnumAdditionalServiceType::VALUE_BOOKBALESERVICEONPLACE
	 * @uses IHomeEnumAdditionalServiceType::VALUE_BOOKABLEONPLACEPAYABLEONPLACE
	 * @uses IHomeEnumAdditionalServiceType::VALUE_SELFORGANISED
	 * @uses IHomeEnumAdditionalServiceType::VALUE_BOOKABLESERVICENOCOST
	 * @uses IHomeEnumAdditionalServiceType::VALUE_COMMISSIONDISCOUNT
	 * @param mixed $_value value
	 * @return bool true|false
	 */
	public static function valueIsValid($_value)
	{
		return in_array($_value,array(IHomeEnumAdditionalServiceType::VALUE_NOTSET,IHomeEnumAdditionalServiceType::VALUE_COSTSONINVOICE,IHomeEnumAdditionalServiceType::VALUE_INPRICEINCLUDED,IHomeEnumAdditionalServiceType::VALUE_EXTRACOSTONPLACE,IHomeEnumAdditionalServiceType::VALUE_BOOKABLESERVICEONINVOICE,IHomeEnumAdditionalServiceType::VALUE_BOOKBALESERVICEONPLACE,IHomeEnumAdditionalServiceType::VALUE_BOOKABLEONPLACEPAYABLEONPLACE,IHomeEnumAdditionalServiceType::VALUE_SELFORGANISED,IHomeEnumAdditionalServiceType::VALUE_BOOKABLESERVICENOCOST,IHomeEnumAdditionalServiceType::VALUE_COMMISSIONDISCOUNT));
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