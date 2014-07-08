<?php
/**
 * File for class NewyseServiceStructPreference
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructPreference originally named Preference
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructPreference extends NewyseServiceWsdlClass
{
    /**
     * The Id
     * @var long
     */
    public $Id;
    /**
     * The Type
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var string
     */
    public $Type;
    /**
     * Constructor method for Preference
     * @see parent::__construct()
     * @param long $_id
     * @param string $_type
     * @return NewyseServiceStructPreference
     */
    public function __construct($_id = NULL,$_type = NULL)
    {
        parent::__construct(array('Id'=>$_id,'Type'=>$_type),false);
    }
    /**
     * Get Id value
     * @return long|null
     */
    public function getId()
    {
        return $this->Id;
    }
    /**
     * Set Id value
     * @param long $_id the Id
     * @return long
     */
    public function setId($_id)
    {
        return ($this->Id = $_id);
    }
    /**
     * Get Type value
     * @return string|null
     */
    public function getType()
    {
        return $this->Type;
    }
    /**
     * Set Type value
     * @param string $_type the Type
     * @return string
     */
    public function setType($_type)
    {
        return ($this->Type = $_type);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructPreference
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
