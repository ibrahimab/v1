<?php
/**
 * File for class NewyseServiceStructAccommodationTypeSearchObjects
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAccommodationTypeSearchObjects originally named AccommodationTypeSearchObjects
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAccommodationTypeSearchObjects extends NewyseServiceWsdlClass
{
    /**
     * The AccommodationTypeSearchObjectItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * @var NewyseServiceStructAccommodationTypeSearchObject
     */
    public $AccommodationTypeSearchObjectItem;
    /**
     * Constructor method for AccommodationTypeSearchObjects
     * @see parent::__construct()
     * @param NewyseServiceStructAccommodationTypeSearchObject $_accommodationTypeSearchObjectItem
     * @return NewyseServiceStructAccommodationTypeSearchObjects
     */
    public function __construct($_accommodationTypeSearchObjectItem = NULL)
    {
        parent::__construct(array('AccommodationTypeSearchObjectItem'=>$_accommodationTypeSearchObjectItem),false);
    }
    /**
     * Get AccommodationTypeSearchObjectItem value
     * @return NewyseServiceStructAccommodationTypeSearchObject|null
     */
    public function getAccommodationTypeSearchObjectItem()
    {
        return $this->AccommodationTypeSearchObjectItem;
    }
    /**
     * Set AccommodationTypeSearchObjectItem value
     * @param NewyseServiceStructAccommodationTypeSearchObject $_accommodationTypeSearchObjectItem the AccommodationTypeSearchObjectItem
     * @return NewyseServiceStructAccommodationTypeSearchObject
     */
    public function setAccommodationTypeSearchObjectItem($_accommodationTypeSearchObjectItem)
    {
        return ($this->AccommodationTypeSearchObjectItem = $_accommodationTypeSearchObjectItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAccommodationTypeSearchObjects
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
