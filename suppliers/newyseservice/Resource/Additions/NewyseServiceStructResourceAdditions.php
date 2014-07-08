<?php
/**
 * File for class NewyseServiceStructResourceAdditions
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructResourceAdditions originally named ResourceAdditions
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructResourceAdditions extends NewyseServiceWsdlClass
{
    /**
     * The ResourceAdditionItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructResourceAddition
     */
    public $ResourceAdditionItem;
    /**
     * Constructor method for ResourceAdditions
     * @see parent::__construct()
     * @param NewyseServiceStructResourceAddition $_resourceAdditionItem
     * @return NewyseServiceStructResourceAdditions
     */
    public function __construct($_resourceAdditionItem = NULL)
    {
        parent::__construct(array('ResourceAdditionItem'=>$_resourceAdditionItem),false);
    }
    /**
     * Get ResourceAdditionItem value
     * @return NewyseServiceStructResourceAddition|null
     */
    public function getResourceAdditionItem()
    {
        return $this->ResourceAdditionItem;
    }
    /**
     * Set ResourceAdditionItem value
     * @param NewyseServiceStructResourceAddition $_resourceAdditionItem the ResourceAdditionItem
     * @return NewyseServiceStructResourceAddition
     */
    public function setResourceAdditionItem($_resourceAdditionItem)
    {
        return ($this->ResourceAdditionItem = $_resourceAdditionItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructResourceAdditions
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
