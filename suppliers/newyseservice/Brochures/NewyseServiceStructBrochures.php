<?php
/**
 * File for class NewyseServiceStructBrochures
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructBrochures originally named Brochures
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructBrochures extends NewyseServiceWsdlClass
{
    /**
     * The BrochureItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructBrochure
     */
    public $BrochureItem;
    /**
     * Constructor method for Brochures
     * @see parent::__construct()
     * @param NewyseServiceStructBrochure $_brochureItem
     * @return NewyseServiceStructBrochures
     */
    public function __construct($_brochureItem = NULL)
    {
        parent::__construct(array('BrochureItem'=>$_brochureItem),false);
    }
    /**
     * Get BrochureItem value
     * @return NewyseServiceStructBrochure|null
     */
    public function getBrochureItem()
    {
        return $this->BrochureItem;
    }
    /**
     * Set BrochureItem value
     * @param NewyseServiceStructBrochure $_brochureItem the BrochureItem
     * @return NewyseServiceStructBrochure
     */
    public function setBrochureItem($_brochureItem)
    {
        return ($this->BrochureItem = $_brochureItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructBrochures
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
