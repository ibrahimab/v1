<?php
/**
 * File for class NewyseServiceStructFacility
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructFacility originally named Facility
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructFacility extends NewyseServiceWsdlClass
{
    /**
     * The FacilityId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $FacilityId;
    /**
     * The Priority
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $Priority;
    /**
     * The Code
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Code;
    /**
     * The Language
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Language;
    /**
     * The Name
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Name;
    /**
     * The Description
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Description;
    /**
     * The ImageURL
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $ImageURL;
    /**
     * The AddressmanagerId
     * @var long
     */
    public $AddressmanagerId;
    /**
     * The Resorts
     * @var NewyseServiceStructResorts
     */
    public $Resorts;
    /**
     * The OpeningTimes
     * @var NewyseServiceStructOpeningTimes
     */
    public $OpeningTimes;
    /**
     * Constructor method for Facility
     * @see parent::__construct()
     * @param long $_facilityId
     * @param long $_priority
     * @param string $_code
     * @param string $_language
     * @param string $_name
     * @param string $_description
     * @param string $_imageURL
     * @param long $_addressmanagerId
     * @param NewyseServiceStructResorts $_resorts
     * @param NewyseServiceStructOpeningTimes $_openingTimes
     * @return NewyseServiceStructFacility
     */
    public function __construct($_facilityId = NULL,$_priority = NULL,$_code = NULL,$_language = NULL,$_name = NULL,$_description = NULL,$_imageURL = NULL,$_addressmanagerId = NULL,$_resorts = NULL,$_openingTimes = NULL)
    {
        parent::__construct(array('FacilityId'=>$_facilityId,'Priority'=>$_priority,'Code'=>$_code,'Language'=>$_language,'Name'=>$_name,'Description'=>$_description,'ImageURL'=>$_imageURL,'AddressmanagerId'=>$_addressmanagerId,'Resorts'=>$_resorts,'OpeningTimes'=>$_openingTimes),false);
    }
    /**
     * Get FacilityId value
     * @return long|null
     */
    public function getFacilityId()
    {
        return $this->FacilityId;
    }
    /**
     * Set FacilityId value
     * @param long $_facilityId the FacilityId
     * @return long
     */
    public function setFacilityId($_facilityId)
    {
        return ($this->FacilityId = $_facilityId);
    }
    /**
     * Get Priority value
     * @return long|null
     */
    public function getPriority()
    {
        return $this->Priority;
    }
    /**
     * Set Priority value
     * @param long $_priority the Priority
     * @return long
     */
    public function setPriority($_priority)
    {
        return ($this->Priority = $_priority);
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
     * Get Language value
     * @return string|null
     */
    public function getLanguage()
    {
        return $this->Language;
    }
    /**
     * Set Language value
     * @param string $_language the Language
     * @return string
     */
    public function setLanguage($_language)
    {
        return ($this->Language = $_language);
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
     * Get ImageURL value
     * @return string|null
     */
    public function getImageURL()
    {
        return $this->ImageURL;
    }
    /**
     * Set ImageURL value
     * @param string $_imageURL the ImageURL
     * @return string
     */
    public function setImageURL($_imageURL)
    {
        return ($this->ImageURL = $_imageURL);
    }
    /**
     * Get AddressmanagerId value
     * @return long|null
     */
    public function getAddressmanagerId()
    {
        return $this->AddressmanagerId;
    }
    /**
     * Set AddressmanagerId value
     * @param long $_addressmanagerId the AddressmanagerId
     * @return long
     */
    public function setAddressmanagerId($_addressmanagerId)
    {
        return ($this->AddressmanagerId = $_addressmanagerId);
    }
    /**
     * Get Resorts value
     * @return NewyseServiceStructResorts|null
     */
    public function getResorts()
    {
        return $this->Resorts;
    }
    /**
     * Set Resorts value
     * @param NewyseServiceStructResorts $_resorts the Resorts
     * @return NewyseServiceStructResorts
     */
    public function setResorts($_resorts)
    {
        return ($this->Resorts = $_resorts);
    }
    /**
     * Get OpeningTimes value
     * @return NewyseServiceStructOpeningTimes|null
     */
    public function getOpeningTimes()
    {
        return $this->OpeningTimes;
    }
    /**
     * Set OpeningTimes value
     * @param NewyseServiceStructOpeningTimes $_openingTimes the OpeningTimes
     * @return NewyseServiceStructOpeningTimes
     */
    public function setOpeningTimes($_openingTimes)
    {
        return ($this->OpeningTimes = $_openingTimes);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructFacility
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
