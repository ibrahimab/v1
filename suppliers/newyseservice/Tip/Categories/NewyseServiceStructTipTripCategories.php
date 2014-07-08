<?php
/**
 * File for class NewyseServiceStructTipTripCategories
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructTipTripCategories originally named TipTripCategories
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructTipTripCategories extends NewyseServiceWsdlClass
{
    /**
     * The TipTripCategoryItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructTipTripCategory
     */
    public $TipTripCategoryItem;
    /**
     * Constructor method for TipTripCategories
     * @see parent::__construct()
     * @param NewyseServiceStructTipTripCategory $_tipTripCategoryItem
     * @return NewyseServiceStructTipTripCategories
     */
    public function __construct($_tipTripCategoryItem = NULL)
    {
        parent::__construct(array('TipTripCategoryItem'=>$_tipTripCategoryItem),false);
    }
    /**
     * Get TipTripCategoryItem value
     * @return NewyseServiceStructTipTripCategory|null
     */
    public function getTipTripCategoryItem()
    {
        return $this->TipTripCategoryItem;
    }
    /**
     * Set TipTripCategoryItem value
     * @param NewyseServiceStructTipTripCategory $_tipTripCategoryItem the TipTripCategoryItem
     * @return NewyseServiceStructTipTripCategory
     */
    public function setTipTripCategoryItem($_tipTripCategoryItem)
    {
        return ($this->TipTripCategoryItem = $_tipTripCategoryItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructTipTripCategories
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
