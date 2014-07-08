<?php
/**
 * File for class NewyseServiceStructCountries
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructCountries originally named Countries
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructCountries extends NewyseServiceWsdlClass
{
    /**
     * The CountryItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructCountry
     */
    public $CountryItem;
    /**
     * Constructor method for Countries
     * @see parent::__construct()
     * @param NewyseServiceStructCountry $_countryItem
     * @return NewyseServiceStructCountries
     */
    public function __construct($_countryItem = NULL)
    {
        parent::__construct(array('CountryItem'=>$_countryItem),false);
    }
    /**
     * Get CountryItem value
     * @return NewyseServiceStructCountry|null
     */
    public function getCountryItem()
    {
        return $this->CountryItem;
    }
    /**
     * Set CountryItem value
     * @param NewyseServiceStructCountry $_countryItem the CountryItem
     * @return NewyseServiceStructCountry
     */
    public function setCountryItem($_countryItem)
    {
        return ($this->CountryItem = $_countryItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructCountries
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
