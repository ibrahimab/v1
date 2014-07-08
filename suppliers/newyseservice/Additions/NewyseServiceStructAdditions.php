<?php
/**
 * File for class NewyseServiceStructAdditions
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAdditions originally named Additions
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAdditions extends NewyseServiceWsdlClass
{
    /**
     * The AdditionItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructAddition
     */
    public $AdditionItem;
    /**
     * Constructor method for Additions
     * @see parent::__construct()
     * @param NewyseServiceStructAddition $_additionItem
     * @return NewyseServiceStructAdditions
     */
    public function __construct($_additionItem = NULL)
    {
        parent::__construct(array('AdditionItem'=>$_additionItem),false);
    }
    /**
     * Get AdditionItem value
     * @return NewyseServiceStructAddition|null
     */
    public function getAdditionItem()
    {
        return $this->AdditionItem;
    }
    /**
     * Set AdditionItem value
     * @param NewyseServiceStructAddition $_additionItem the AdditionItem
     * @return NewyseServiceStructAddition
     */
    public function setAdditionItem($_additionItem)
    {
        return ($this->AdditionItem = $_additionItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAdditions
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
