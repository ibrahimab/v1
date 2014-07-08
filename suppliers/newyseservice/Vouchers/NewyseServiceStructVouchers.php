<?php
/**
 * File for class NewyseServiceStructVouchers
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructVouchers originally named Vouchers
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructVouchers extends NewyseServiceWsdlClass
{
    /**
     * The VoucherItem
     * @var NewyseServiceStructVoucherItem
     */
    public $VoucherItem;
    /**
     * Constructor method for Vouchers
     * @see parent::__construct()
     * @param NewyseServiceStructVoucherItem $_voucherItem
     * @return NewyseServiceStructVouchers
     */
    public function __construct($_voucherItem = NULL)
    {
        parent::__construct(array('VoucherItem'=>$_voucherItem),false);
    }
    /**
     * Get VoucherItem value
     * @return NewyseServiceStructVoucherItem|null
     */
    public function getVoucherItem()
    {
        return $this->VoucherItem;
    }
    /**
     * Set VoucherItem value
     * @param NewyseServiceStructVoucherItem $_voucherItem the VoucherItem
     * @return NewyseServiceStructVoucherItem
     */
    public function setVoucherItem($_voucherItem)
    {
        return ($this->VoucherItem = $_voucherItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructVouchers
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
