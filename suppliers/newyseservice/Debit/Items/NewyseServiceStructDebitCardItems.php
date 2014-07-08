<?php
/**
 * File for class NewyseServiceStructDebitCardItems
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructDebitCardItems originally named DebitCardItems
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructDebitCardItems extends NewyseServiceWsdlClass
{
    /**
     * The DebitCardItem
     * @var NewyseServiceStructDebitCardItem
     */
    public $DebitCardItem;
    /**
     * Constructor method for DebitCardItems
     * @see parent::__construct()
     * @param NewyseServiceStructDebitCardItem $_debitCardItem
     * @return NewyseServiceStructDebitCardItems
     */
    public function __construct($_debitCardItem = NULL)
    {
        parent::__construct(array('DebitCardItem'=>$_debitCardItem),false);
    }
    /**
     * Get DebitCardItem value
     * @return NewyseServiceStructDebitCardItem|null
     */
    public function getDebitCardItem()
    {
        return $this->DebitCardItem;
    }
    /**
     * Set DebitCardItem value
     * @param NewyseServiceStructDebitCardItem $_debitCardItem the DebitCardItem
     * @return NewyseServiceStructDebitCardItem
     */
    public function setDebitCardItem($_debitCardItem)
    {
        return ($this->DebitCardItem = $_debitCardItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructDebitCardItems
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
