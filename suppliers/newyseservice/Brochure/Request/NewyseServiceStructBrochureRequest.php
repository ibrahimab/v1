<?php
/**
 * File for class NewyseServiceStructBrochureRequest
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructBrochureRequest originally named BrochureRequest
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructBrochureRequest extends NewyseServiceWsdlClass
{
    /**
     * The BrochureCodes
     * @var NewyseServiceStructBrochureCodes
     */
    public $BrochureCodes;
    /**
     * The CustomerId
     * @var long
     */
    public $CustomerId;
    /**
     * Constructor method for BrochureRequest
     * @see parent::__construct()
     * @param NewyseServiceStructBrochureCodes $_brochureCodes
     * @param long $_customerId
     * @return NewyseServiceStructBrochureRequest
     */
    public function __construct($_brochureCodes = NULL,$_customerId = NULL)
    {
        parent::__construct(array('BrochureCodes'=>$_brochureCodes,'CustomerId'=>$_customerId),false);
    }
    /**
     * Get BrochureCodes value
     * @return NewyseServiceStructBrochureCodes|null
     */
    public function getBrochureCodes()
    {
        return $this->BrochureCodes;
    }
    /**
     * Set BrochureCodes value
     * @param NewyseServiceStructBrochureCodes $_brochureCodes the BrochureCodes
     * @return NewyseServiceStructBrochureCodes
     */
    public function setBrochureCodes($_brochureCodes)
    {
        return ($this->BrochureCodes = $_brochureCodes);
    }
    /**
     * Get CustomerId value
     * @return long|null
     */
    public function getCustomerId()
    {
        return $this->CustomerId;
    }
    /**
     * Set CustomerId value
     * @param long $_customerId the CustomerId
     * @return long
     */
    public function setCustomerId($_customerId)
    {
        return ($this->CustomerId = $_customerId);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructBrochureRequest
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
