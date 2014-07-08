<?php
/**
 * File for class NewyseServiceStructAccommodationTypes
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAccommodationTypes originally named AccommodationTypes
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAccommodationTypes extends NewyseServiceWsdlClass
{
    /**
     * The AccommodationTypeItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructAccommodationTypeSearch
     */
    public $AccommodationTypeItem;
    /**
     * Constructor method for AccommodationTypes
     * @see parent::__construct()
     * @param NewyseServiceStructAccommodationTypeSearch $_accommodationTypeItem
     * @return NewyseServiceStructAccommodationTypes
     */
    public function __construct($_accommodationTypeItem = NULL)
    {
        parent::__construct(array('AccommodationTypeItem'=>$_accommodationTypeItem),false);
    }
    /**
     * Get AccommodationTypeItem value
     * @return NewyseServiceStructAccommodationTypeSearch|null
     */
    public function getAccommodationTypeItem()
    {
        return $this->AccommodationTypeItem;
    }
    /**
     * Set AccommodationTypeItem value
     * @param NewyseServiceStructAccommodationTypeSearch $_accommodationTypeItem the AccommodationTypeItem
     * @return NewyseServiceStructAccommodationTypeSearch
     */
    public function setAccommodationTypeItem($_accommodationTypeItem)
    {
        return ($this->AccommodationTypeItem = $_accommodationTypeItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAccommodationTypes
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
