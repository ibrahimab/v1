<?php
/**
 * File for class NewyseServiceStructCustomerTitleContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructCustomerTitleContainer originally named CustomerTitleContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructCustomerTitleContainer extends NewyseServiceWsdlClass
{
    /**
     * The CustomerTitles
     * @var NewyseServiceStructCustomerTitles
     */
    public $CustomerTitles;
    /**
     * Constructor method for CustomerTitleContainer
     * @see parent::__construct()
     * @param NewyseServiceStructCustomerTitles $_customerTitles
     * @return NewyseServiceStructCustomerTitleContainer
     */
    public function __construct($_customerTitles = NULL)
    {
        parent::__construct(array('CustomerTitles'=>$_customerTitles),false);
    }
    /**
     * Get CustomerTitles value
     * @return NewyseServiceStructCustomerTitles|null
     */
    public function getCustomerTitles()
    {
        return $this->CustomerTitles;
    }
    /**
     * Set CustomerTitles value
     * @param NewyseServiceStructCustomerTitles $_customerTitles the CustomerTitles
     * @return NewyseServiceStructCustomerTitles
     */
    public function setCustomerTitles($_customerTitles)
    {
        return ($this->CustomerTitles = $_customerTitles);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructCustomerTitleContainer
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
