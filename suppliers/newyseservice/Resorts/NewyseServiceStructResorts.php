<?php
/**
 * File for class NewyseServiceStructResorts
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructResorts originally named Resorts
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructResorts extends NewyseServiceWsdlClass
{
    /**
     * The ResortItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructResort
     */
    public $ResortItem;
    /**
     * The Resort
     * @var NewyseServiceStructResort
     */
    public $Resort;
    /**
     * Constructor method for Resorts
     * @see parent::__construct()
     * @param NewyseServiceStructResort $_resortItem
     * @param NewyseServiceStructResort $_resort
     * @return NewyseServiceStructResorts
     */
    public function __construct($_resortItem = NULL,$_resort = NULL)
    {
        parent::__construct(array('ResortItem'=>$_resortItem,'Resort'=>$_resort),false);
    }
    /**
     * Get ResortItem value
     * @return NewyseServiceStructResort|null
     */
    public function getResortItem()
    {
        return $this->ResortItem;
    }
    /**
     * Set ResortItem value
     * @param NewyseServiceStructResort $_resortItem the ResortItem
     * @return NewyseServiceStructResort
     */
    public function setResortItem($_resortItem)
    {
        return ($this->ResortItem = $_resortItem);
    }
    /**
     * Get Resort value
     * @return NewyseServiceStructResort|null
     */
    public function getResort()
    {
        return $this->Resort;
    }
    /**
     * Set Resort value
     * @param NewyseServiceStructResort $_resort the Resort
     * @return NewyseServiceStructResort
     */
    public function setResort($_resort)
    {
        return ($this->Resort = $_resort);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructResorts
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
