<?php
/**
 * File for class NewyseServiceStructDebitCardItemContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructDebitCardItemContainer originally named DebitCardItemContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructDebitCardItemContainer extends NewyseServiceWsdlClass
{
    /**
     * The DebitCardItems
     * @var NewyseServiceStructDebitCardItems
     */
    public $DebitCardItems;
    /**
     * Constructor method for DebitCardItemContainer
     * @see parent::__construct()
     * @param NewyseServiceStructDebitCardItems $_debitCardItems
     * @return NewyseServiceStructDebitCardItemContainer
     */
    public function __construct($_debitCardItems = NULL)
    {
        parent::__construct(array('DebitCardItems'=>$_debitCardItems),false);
    }
    /**
     * Get DebitCardItems value
     * @return NewyseServiceStructDebitCardItems|null
     */
    public function getDebitCardItems()
    {
        return $this->DebitCardItems;
    }
    /**
     * Set DebitCardItems value
     * @param NewyseServiceStructDebitCardItems $_debitCardItems the DebitCardItems
     * @return NewyseServiceStructDebitCardItems
     */
    public function setDebitCardItems($_debitCardItems)
    {
        return ($this->DebitCardItems = $_debitCardItems);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructDebitCardItemContainer
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
