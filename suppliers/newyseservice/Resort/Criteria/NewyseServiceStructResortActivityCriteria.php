<?php
/**
 * File for class NewyseServiceStructResortActivityCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructResortActivityCriteria originally named ResortActivityCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructResortActivityCriteria extends NewyseServiceWsdlClass
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
     * The FromDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var dateTime
     */
    public $FromDate;
    /**
     * The ToDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var dateTime
     */
    public $ToDate;
    /**
     * Constructor method for ResortActivityCriteria
     * @see parent::__construct()
     * @param string $_resortCode
     * @param dateTime $_fromDate
     * @param dateTime $_toDate
     * @return NewyseServiceStructResortActivityCriteria
     */
    public function __construct($_resortCode = NULL,$_fromDate = NULL,$_toDate = NULL)
    {
        parent::__construct(array('ResortCode'=>$_resortCode,'FromDate'=>$_fromDate,'ToDate'=>$_toDate),false);
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
     * Get FromDate value
     * @return dateTime|null
     */
    public function getFromDate()
    {
        return $this->FromDate;
    }
    /**
     * Set FromDate value
     * @param dateTime $_fromDate the FromDate
     * @return dateTime
     */
    public function setFromDate($_fromDate)
    {
        return ($this->FromDate = $_fromDate);
    }
    /**
     * Get ToDate value
     * @return dateTime|null
     */
    public function getToDate()
    {
        return $this->ToDate;
    }
    /**
     * Set ToDate value
     * @param dateTime $_toDate the ToDate
     * @return dateTime
     */
    public function setToDate($_toDate)
    {
        return ($this->ToDate = $_toDate);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructResortActivityCriteria
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
