<?php
/**
 * File for class NewyseServiceStructVoucherContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructVoucherContainer originally named VoucherContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructVoucherContainer extends NewyseServiceWsdlClass
{
    /**
     * The Vouchers
     * @var NewyseServiceStructVouchers
     */
    public $Vouchers;
    /**
     * Constructor method for VoucherContainer
     * @see parent::__construct()
     * @param NewyseServiceStructVouchers $_vouchers
     * @return NewyseServiceStructVoucherContainer
     */
    public function __construct($_vouchers = NULL)
    {
        parent::__construct(array('Vouchers'=>$_vouchers),false);
    }
    /**
     * Get Vouchers value
     * @return NewyseServiceStructVouchers|null
     */
    public function getVouchers()
    {
        return $this->Vouchers;
    }
    /**
     * Set Vouchers value
     * @param NewyseServiceStructVouchers $_vouchers the Vouchers
     * @return NewyseServiceStructVouchers
     */
    public function setVouchers($_vouchers)
    {
        return ($this->Vouchers = $_vouchers);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructVoucherContainer
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
