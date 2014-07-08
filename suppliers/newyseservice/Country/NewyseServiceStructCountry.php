<?php
/**
 * File for class NewyseServiceStructCountry
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructCountry originally named Country
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructCountry extends NewyseServiceWsdlClass
{
    /**
     * The CountryId
     * @var long
     */
    public $CountryId;
    /**
     * The Code
     * @var string
     */
    public $Code;
    /**
     * The Name
     * @var string
     */
    public $Name;
    /**
     * Constructor method for Country
     * @see parent::__construct()
     * @param long $_countryId
     * @param string $_code
     * @param string $_name
     * @return NewyseServiceStructCountry
     */
    public function __construct($_countryId = NULL,$_code = NULL,$_name = NULL)
    {
        parent::__construct(array('CountryId'=>$_countryId,'Code'=>$_code,'Name'=>$_name),false);
    }
    /**
     * Get CountryId value
     * @return long|null
     */
    public function getCountryId()
    {
        return $this->CountryId;
    }
    /**
     * Set CountryId value
     * @param long $_countryId the CountryId
     * @return long
     */
    public function setCountryId($_countryId)
    {
        return ($this->CountryId = $_countryId);
    }
    /**
     * Get Code value
     * @return string|null
     */
    public function getCode()
    {
        return $this->Code;
    }
    /**
     * Set Code value
     * @param string $_code the Code
     * @return string
     */
    public function setCode($_code)
    {
        return ($this->Code = $_code);
    }
    /**
     * Get Name value
     * @return string|null
     */
    public function getName()
    {
        return $this->Name;
    }
    /**
     * Set Name value
     * @param string $_name the Name
     * @return string
     */
    public function setName($_name)
    {
        return ($this->Name = $_name);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructCountry
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
