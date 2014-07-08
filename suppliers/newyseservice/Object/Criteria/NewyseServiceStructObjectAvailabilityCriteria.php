<?php
/**
 * File for class NewyseServiceStructObjectAvailabilityCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructObjectAvailabilityCriteria originally named ObjectAvailabilityCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructObjectAvailabilityCriteria extends NewyseServiceWsdlClass
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
     * The ObjectId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $ObjectId;
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
     * Constructor method for ObjectAvailabilityCriteria
     * @see parent::__construct()
     * @param long $_resourceId
     * @param long $_objectId
     * @param dateTime $_startDate
     * @param dateTime $_endDate
     * @return NewyseServiceStructObjectAvailabilityCriteria
     */
    public function __construct($_resourceId = NULL,$_objectId = NULL,$_startDate = NULL,$_endDate = NULL)
    {
        parent::__construct(array('ResourceId'=>$_resourceId,'ObjectId'=>$_objectId,'StartDate'=>$_startDate,'EndDate'=>$_endDate),false);
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
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructObjectAvailabilityCriteria
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
