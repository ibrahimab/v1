<?php
/**
 * File for class NewyseServiceStructBrochure
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructBrochure originally named Brochure
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructBrochure extends NewyseServiceWsdlClass
{
    /**
     * The BrochureId
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var long
     */
    public $BrochureId;
    /**
     * The Code
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var string
     */
    public $Code;
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
     * The ResortCode
     * @var string
     */
    public $ResortCode;
    /**
     * Constructor method for Brochure
     * @see parent::__construct()
     * @param long $_brochureId
     * @param string $_code
     * @param string $_name
     * @param string $_description
     * @param string $_resortCode
     * @return NewyseServiceStructBrochure
     */
    public function __construct($_brochureId = NULL,$_code = NULL,$_name = NULL,$_description = NULL,$_resortCode = NULL)
    {
        parent::__construct(array('BrochureId'=>$_brochureId,'Code'=>$_code,'Name'=>$_name,'Description'=>$_description,'ResortCode'=>$_resortCode),false);
    }
    /**
     * Get BrochureId value
     * @return long|null
     */
    public function getBrochureId()
    {
        return $this->BrochureId;
    }
    /**
     * Set BrochureId value
     * @param long $_brochureId the BrochureId
     * @return long
     */
    public function setBrochureId($_brochureId)
    {
        return ($this->BrochureId = $_brochureId);
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
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructBrochure
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
