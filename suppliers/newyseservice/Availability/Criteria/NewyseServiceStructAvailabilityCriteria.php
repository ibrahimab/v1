<?php
/**
 * File for class NewyseServiceStructAvailabilityCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAvailabilityCriteria originally named AvailabilityCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAvailabilityCriteria extends NewyseServiceWsdlClass
{
    /**
     * The ResourceId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $ResourceId;
    /**
     * The ResortCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $ResortCode;
    /**
     * The StartDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var dateTime
     */
    public $StartDate;
    /**
     * The EndDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var dateTime
     */
    public $EndDate;
    /**
     * The NrOfNights
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var int
     */
    public $NrOfNights;
    /**
     * The SpecialCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $SpecialCode;
    /**
     * The IncludeHiddenSpecials
     * Meta informations extracted from the WSDL
     * - default : false
     * - nillable : true
     * @var boolean
     */
    public $IncludeHiddenSpecials;
    /**
     * The IncludeAllPrices
     * Meta informations extracted from the WSDL
     * - default : false
     * - nillable : true
     * @var boolean
     */
    public $IncludeAllPrices;
    /**
     * Constructor method for AvailabilityCriteria
     * @see parent::__construct()
     * @param long $_resourceId
     * @param string $_resortCode
     * @param dateTime $_startDate
     * @param dateTime $_endDate
     * @param int $_nrOfNights
     * @param string $_specialCode
     * @param boolean $_includeHiddenSpecials
     * @param boolean $_includeAllPrices
     * @return NewyseServiceStructAvailabilityCriteria
     */
    public function __construct($_resourceId = NULL,$_resortCode = NULL,$_startDate = NULL,$_endDate = NULL,$_nrOfNights = NULL,$_specialCode = NULL,$_includeHiddenSpecials = false,$_includeAllPrices = false)
    {
        parent::__construct(array('ResourceId'=>$_resourceId,'ResortCode'=>$_resortCode,'StartDate'=>$_startDate,'EndDate'=>$_endDate,'NrOfNights'=>$_nrOfNights,'SpecialCode'=>$_specialCode,'IncludeHiddenSpecials'=>$_includeHiddenSpecials,'IncludeAllPrices'=>$_includeAllPrices),false);
    }
    /**
     * Get ResourceId value
     * @return long|null
     */
    public function getResourceId()
    {
        return $this->ResourceId;
    }
    /**
     * Set ResourceId value
     * @param long $_resourceId the ResourceId
     * @return long
     */
    public function setResourceId($_resourceId)
    {
        return ($this->ResourceId = $_resourceId);
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
     * Get StartDate value
     * @return dateTime|null
     */
    public function getStartDate()
    {
        return $this->StartDate;
    }
    /**
     * Set StartDate value
     * @param dateTime $_startDate the StartDate
     * @return dateTime
     */
    public function setStartDate($_startDate)
    {
        return ($this->StartDate = $_startDate);
    }
    /**
     * Get EndDate value
     * @return dateTime|null
     */
    public function getEndDate()
    {
        return $this->EndDate;
    }
    /**
     * Set EndDate value
     * @param dateTime $_endDate the EndDate
     * @return dateTime
     */
    public function setEndDate($_endDate)
    {
        return ($this->EndDate = $_endDate);
    }
    /**
     * Get NrOfNights value
     * @return int|null
     */
    public function getNrOfNights()
    {
        return $this->NrOfNights;
    }
    /**
     * Set NrOfNights value
     * @param int $_nrOfNights the NrOfNights
     * @return int
     */
    public function setNrOfNights($_nrOfNights)
    {
        return ($this->NrOfNights = $_nrOfNights);
    }
    /**
     * Get SpecialCode value
     * @return string|null
     */
    public function getSpecialCode()
    {
        return $this->SpecialCode;
    }
    /**
     * Set SpecialCode value
     * @param string $_specialCode the SpecialCode
     * @return string
     */
    public function setSpecialCode($_specialCode)
    {
        return ($this->SpecialCode = $_specialCode);
    }
    /**
     * Get IncludeHiddenSpecials value
     * @return boolean|null
     */
    public function getIncludeHiddenSpecials()
    {
        return $this->IncludeHiddenSpecials;
    }
    /**
     * Set IncludeHiddenSpecials value
     * @param boolean $_includeHiddenSpecials the IncludeHiddenSpecials
     * @return boolean
     */
    public function setIncludeHiddenSpecials($_includeHiddenSpecials)
    {
        return ($this->IncludeHiddenSpecials = $_includeHiddenSpecials);
    }
    /**
     * Get IncludeAllPrices value
     * @return boolean|null
     */
    public function getIncludeAllPrices()
    {
        return $this->IncludeAllPrices;
    }
    /**
     * Set IncludeAllPrices value
     * @param boolean $_includeAllPrices the IncludeAllPrices
     * @return boolean
     */
    public function setIncludeAllPrices($_includeAllPrices)
    {
        return ($this->IncludeAllPrices = $_includeAllPrices);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAvailabilityCriteria
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
