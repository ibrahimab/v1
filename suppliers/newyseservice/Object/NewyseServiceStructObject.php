<?php
/**
 * File for class NewyseServiceStructObject
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructObject originally named Object
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructObject extends NewyseServiceWsdlClass
{
    /**
     * The ObjectId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $ObjectId;
    /**
     * The ResourceId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $ResourceId;
    /**
     * The Code
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Code;
    /**
     * The Name
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Name;
    /**
     * The ShortDescription
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $ShortDescription;
    /**
     * The Description
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Description;
    /**
     * The PropertyManagerId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $PropertyManagerId;
    /**
     * The ImageManagerId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $ImageManagerId;
    /**
     * The Status
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var NewyseServiceEnumObjectCleaningStatus
     */
    public $Status;
    /**
     * Constructor method for Object
     * @see parent::__construct()
     * @param long $_objectId
     * @param long $_resourceId
     * @param string $_code
     * @param string $_name
     * @param string $_shortDescription
     * @param string $_description
     * @param long $_propertyManagerId
     * @param long $_imageManagerId
     * @param NewyseServiceEnumObjectCleaningStatus $_status
     * @return NewyseServiceStructObject
     */
    public function __construct($_objectId = NULL,$_resourceId = NULL,$_code = NULL,$_name = NULL,$_shortDescription = NULL,$_description = NULL,$_propertyManagerId = NULL,$_imageManagerId = NULL,$_status = NULL)
    {
        parent::__construct(array('ObjectId'=>$_objectId,'ResourceId'=>$_resourceId,'Code'=>$_code,'Name'=>$_name,'ShortDescription'=>$_shortDescription,'Description'=>$_description,'PropertyManagerId'=>$_propertyManagerId,'ImageManagerId'=>$_imageManagerId,'Status'=>$_status),false);
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
     * Get Status value
     * @return NewyseServiceEnumObjectCleaningStatus|null
     */
    public function getStatus()
    {
        return $this->Status;
    }
    /**
     * Set Status value
     * @uses NewyseServiceEnumObjectCleaningStatus::valueIsValid()
     * @param NewyseServiceEnumObjectCleaningStatus $_status the Status
     * @return NewyseServiceEnumObjectCleaningStatus
     */
    public function setStatus($_status)
    {
        if(!NewyseServiceEnumObjectCleaningStatus::valueIsValid($_status))
        {
            return false;
        }
        return ($this->Status = $_status);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructObject
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
