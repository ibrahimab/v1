<?php
/**
 * File for class NewyseServiceStructCountryContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructCountryContainer originally named CountryContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructCountryContainer extends NewyseServiceWsdlClass
{
    /**
     * The Countries
     * @var NewyseServiceStructCountries
     */
    public $Countries;
    /**
     * Constructor method for CountryContainer
     * @see parent::__construct()
     * @param NewyseServiceStructCountries $_countries
     * @return NewyseServiceStructCountryContainer
     */
    public function __construct($_countries = NULL)
    {
        parent::__construct(array('Countries'=>$_countries),false);
    }
    /**
     * Get Countries value
     * @return NewyseServiceStructCountries|null
     */
    public function getCountries()
    {
        return $this->Countries;
    }
    /**
     * Set Countries value
     * @param NewyseServiceStructCountries $_countries the Countries
     * @return NewyseServiceStructCountries
     */
    public function setCountries($_countries)
    {
        return ($this->Countries = $_countries);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructCountryContainer
     */
    public static function __set_state(array $_array,$_className = __CLASS__)
    {
        return parent::__set_state($_array,$_className);
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
