<?php
/**
 * File for class NewyseServiceStructDebitCardConsumptions
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructDebitCardConsumptions originally named DebitCardConsumptions
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructDebitCardConsumptions extends NewyseServiceWsdlClass
{
    /**
     * The DebitCardConsumption
     * @var NewyseServiceStructDebitCardConsumption
     */
    public $DebitCardConsumption;
    /**
     * Constructor method for DebitCardConsumptions
     * @see parent::__construct()
     * @param NewyseServiceStructDebitCardConsumption $_debitCardConsumption
     * @return NewyseServiceStructDebitCardConsumptions
     */
    public function __construct($_debitCardConsumption = NULL)
    {
        parent::__construct(array('DebitCardConsumption'=>$_debitCardConsumption),false);
    }
    /**
     * Get DebitCardConsumption value
     * @return NewyseServiceStructDebitCardConsumption|null
     */
    public function getDebitCardConsumption()
    {
        return $this->DebitCardConsumption;
    }
    /**
     * Set DebitCardConsumption value
     * @param NewyseServiceStructDebitCardConsumption $_debitCardConsumption the DebitCardConsumption
     * @return NewyseServiceStructDebitCardConsumption
     */
    public function setDebitCardConsumption($_debitCardConsumption)
    {
        return ($this->DebitCardConsumption = $_debitCardConsumption);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructDebitCardConsumptions
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
