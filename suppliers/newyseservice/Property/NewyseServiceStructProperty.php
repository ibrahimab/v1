<?php
/**
 * File for class NewyseServiceStructProperty
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructProperty originally named Property
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructProperty extends NewyseServiceWsdlClass
{
    /**
     * The PropertydefId
     * @var long
     */
    public $PropertydefId;
    /**
     * The PropertyManagerId
     * @var long
     */
    public $PropertyManagerId;
    /**
     * The Code
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
     * The Description
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Description;
    /**
     * The GroupCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $GroupCode;
    /**
     * The GroupName
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $GroupName;
    /**
     * The Partial
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var boolean
     */
    public $Partial;
    /**
     * The HasPreferenceCosts
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var boolean
     */
    public $HasPreferenceCosts;
    /**
     * The Value
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Value;
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
     * Constructor method for Property
     * @see parent::__construct()
     * @param long $_propertydefId
     * @param long $_propertyManagerId
     * @param string $_code
     * @param string $_name
     * @param string $_description
     * @param string $_groupCode
     * @param string $_groupName
     * @param boolean $_partial
     * @param boolean $_hasPreferenceCosts
     * @param string $_value
     * @param dateTime $_startDate
     * @param dateTime $_endDate
     * @return NewyseServiceStructProperty
     */
    public function __construct($_propertydefId = NULL,$_propertyManagerId = NULL,$_code = NULL,$_name = NULL,$_description = NULL,$_groupCode = NULL,$_groupName = NULL,$_partial = NULL,$_hasPreferenceCosts = NULL,$_value = NULL,$_startDate = NULL,$_endDate = NULL)
    {
        parent::__construct(array('PropertydefId'=>$_propertydefId,'PropertyManagerId'=>$_propertyManagerId,'Code'=>$_code,'Name'=>$_name,'Description'=>$_description,'GroupCode'=>$_groupCode,'GroupName'=>$_groupName,'Partial'=>$_partial,'HasPreferenceCosts'=>$_hasPreferenceCosts,'Value'=>$_value,'StartDate'=>$_startDate,'EndDate'=>$_endDate),false);
    }
    /**
     * Get PropertydefId value
     * @return long|null
     */
    public function getPropertydefId()
    {
        return $this->PropertydefId;
    }
    /**
     * Set PropertydefId value
     * @param long $_propertydefId the PropertydefId
     * @return long
     */
    public function setPropertydefId($_propertydefId)
    {
        return ($this->PropertydefId = $_propertydefId);
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
     * Get GroupCode value
     * @return string|null
     */
    public function getGroupCode()
    {
        return $this->GroupCode;
    }
    /**
     * Set GroupCode value
     * @param string $_groupCode the GroupCode
     * @return string
     */
    public function setGroupCode($_groupCode)
    {
        return ($this->GroupCode = $_groupCode);
    }
    /**
     * Get GroupName value
     * @return string|null
     */
    public function getGroupName()
    {
        return $this->GroupName;
    }
    /**
     * Set GroupName value
     * @param string $_groupName the GroupName
     * @return string
     */
    public function setGroupName($_groupName)
    {
        return ($this->GroupName = $_groupName);
    }
    /**
     * Get Partial value
     * @return boolean|null
     */
    public function getPartial()
    {
        return $this->Partial;
    }
    /**
     * Set Partial value
     * @param boolean $_partial the Partial
     * @return boolean
     */
    public function setPartial($_partial)
    {
        return ($this->Partial = $_partial);
    }
    /**
     * Get HasPreferenceCosts value
     * @return boolean|null
     */
    public function getHasPreferenceCosts()
    {
        return $this->HasPreferenceCosts;
    }
    /**
     * Set HasPreferenceCosts value
     * @param boolean $_hasPreferenceCosts the HasPreferenceCosts
     * @return boolean
     */
    public function setHasPreferenceCosts($_hasPreferenceCosts)
    {
        return ($this->HasPreferenceCosts = $_hasPreferenceCosts);
    }
    /**
     * Get Value value
     * @return string|null
     */
    public function getValue()
    {
        return $this->Value;
    }
    /**
     * Set Value value
     * @param string $_value the Value
     * @return string
     */
    public function setValue($_value)
    {
        return ($this->Value = $_value);
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
     * @return NewyseServiceStructProperty
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
