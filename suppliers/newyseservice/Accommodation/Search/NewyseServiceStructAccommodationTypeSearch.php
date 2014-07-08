<?php
/**
 * File for class NewyseServiceStructAccommodationTypeSearch
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAccommodationTypeSearch originally named AccommodationTypeSearch
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAccommodationTypeSearch extends NewyseServiceWsdlClass
{
    /**
     * The ResourceId
     * @var long
     */
    public $ResourceId;
    /**
     * The Code
     * @var string
     */
    public $Code;
    /**
     * The Name
     * @var string
     */
    public $Name;
    /**
     * The ShortDescription
     * @var string
     */
    public $ShortDescription;
    /**
     * The Description
     * @var string
     */
    public $Description;
    /**
     * The Description2
     * @var string
     */
    public $Description2;
    /**
     * The PropertyManagerId
     * @var long
     */
    public $PropertyManagerId;
    /**
     * The ImageManagerId
     * @var long
     */
    public $ImageManagerId;
    /**
     * The ResortId
     * @var long
     */
    public $ResortId;
    /**
     * The ResortCode
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
     * The AccommodationTypeSearchObjects
     * @var NewyseServiceStructAccommodationTypeSearchObjects
     */
    public $AccommodationTypeSearchObjects;
    /**
     * Constructor method for AccommodationTypeSearch
     * @see parent::__construct()
     * @param long $_resourceId
     * @param string $_code
     * @param string $_name
     * @param string $_shortDescription
     * @param string $_description
     * @param string $_description2
     * @param long $_propertyManagerId
     * @param long $_imageManagerId
     * @param long $_resortId
     * @param string $_resortCode
     * @param dateTime $_startDate
     * @param dateTime $_endDate
     * @param NewyseServiceStructAccommodationTypeSearchObjects $_accommodationTypeSearchObjects
     * @return NewyseServiceStructAccommodationTypeSearch
     */
    public function __construct($_resourceId = NULL,$_code = NULL,$_name = NULL,$_shortDescription = NULL,$_description = NULL,$_description2 = NULL,$_propertyManagerId = NULL,$_imageManagerId = NULL,$_resortId = NULL,$_resortCode = NULL,$_startDate = NULL,$_endDate = NULL,$_accommodationTypeSearchObjects = NULL)
    {
        parent::__construct(array('ResourceId'=>$_resourceId,'Code'=>$_code,'Name'=>$_name,'ShortDescription'=>$_shortDescription,'Description'=>$_description,'Description2'=>$_description2,'PropertyManagerId'=>$_propertyManagerId,'ImageManagerId'=>$_imageManagerId,'ResortId'=>$_resortId,'ResortCode'=>$_resortCode,'StartDate'=>$_startDate,'EndDate'=>$_endDate,'AccommodationTypeSearchObjects'=>$_accommodationTypeSearchObjects),false);
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
     * Get Code value
     * @return string|null
     */
    public function getCode()
    {
        return $this->Code;
    }
    /**
     * Set Code value
     * @param string $_code the Code
     * @return string
     */
    public function setCode($_code)
    {
        return ($this->Code = $_code);
    }
    /**
     * Get Name value
     * @return string|null
     */
    public function getName()
    {
        return $this->Name;
    }
    /**
     * Set Name value
     * @param string $_name the Name
     * @return string
     */
    public function setName($_name)
    {
        return ($this->Name = $_name);
    }
    /**
     * Get ShortDescription value
     * @return string|null
     */
    public function getShortDescription()
    {
        return $this->ShortDescription;
    }
    /**
     * Set ShortDescription value
     * @param string $_shortDescription the ShortDescription
     * @return string
     */
    public function setShortDescription($_shortDescription)
    {
        return ($this->ShortDescription = $_shortDescription);
    }
    /**
     * Get Description value
     * @return string|null
     */
    public function getDescription()
    {
        return $this->Description;
    }
    /**
     * Set Description value
     * @param string $_description the Description
     * @return string
     */
    public function setDescription($_description)
    {
        return ($this->Description = $_description);
    }
    /**
     * Get Description2 value
     * @return string|null
     */
    public function getDescription2()
    {
        return $this->Description2;
    }
    /**
     * Set Description2 value
     * @param string $_description2 the Description2
     * @return string
     */
    public function setDescription2($_description2)
    {
        return ($this->Description2 = $_description2);
    }
    /**
     * Get PropertyManagerId value
     * @return long|null
     */
    public function getPropertyManagerId()
    {
        return $this->PropertyManagerId;
    }
    /**
     * Set PropertyManagerId value
     * @param long $_propertyManagerId the PropertyManagerId
     * @return long
     */
    public function setPropertyManagerId($_propertyManagerId)
    {
        return ($this->PropertyManagerId = $_propertyManagerId);
    }
    /**
     * Get ImageManagerId value
     * @return long|null
     */
    public function getImageManagerId()
    {
        return $this->ImageManagerId;
    }
    /**
     * Set ImageManagerId value
     * @param long $_imageManagerId the ImageManagerId
     * @return long
     */
    public function setImageManagerId($_imageManagerId)
    {
        return ($this->ImageManagerId = $_imageManagerId);
    }
    /**
     * Get ResortId value
     * @return long|null
     */
    public function getResortId()
    {
        return $this->ResortId;
    }
    /**
     * Set ResortId value
     * @param long $_resortId the ResortId
     * @return long
     */
    public function setResortId($_resortId)
    {
        return ($this->ResortId = $_resortId);
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
     * Get AccommodationTypeSearchObjects value
     * @return NewyseServiceStructAccommodationTypeSearchObjects|null
     */
    public function getAccommodationTypeSearchObjects()
    {
        return $this->AccommodationTypeSearchObjects;
    }
    /**
     * Set AccommodationTypeSearchObjects value
     * @param NewyseServiceStructAccommodationTypeSearchObjects $_accommodationTypeSearchObjects the AccommodationTypeSearchObjects
     * @return NewyseServiceStructAccommodationTypeSearchObjects
     */
    public function setAccommodationTypeSearchObjects($_accommodationTypeSearchObjects)
    {
        return ($this->AccommodationTypeSearchObjects = $_accommodationTypeSearchObjects);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAccommodationTypeSearch
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
