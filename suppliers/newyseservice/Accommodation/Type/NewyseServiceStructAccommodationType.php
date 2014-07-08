<?php
/**
 * File for class NewyseServiceStructAccommodationType
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAccommodationType originally named AccommodationType
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAccommodationType extends NewyseServiceWsdlClass
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
     * The Description2
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Description2;
    /**
     * The NumberOfPersons
     * @var int
     */
    public $NumberOfPersons;
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
     * The ResortCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $ResortCode;
    /**
     * The KindId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $KindId;
    /**
     * The KindCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $KindCode;
    /**
     * Constructor method for AccommodationType
     * @see parent::__construct()
     * @param long $_resourceId
     * @param string $_code
     * @param string $_name
     * @param string $_shortDescription
     * @param string $_description
     * @param string $_description2
     * @param int $_numberOfPersons
     * @param long $_propertyManagerId
     * @param long $_imageManagerId
     * @param string $_resortCode
     * @param long $_kindId
     * @param string $_kindCode
     * @return NewyseServiceStructAccommodationType
     */
    public function __construct($_resourceId = NULL,$_code = NULL,$_name = NULL,$_shortDescription = NULL,$_description = NULL,$_description2 = NULL,$_numberOfPersons = NULL,$_propertyManagerId = NULL,$_imageManagerId = NULL,$_resortCode = NULL,$_kindId = NULL,$_kindCode = NULL)
    {
        parent::__construct(array('ResourceId'=>$_resourceId,'Code'=>$_code,'Name'=>$_name,'ShortDescription'=>$_shortDescription,'Description'=>$_description,'Description2'=>$_description2,'NumberOfPersons'=>$_numberOfPersons,'PropertyManagerId'=>$_propertyManagerId,'ImageManagerId'=>$_imageManagerId,'ResortCode'=>$_resortCode,'KindId'=>$_kindId,'KindCode'=>$_kindCode),false);
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
     * Get NumberOfPersons value
     * @return int|null
     */
    public function getNumberOfPersons()
    {
        return $this->NumberOfPersons;
    }
    /**
     * Set NumberOfPersons value
     * @param int $_numberOfPersons the NumberOfPersons
     * @return int
     */
    public function setNumberOfPersons($_numberOfPersons)
    {
        return ($this->NumberOfPersons = $_numberOfPersons);
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
     * Get KindId value
     * @return long|null
     */
    public function getKindId()
    {
        return $this->KindId;
    }
    /**
     * Set KindId value
     * @param long $_kindId the KindId
     * @return long
     */
    public function setKindId($_kindId)
    {
        return ($this->KindId = $_kindId);
    }
    /**
     * Get KindCode value
     * @return string|null
     */
    public function getKindCode()
    {
        return $this->KindCode;
    }
    /**
     * Set KindCode value
     * @param string $_kindCode the KindCode
     * @return string
     */
    public function setKindCode($_kindCode)
    {
        return ($this->KindCode = $_kindCode);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAccommodationType
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
