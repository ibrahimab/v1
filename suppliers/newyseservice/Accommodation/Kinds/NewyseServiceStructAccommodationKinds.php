<?php
/**
 * File for class NewyseServiceStructAccommodationKinds
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAccommodationKinds originally named AccommodationKinds
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAccommodationKinds extends NewyseServiceWsdlClass
{
    /**
     * The AccommodationKindItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructAccommodationKind
     */
    public $AccommodationKindItem;
    /**
     * Constructor method for AccommodationKinds
     * @see parent::__construct()
     * @param NewyseServiceStructAccommodationKind $_accommodationKindItem
     * @return NewyseServiceStructAccommodationKinds
     */
    public function __construct($_accommodationKindItem = NULL)
    {
        parent::__construct(array('AccommodationKindItem'=>$_accommodationKindItem),false);
    }
    /**
     * Get AccommodationKindItem value
     * @return NewyseServiceStructAccommodationKind|null
     */
    public function getAccommodationKindItem()
    {
        return $this->AccommodationKindItem;
    }
    /**
     * Set AccommodationKindItem value
     * @param NewyseServiceStructAccommodationKind $_accommodationKindItem the AccommodationKindItem
     * @return NewyseServiceStructAccommodationKind
     */
    public function setAccommodationKindItem($_accommodationKindItem)
    {
        return ($this->AccommodationKindItem = $_accommodationKindItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAccommodationKinds
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
