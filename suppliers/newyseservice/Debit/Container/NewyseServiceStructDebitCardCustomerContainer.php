<?php
/**
 * File for class NewyseServiceStructDebitCardCustomerContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructDebitCardCustomerContainer originally named DebitCardCustomerContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructDebitCardCustomerContainer extends NewyseServiceWsdlClass
{
    /**
     * The DebitCardCustomers
     * @var NewyseServiceStructDebitCardCustomers
     */
    public $DebitCardCustomers;
    /**
     * Constructor method for DebitCardCustomerContainer
     * @see parent::__construct()
     * @param NewyseServiceStructDebitCardCustomers $_debitCardCustomers
     * @return NewyseServiceStructDebitCardCustomerContainer
     */
    public function __construct($_debitCardCustomers = NULL)
    {
        parent::__construct(array('DebitCardCustomers'=>$_debitCardCustomers),false);
    }
    /**
     * Get DebitCardCustomers value
     * @return NewyseServiceStructDebitCardCustomers|null
     */
    public function getDebitCardCustomers()
    {
        return $this->DebitCardCustomers;
    }
    /**
     * Set DebitCardCustomers value
     * @param NewyseServiceStructDebitCardCustomers $_debitCardCustomers the DebitCardCustomers
     * @return NewyseServiceStructDebitCardCustomers
     */
    public function setDebitCardCustomers($_debitCardCustomers)
    {
        return ($this->DebitCardCustomers = $_debitCardCustomers);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructDebitCardCustomerContainer
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
