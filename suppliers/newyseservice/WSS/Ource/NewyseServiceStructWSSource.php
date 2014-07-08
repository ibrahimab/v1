<?php
/**
 * File for class NewyseServiceStructWSSource
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructWSSource originally named WSSource
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructWSSource extends NewyseServiceWsdlClass
{
    /**
     * The SourceId
     * @var long
     */
    public $SourceId;
    /**
     * The CategoryId
     * @var long
     */
    public $CategoryId;
    /**
     * The Code
     * @var string
     */
    public $Code;
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
     * The Name
     * Meta informations extracted from the WSDL
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
     * Constructor method for WSSource
     * @see parent::__construct()
     * @param long $_sourceId
     * @param long $_categoryId
     * @param string $_code
     * @param dateTime $_startDate
     * @param dateTime $_endDate
     * @param string $_name
     * @param string $_description
     * @return NewyseServiceStructWSSource
     */
    public function __construct($_sourceId = NULL,$_categoryId = NULL,$_code = NULL,$_startDate = NULL,$_endDate = NULL,$_name = NULL,$_description = NULL)
    {
        parent::__construct(array('SourceId'=>$_sourceId,'CategoryId'=>$_categoryId,'Code'=>$_code,'StartDate'=>$_startDate,'EndDate'=>$_endDate,'Name'=>$_name,'Description'=>$_description),false);
    }
    /**
     * Get SourceId value
     * @return long|null
     */
    public function getSourceId()
    {
        return $this->SourceId;
    }
    /**
     * Set SourceId value
     * @param long $_sourceId the SourceId
     * @return long
     */
    public function setSourceId($_sourceId)
    {
        return ($this->SourceId = $_sourceId);
    }
    /**
     * Get CategoryId value
     * @return long|null
     */
    public function getCategoryId()
    {
        return $this->CategoryId;
    }
    /**
     * Set CategoryId value
     * @param long $_categoryId the CategoryId
     * @return long
     */
    public function setCategoryId($_categoryId)
    {
        return ($this->CategoryId = $_categoryId);
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
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructWSSource
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
