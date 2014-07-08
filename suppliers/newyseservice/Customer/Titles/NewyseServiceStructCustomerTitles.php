<?php
/**
 * File for class NewyseServiceStructCustomerTitles
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructCustomerTitles originally named CustomerTitles
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructCustomerTitles extends NewyseServiceWsdlClass
{
    /**
     * The CustomerTitleItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructWSCustomerTitle
     */
    public $CustomerTitleItem;
    /**
     * Constructor method for CustomerTitles
     * @see parent::__construct()
     * @param NewyseServiceStructWSCustomerTitle $_customerTitleItem
     * @return NewyseServiceStructCustomerTitles
     */
    public function __construct($_customerTitleItem = NULL)
    {
        parent::__construct(array('CustomerTitleItem'=>$_customerTitleItem),false);
    }
    /**
     * Get CustomerTitleItem value
     * @return NewyseServiceStructWSCustomerTitle|null
     */
    public function getCustomerTitleItem()
    {
        return $this->CustomerTitleItem;
    }
    /**
     * Set CustomerTitleItem value
     * @param NewyseServiceStructWSCustomerTitle $_customerTitleItem the CustomerTitleItem
     * @return NewyseServiceStructWSCustomerTitle
     */
    public function setCustomerTitleItem($_customerTitleItem)
    {
        return ($this->CustomerTitleItem = $_customerTitleItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructCustomerTitles
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
