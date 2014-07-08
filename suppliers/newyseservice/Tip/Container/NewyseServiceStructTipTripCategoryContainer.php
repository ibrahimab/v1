<?php
/**
 * File for class NewyseServiceStructTipTripCategoryContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructTipTripCategoryContainer originally named TipTripCategoryContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructTipTripCategoryContainer extends NewyseServiceWsdlClass
{
    /**
     * The TipTripCategories
     * @var NewyseServiceStructTipTripCategories
     */
    public $TipTripCategories;
    /**
     * Constructor method for TipTripCategoryContainer
     * @see parent::__construct()
     * @param NewyseServiceStructTipTripCategories $_tipTripCategories
     * @return NewyseServiceStructTipTripCategoryContainer
     */
    public function __construct($_tipTripCategories = NULL)
    {
        parent::__construct(array('TipTripCategories'=>$_tipTripCategories),false);
    }
    /**
     * Get TipTripCategories value
     * @return NewyseServiceStructTipTripCategories|null
     */
    public function getTipTripCategories()
    {
        return $this->TipTripCategories;
    }
    /**
     * Set TipTripCategories value
     * @param NewyseServiceStructTipTripCategories $_tipTripCategories the TipTripCategories
     * @return NewyseServiceStructTipTripCategories
     */
    public function setTipTripCategories($_tipTripCategories)
    {
        return ($this->TipTripCategories = $_tipTripCategories);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructTipTripCategoryContainer
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
