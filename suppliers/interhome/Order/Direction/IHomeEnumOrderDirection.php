<?php
/**
 * File for class IHomeEnumOrderDirection
 * @package IHome
 * @subpackage Enumerations
 *
 */
/**
 * This class stands for IHomeEnumOrderDirection originally named OrderDirection
 * @package IHome
 * @subpackage Enumerations
 *
 */
class IHomeEnumOrderDirection extends IHomeWsdlClass
{
	/**
	 * Constant for value 'NotSet'
	 * @return string 'NotSet'
	 */
	const VALUE_NOTSET = 'NotSet';
	/**
	 * Constant for value 'Ascending'
	 * @return string 'Ascending'
	 */
	const VALUE_ASCENDING = 'Ascending';
	/**
	 * Constant for value 'Descending'
	 * @return string 'Descending'
	 */
	const VALUE_DESCENDING = 'Descending';
	/**
	 * Return true if value is allowed
	 * @uses IHomeEnumOrderDirection::VALUE_NOTSET
	 * @uses IHomeEnumOrderDirection::VALUE_ASCENDING
	 * @uses IHomeEnumOrderDirection::VALUE_DESCENDING
	 * @param mixed $_value value
	 * @return bool true|false
	 */
	public static function valueIsValid($_value)
	{
		return in_array($_value,array(IHomeEnumOrderDirection::VALUE_NOTSET,IHomeEnumOrderDirection::VALUE_ASCENDING,IHomeEnumOrderDirection::VALUE_DESCENDING));
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