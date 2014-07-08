<?php
/**
 * File for class NewyseServiceStructPreferences
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructPreferences originally named Preferences
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructPreferences extends NewyseServiceWsdlClass
{
    /**
     * The PreferenceItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructPreference
     */
    public $PreferenceItem;
    /**
     * Constructor method for Preferences
     * @see parent::__construct()
     * @param NewyseServiceStructPreference $_preferenceItem
     * @return NewyseServiceStructPreferences
     */
    public function __construct($_preferenceItem = NULL)
    {
        parent::__construct(array('PreferenceItem'=>$_preferenceItem),false);
    }
    /**
     * Get PreferenceItem value
     * @return NewyseServiceStructPreference|null
     */
    public function getPreferenceItem()
    {
        return $this->PreferenceItem;
    }
    /**
     * Set PreferenceItem value
     * @param NewyseServiceStructPreference $_preferenceItem the PreferenceItem
     * @return NewyseServiceStructPreference
     */
    public function setPreferenceItem($_preferenceItem)
    {
        return ($this->PreferenceItem = $_preferenceItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructPreferences
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
