<?php
/**
 * File for class NewyseServiceStructOpeningTimes
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructOpeningTimes originally named OpeningTimes
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructOpeningTimes extends NewyseServiceWsdlClass
{
    /**
     * The OpeningTimeItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructOpeningTime
     */
    public $OpeningTimeItem;
    /**
     * Constructor method for OpeningTimes
     * @see parent::__construct()
     * @param NewyseServiceStructOpeningTime $_openingTimeItem
     * @return NewyseServiceStructOpeningTimes
     */
    public function __construct($_openingTimeItem = NULL)
    {
        parent::__construct(array('OpeningTimeItem'=>$_openingTimeItem),false);
    }
    /**
     * Get OpeningTimeItem value
     * @return NewyseServiceStructOpeningTime|null
     */
    public function getOpeningTimeItem()
    {
        return $this->OpeningTimeItem;
    }
    /**
     * Set OpeningTimeItem value
     * @param NewyseServiceStructOpeningTime $_openingTimeItem the OpeningTimeItem
     * @return NewyseServiceStructOpeningTime
     */
    public function setOpeningTimeItem($_openingTimeItem)
    {
        return ($this->OpeningTimeItem = $_openingTimeItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructOpeningTimes
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
