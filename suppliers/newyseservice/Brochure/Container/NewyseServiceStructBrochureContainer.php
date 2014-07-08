<?php
/**
 * File for class NewyseServiceStructBrochureContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructBrochureContainer originally named BrochureContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructBrochureContainer extends NewyseServiceWsdlClass
{
    /**
     * The Brochures
     * @var NewyseServiceStructBrochures
     */
    public $Brochures;
    /**
     * Constructor method for BrochureContainer
     * @see parent::__construct()
     * @param NewyseServiceStructBrochures $_brochures
     * @return NewyseServiceStructBrochureContainer
     */
    public function __construct($_brochures = NULL)
    {
        parent::__construct(array('Brochures'=>$_brochures),false);
    }
    /**
     * Get Brochures value
     * @return NewyseServiceStructBrochures|null
     */
    public function getBrochures()
    {
        return $this->Brochures;
    }
    /**
     * Set Brochures value
     * @param NewyseServiceStructBrochures $_brochures the Brochures
     * @return NewyseServiceStructBrochures
     */
    public function setBrochures($_brochures)
    {
        return ($this->Brochures = $_brochures);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructBrochureContainer
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
