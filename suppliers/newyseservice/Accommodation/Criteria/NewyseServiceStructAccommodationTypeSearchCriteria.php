<?php
/**
 * File for class NewyseServiceStructAccommodationTypeSearchCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAccommodationTypeSearchCriteria originally named AccommodationTypeSearchCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAccommodationTypeSearchCriteria extends NewyseServiceWsdlClass
{
    /**
     * The ResourceId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $ResourceId;
    /**
     * The ObjectId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $ObjectId;
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
     * @var dateTime
     */
    public $StartDate;
    /**
     * The EndDate
     * @var dateTime
     */
    public $EndDate;
    /**
     * The SubjectQuantities
     * @var NewyseServiceStructSubjectQuantities
     */
    public $SubjectQuantities;
    /**
     * The SpecialCodes
     * @var NewyseServiceStructSpecialCodes
     */
    public $SpecialCodes;
    /**
     * The Properties
     * @var NewyseServiceStructProperties
     */
    public $Properties;
    /**
     * The ReturnObjects
     * Meta informations extracted from the WSDL
     * - default : false
     * - nillable : true
     * @var boolean
     */
    public $ReturnObjects;
    /**
     * Constructor method for AccommodationTypeSearchCriteria
     * @see parent::__construct()
     * @param long $_resourceId
     * @param long $_objectId
     * @param string $_resortCode
     * @param dateTime $_startDate
     * @param dateTime $_endDate
     * @param NewyseServiceStructSubjectQuantities $_subjectQuantities
     * @param NewyseServiceStructSpecialCodes $_specialCodes
     * @param NewyseServiceStructProperties $_properties
     * @param boolean $_returnObjects
     * @return NewyseServiceStructAccommodationTypeSearchCriteria
     */
    public function __construct($_resourceId = NULL,$_objectId = NULL,$_resortCode = NULL,$_startDate = NULL,$_endDate = NULL,$_subjectQuantities = NULL,$_specialCodes = NULL,$_properties = NULL,$_returnObjects = false)
    {
        parent::__construct(array('ResourceId'=>$_resourceId,'ObjectId'=>$_objectId,'ResortCode'=>$_resortCode,'StartDate'=>$_startDate,'EndDate'=>$_endDate,'SubjectQuantities'=>$_subjectQuantities,'SpecialCodes'=>$_specialCodes,'Properties'=>$_properties,'ReturnObjects'=>$_returnObjects),false);
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
     * Get ObjectId value
     * @return long|null
     */
    public function getObjectId()
    {
        return $this->ObjectId;
    }
    /**
     * Set ObjectId value
     * @param long $_objectId the ObjectId
     * @return long
     */
    public function setObjectId($_objectId)
    {
        return ($this->ObjectId = $_objectId);
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
     * Get SubjectQuantities value
     * @return NewyseServiceStructSubjectQuantities|null
     */
    public function getSubjectQuantities()
    {
        return $this->SubjectQuantities;
    }
    /**
     * Set SubjectQuantities value
     * @param NewyseServiceStructSubjectQuantities $_subjectQuantities the SubjectQuantities
     * @return NewyseServiceStructSubjectQuantities
     */
    public function setSubjectQuantities($_subjectQuantities)
    {
        return ($this->SubjectQuantities = $_subjectQuantities);
    }
    /**
     * Get SpecialCodes value
     * @return NewyseServiceStructSpecialCodes|null
     */
    public function getSpecialCodes()
    {
        return $this->SpecialCodes;
    }
    /**
     * Set SpecialCodes value
     * @param NewyseServiceStructSpecialCodes $_specialCodes the SpecialCodes
     * @return NewyseServiceStructSpecialCodes
     */
    public function setSpecialCodes($_specialCodes)
    {
        return ($this->SpecialCodes = $_specialCodes);
    }
    /**
     * Get Properties value
     * @return NewyseServiceStructProperties|null
     */
    public function getProperties()
    {
        return $this->Properties;
    }
    /**
     * Set Properties value
     * @param NewyseServiceStructProperties $_properties the Properties
     * @return NewyseServiceStructProperties
     */
    public function setProperties($_properties)
    {
        return ($this->Properties = $_properties);
    }
    /**
     * Get ReturnObjects value
     * @return boolean|null
     */
    public function getReturnObjects()
    {
        return $this->ReturnObjects;
    }
    /**
     * Set ReturnObjects value
     * @param boolean $_returnObjects the ReturnObjects
     * @return boolean
     */
    public function setReturnObjects($_returnObjects)
    {
        return ($this->ReturnObjects = $_returnObjects);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAccommodationTypeSearchCriteria
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
