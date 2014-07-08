<?php
/**
 * File for class NewyseServiceStructActivityCategory
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructActivityCategory originally named ActivityCategory
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructActivityCategory extends NewyseServiceWsdlClass
{
    /**
     * The Name
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Name;
    /**
     * The Code
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Code;
    /**
     * The Language
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Language;
    /**
     * Constructor method for ActivityCategory
     * @see parent::__construct()
     * @param string $_name
     * @param string $_code
     * @param string $_language
     * @return NewyseServiceStructActivityCategory
     */
    public function __construct($_name = NULL,$_code = NULL,$_language = NULL)
    {
        parent::__construct(array('Name'=>$_name,'Code'=>$_code,'Language'=>$_language),false);
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
     * Get Language value
     * @return string|null
     */
    public function getLanguage()
    {
        return $this->Language;
    }
    /**
     * Set Language value
     * @param string $_language the Language
     * @return string
     */
    public function setLanguage($_language)
    {
        return ($this->Language = $_language);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructActivityCategory
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
