<?php
/**
 * File for class NewyseServiceStructDebitCardCustomers
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructDebitCardCustomers originally named DebitCardCustomers
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructDebitCardCustomers extends NewyseServiceWsdlClass
{
    /**
     * The DebitCardCustomer
     * @var NewyseServiceStructDebitCardCustomer
     */
    public $DebitCardCustomer;
    /**
     * Constructor method for DebitCardCustomers
     * @see parent::__construct()
     * @param NewyseServiceStructDebitCardCustomer $_debitCardCustomer
     * @return NewyseServiceStructDebitCardCustomers
     */
    public function __construct($_debitCardCustomer = NULL)
    {
        parent::__construct(array('DebitCardCustomer'=>$_debitCardCustomer),false);
    }
    /**
     * Get DebitCardCustomer value
     * @return NewyseServiceStructDebitCardCustomer|null
     */
    public function getDebitCardCustomer()
    {
        return $this->DebitCardCustomer;
    }
    /**
     * Set DebitCardCustomer value
     * @param NewyseServiceStructDebitCardCustomer $_debitCardCustomer the DebitCardCustomer
     * @return NewyseServiceStructDebitCardCustomer
     */
    public function setDebitCardCustomer($_debitCardCustomer)
    {
        return ($this->DebitCardCustomer = $_debitCardCustomer);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructDebitCardCustomers
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
