<?php
/**
 * File for class NewyseServiceStructActivityCategories
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructActivityCategories originally named ActivityCategories
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructActivityCategories extends NewyseServiceWsdlClass
{
    /**
     * The ActivityCategoryItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructActivityCategory
     */
    public $ActivityCategoryItem;
    /**
     * Constructor method for ActivityCategories
     * @see parent::__construct()
     * @param NewyseServiceStructActivityCategory $_activityCategoryItem
     * @return NewyseServiceStructActivityCategories
     */
    public function __construct($_activityCategoryItem = NULL)
    {
        parent::__construct(array('ActivityCategoryItem'=>$_activityCategoryItem),false);
    }
    /**
     * Get ActivityCategoryItem value
     * @return NewyseServiceStructActivityCategory|null
     */
    public function getActivityCategoryItem()
    {
        return $this->ActivityCategoryItem;
    }
    /**
     * Set ActivityCategoryItem value
     * @param NewyseServiceStructActivityCategory $_activityCategoryItem the ActivityCategoryItem
     * @return NewyseServiceStructActivityCategory
     */
    public function setActivityCategoryItem($_activityCategoryItem)
    {
        return ($this->ActivityCategoryItem = $_activityCategoryItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructActivityCategories
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
