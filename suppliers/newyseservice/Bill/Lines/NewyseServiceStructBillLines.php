<?php
/**
 * File for class NewyseServiceStructBillLines
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructBillLines originally named BillLines
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructBillLines extends NewyseServiceWsdlClass
{
    /**
     * The BillLineItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructReservationBillLine
     */
    public $BillLineItem;
    /**
     * Constructor method for BillLines
     * @see parent::__construct()
     * @param NewyseServiceStructReservationBillLine $_billLineItem
     * @return NewyseServiceStructBillLines
     */
    public function __construct($_billLineItem = NULL)
    {
        parent::__construct(array('BillLineItem'=>$_billLineItem),false);
    }
    /**
     * Get BillLineItem value
     * @return NewyseServiceStructReservationBillLine|null
     */
    public function getBillLineItem()
    {
        return $this->BillLineItem;
    }
    /**
     * Set BillLineItem value
     * @param NewyseServiceStructReservationBillLine $_billLineItem the BillLineItem
     * @return NewyseServiceStructReservationBillLine
     */
    public function setBillLineItem($_billLineItem)
    {
        return ($this->BillLineItem = $_billLineItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructBillLines
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
