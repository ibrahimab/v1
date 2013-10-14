<?php
/**
 * File for class IHomeEnumOrderBy
 * @package IHome
 * @subpackage Enumerations
 *
 */
/**
 * This class stands for IHomeEnumOrderBy originally named OrderBy
 * @package IHome
 * @subpackage Enumerations
 *
 */
class IHomeEnumOrderBy extends IHomeWsdlClass
{
	/**
	 * Constant for value 'NotSet'
	 * @return string 'NotSet'
	 */
	const VALUE_NOTSET = 'NotSet';
	/**
	 * Constant for value 'Favorite'
	 * @return string 'Favorite'
	 */
	const VALUE_FAVORITE = 'Favorite';
	/**
	 * Constant for value 'Price'
	 * @return string 'Price'
	 */
	const VALUE_PRICE = 'Price';
	/**
	 * Constant for value 'Place'
	 * @return string 'Place'
	 */
	const VALUE_PLACE = 'Place';
	/**
	 * Return true if value is allowed
	 * @uses IHomeEnumOrderBy::VALUE_NOTSET
	 * @uses IHomeEnumOrderBy::VALUE_FAVORITE
	 * @uses IHomeEnumOrderBy::VALUE_PRICE
	 * @uses IHomeEnumOrderBy::VALUE_PLACE
	 * @param mixed $_value value
	 * @return bool true|false
	 */
	public static function valueIsValid($_value)
	{
		return in_array($_value,array(IHomeEnumOrderBy::VALUE_NOTSET,IHomeEnumOrderBy::VALUE_FAVORITE,IHomeEnumOrderBy::VALUE_PRICE,IHomeEnumOrderBy::VALUE_PLACE));
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