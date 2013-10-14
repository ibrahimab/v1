<?php
/**
 * File for class IHomeStructAccommodationDetailInputValue
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructAccommodationDetailInputValue originally named AccommodationDetailInputValue
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructAccommodationDetailInputValue extends IHomeWsdlClass
{
	/**
	 * The AccommodationCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $AccommodationCode;
	/**
	 * The LanguageCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $LanguageCode;
	/**
	 * Constructor method for AccommodationDetailInputValue
	 * @see parent::__construct()
	 * @param string $_accommodationCode
	 * @param string $_languageCode
	 * @return IHomeStructAccommodationDetailInputValue
	 */
	public function __construct($_accommodationCode = NULL,$_languageCode = NULL)
	{
		parent::__construct(array('AccommodationCode'=>$_accommodationCode,'LanguageCode'=>$_languageCode));
	}
	/**
	 * Get AccommodationCode value
	 * @return string|null
	 */
	public function getAccommodationCode()
	{
		return $this->AccommodationCode;
	}
	/**
	 * Set AccommodationCode value
	 * @param string the AccommodationCode
	 * @return string
	 */
	public function setAccommodationCode($_accommodationCode)
	{
		return ($this->AccommodationCode = $_accommodationCode);
	}
	/**
	 * Get LanguageCode value
	 * @return string|null
	 */
	public function getLanguageCode()
	{
		return $this->LanguageCode;
	}
	/**
	 * Set LanguageCode value
	 * @param string the LanguageCode
	 * @return string
	 */
	public function setLanguageCode($_languageCode)
	{
		return ($this->LanguageCode = $_languageCode);
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