<?php
/**
 * File for class NewyseServiceStructTipTripCategory
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructTipTripCategory originally named TipTripCategory
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructTipTripCategory extends NewyseServiceWsdlClass
{
    /**
     * The TipsTripsCategoryId
     * @var long
     */
    public $TipsTripsCategoryId;
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
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Name;
    /**
     * Constructor method for TipTripCategory
     * @see parent::__construct()
     * @param long $_tipsTripsCategoryId
     * @param string $_code
     * @param string $_name
     * @return NewyseServiceStructTipTripCategory
     */
    public function __construct($_tipsTripsCategoryId = NULL,$_code = NULL,$_name = NULL)
    {
        parent::__construct(array('TipsTripsCategoryId'=>$_tipsTripsCategoryId,'Code'=>$_code,'Name'=>$_name),false);
    }
    /**
     * Get TipsTripsCategoryId value
     * @return long|null
     */
    public function getTipsTripsCategoryId()
    {
        return $this->TipsTripsCategoryId;
    }
    /**
     * Set TipsTripsCategoryId value
     * @param long $_tipsTripsCategoryId the TipsTripsCategoryId
     * @return long
     */
    public function setTipsTripsCategoryId($_tipsTripsCategoryId)
    {
        return ($this->TipsTripsCategoryId = $_tipsTripsCategoryId);
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
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructTipTripCategory
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
