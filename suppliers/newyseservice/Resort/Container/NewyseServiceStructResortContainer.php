<?php
/**
 * File for class NewyseServiceStructResortContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructResortContainer originally named ResortContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructResortContainer extends NewyseServiceWsdlClass
{
    /**
     * The Resorts
     * @var NewyseServiceStructResorts
     */
    public $Resorts;
    /**
     * Constructor method for ResortContainer
     * @see parent::__construct()
     * @param NewyseServiceStructResorts $_resorts
     * @return NewyseServiceStructResortContainer
     */
    public function __construct($_resorts = NULL)
    {
        parent::__construct(array('Resorts'=>$_resorts),false);
    }
    /**
     * Get Resorts value
     * @return NewyseServiceStructResorts|null
     */
    public function getResorts()
    {
        return $this->Resorts;
    }
    /**
     * Set Resorts value
     * @param NewyseServiceStructResorts $_resorts the Resorts
     * @return NewyseServiceStructResorts
     */
    public function setResorts($_resorts)
    {
        return ($this->Resorts = $_resorts);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructResortContainer
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
