<?php
/**
 * File for class NewyseServiceStructResort
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructResort originally named Resort
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructResort extends NewyseServiceWsdlClass
{
    /**
     * The ResortId
     * @var long
     */
    public $ResortId;
    /**
     * The ResortParentId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $ResortParentId;
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
     * The VisitaddressManagerId
     * @var long
     */
    public $VisitaddressManagerId;
    /**
     * The MailaddressManagerId
     * @var long
     */
    public $MailaddressManagerId;
    /**
     * The ImagemanagerId
     * @var long
     */
    public $ImagemanagerId;
    /**
     * The PropertyManagerId
     * @var long
     */
    public $PropertyManagerId;
    /**
     * The Language
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Language;
    /**
     * Constructor method for Resort
     * @see parent::__construct()
     * @param long $_resortId
     * @param long $_resortParentId
     * @param string $_code
     * @param string $_name
     * @param string $_shortDescription
     * @param string $_description
     * @param long $_visitaddressManagerId
     * @param long $_mailaddressManagerId
     * @param long $_imagemanagerId
     * @param long $_propertyManagerId
     * @param string $_language
     * @return NewyseServiceStructResort
     */
    public function __construct($_resortId = NULL,$_resortParentId = NULL,$_code = NULL,$_name = NULL,$_shortDescription = NULL,$_description = NULL,$_visitaddressManagerId = NULL,$_mailaddressManagerId = NULL,$_imagemanagerId = NULL,$_propertyManagerId = NULL,$_language = NULL)
    {
        parent::__construct(array('ResortId'=>$_resortId,'ResortParentId'=>$_resortParentId,'Code'=>$_code,'Name'=>$_name,'ShortDescription'=>$_shortDescription,'Description'=>$_description,'VisitaddressManagerId'=>$_visitaddressManagerId,'MailaddressManagerId'=>$_mailaddressManagerId,'ImagemanagerId'=>$_imagemanagerId,'PropertyManagerId'=>$_propertyManagerId,'Language'=>$_language),false);
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
     * Get ResortParentId value
     * @return long|null
     */
    public function getResortParentId()
    {
        return $this->ResortParentId;
    }
    /**
     * Set ResortParentId value
     * @param long $_resortParentId the ResortParentId
     * @return long
     */
    public function setResortParentId($_resortParentId)
    {
        return ($this->ResortParentId = $_resortParentId);
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
     * Get VisitaddressManagerId value
     * @return long|null
     */
    public function getVisitaddressManagerId()
    {
        return $this->VisitaddressManagerId;
    }
    /**
     * Set VisitaddressManagerId value
     * @param long $_visitaddressManagerId the VisitaddressManagerId
     * @return long
     */
    public function setVisitaddressManagerId($_visitaddressManagerId)
    {
        return ($this->VisitaddressManagerId = $_visitaddressManagerId);
    }
    /**
     * Get MailaddressManagerId value
     * @return long|null
     */
    public function getMailaddressManagerId()
    {
        return $this->MailaddressManagerId;
    }
    /**
     * Set MailaddressManagerId value
     * @param long $_mailaddressManagerId the MailaddressManagerId
     * @return long
     */
    public function setMailaddressManagerId($_mailaddressManagerId)
    {
        return ($this->MailaddressManagerId = $_mailaddressManagerId);
    }
    /**
     * Get ImagemanagerId value
     * @return long|null
     */
    public function getImagemanagerId()
    {
        return $this->ImagemanagerId;
    }
    /**
     * Set ImagemanagerId value
     * @param long $_imagemanagerId the ImagemanagerId
     * @return long
     */
    public function setImagemanagerId($_imagemanagerId)
    {
        return ($this->ImagemanagerId = $_imagemanagerId);
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
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructResort
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
