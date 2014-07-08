<?php
/**
 * File for class NewyseServiceStructVoucherCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructVoucherCriteria originally named VoucherCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructVoucherCriteria extends NewyseServiceWsdlClass
{
    /**
     * The VoucherSetCode
     * @var string
     */
    public $VoucherSetCode;
    /**
     * The ValidForDaysAfterGeneration
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $ValidForDaysAfterGeneration;
    /**
     * The ValidFrom
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var dateTime
     */
    public $ValidFrom;
    /**
     * The ValidTo
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var dateTime
     */
    public $ValidTo;
    /**
     * The NumberOfVouchers
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $NumberOfVouchers;
    /**
     * Constructor method for VoucherCriteria
     * @see parent::__construct()
     * @param string $_voucherSetCode
     * @param long $_validForDaysAfterGeneration
     * @param dateTime $_validFrom
     * @param dateTime $_validTo
     * @param long $_numberOfVouchers
     * @return NewyseServiceStructVoucherCriteria
     */
    public function __construct($_voucherSetCode = NULL,$_validForDaysAfterGeneration = NULL,$_validFrom = NULL,$_validTo = NULL,$_numberOfVouchers = NULL)
    {
        parent::__construct(array('VoucherSetCode'=>$_voucherSetCode,'ValidForDaysAfterGeneration'=>$_validForDaysAfterGeneration,'ValidFrom'=>$_validFrom,'ValidTo'=>$_validTo,'NumberOfVouchers'=>$_numberOfVouchers),false);
    }
    /**
     * Get VoucherSetCode value
     * @return string|null
     */
    public function getVoucherSetCode()
    {
        return $this->VoucherSetCode;
    }
    /**
     * Set VoucherSetCode value
     * @param string $_voucherSetCode the VoucherSetCode
     * @return string
     */
    public function setVoucherSetCode($_voucherSetCode)
    {
        return ($this->VoucherSetCode = $_voucherSetCode);
    }
    /**
     * Get ValidForDaysAfterGeneration value
     * @return long|null
     */
    public function getValidForDaysAfterGeneration()
    {
        return $this->ValidForDaysAfterGeneration;
    }
    /**
     * Set ValidForDaysAfterGeneration value
     * @param long $_validForDaysAfterGeneration the ValidForDaysAfterGeneration
     * @return long
     */
    public function setValidForDaysAfterGeneration($_validForDaysAfterGeneration)
    {
        return ($this->ValidForDaysAfterGeneration = $_validForDaysAfterGeneration);
    }
    /**
     * Get ValidFrom value
     * @return dateTime|null
     */
    public function getValidFrom()
    {
        return $this->ValidFrom;
    }
    /**
     * Set ValidFrom value
     * @param dateTime $_validFrom the ValidFrom
     * @return dateTime
     */
    public function setValidFrom($_validFrom)
    {
        return ($this->ValidFrom = $_validFrom);
    }
    /**
     * Get ValidTo value
     * @return dateTime|null
     */
    public function getValidTo()
    {
        return $this->ValidTo;
    }
    /**
     * Set ValidTo value
     * @param dateTime $_validTo the ValidTo
     * @return dateTime
     */
    public function setValidTo($_validTo)
    {
        return ($this->ValidTo = $_validTo);
    }
    /**
     * Get NumberOfVouchers value
     * @return long|null
     */
    public function getNumberOfVouchers()
    {
        return $this->NumberOfVouchers;
    }
    /**
     * Set NumberOfVouchers value
     * @param long $_numberOfVouchers the NumberOfVouchers
     * @return long
     */
    public function setNumberOfVouchers($_numberOfVouchers)
    {
        return ($this->NumberOfVouchers = $_numberOfVouchers);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructVoucherCriteria
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
