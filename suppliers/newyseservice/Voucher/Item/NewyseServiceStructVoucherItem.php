<?php
/**
 * File for class NewyseServiceStructVoucherItem
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructVoucherItem originally named VoucherItem
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructVoucherItem extends NewyseServiceWsdlClass
{
    /**
     * The Type
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Type;
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
     * The Codes
     * @var NewyseServiceStructCodes
     */
    public $Codes;
    /**
     * Constructor method for VoucherItem
     * @see parent::__construct()
     * @param string $_type
     * @param dateTime $_validFrom
     * @param dateTime $_validTo
     * @param NewyseServiceStructCodes $_codes
     * @return NewyseServiceStructVoucherItem
     */
    public function __construct($_type = NULL,$_validFrom = NULL,$_validTo = NULL,$_codes = NULL)
    {
        parent::__construct(array('Type'=>$_type,'ValidFrom'=>$_validFrom,'ValidTo'=>$_validTo,'Codes'=>$_codes),false);
    }
    /**
     * Get Type value
     * @return string|null
     */
    public function getType()
    {
        return $this->Type;
    }
    /**
     * Set Type value
     * @param string $_type the Type
     * @return string
     */
    public function setType($_type)
    {
        return ($this->Type = $_type);
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
     * Get Codes value
     * @return NewyseServiceStructCodes|null
     */
    public function getCodes()
    {
        return $this->Codes;
    }
    /**
     * Set Codes value
     * @param NewyseServiceStructCodes $_codes the Codes
     * @return NewyseServiceStructCodes
     */
    public function setCodes($_codes)
    {
        return ($this->Codes = $_codes);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructVoucherItem
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
