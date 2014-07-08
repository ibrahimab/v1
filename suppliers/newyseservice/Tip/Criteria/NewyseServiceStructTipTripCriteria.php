<?php
/**
 * File for class NewyseServiceStructTipTripCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructTipTripCriteria originally named TipTripCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructTipTripCriteria extends NewyseServiceWsdlClass
{
    /**
     * The ResortCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $ResortCode;
    /**
     * The TipTripCategoryId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $TipTripCategoryId;
    /**
     * Constructor method for TipTripCriteria
     * @see parent::__construct()
     * @param string $_resortCode
     * @param long $_tipTripCategoryId
     * @return NewyseServiceStructTipTripCriteria
     */
    public function __construct($_resortCode = NULL,$_tipTripCategoryId = NULL)
    {
        parent::__construct(array('ResortCode'=>$_resortCode,'TipTripCategoryId'=>$_tipTripCategoryId),false);
    }
    /**
     * Get ResortCode value
     * @return string|null
     */
    public function getResortCode()
    {
        return $this->ResortCode;
    }
    /**
     * Set ResortCode value
     * @param string $_resortCode the ResortCode
     * @return string
     */
    public function setResortCode($_resortCode)
    {
        return ($this->ResortCode = $_resortCode);
    }
    /**
     * Get TipTripCategoryId value
     * @return long|null
     */
    public function getTipTripCategoryId()
    {
        return $this->TipTripCategoryId;
    }
    /**
     * Set TipTripCategoryId value
     * @param long $_tipTripCategoryId the TipTripCategoryId
     * @return long
     */
    public function setTipTripCategoryId($_tipTripCategoryId)
    {
        return ($this->TipTripCategoryId = $_tipTripCategoryId);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructTipTripCriteria
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
