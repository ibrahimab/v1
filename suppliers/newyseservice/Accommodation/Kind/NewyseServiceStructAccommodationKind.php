<?php
/**
 * File for class NewyseServiceStructAccommodationKind
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAccommodationKind originally named AccommodationKind
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAccommodationKind extends NewyseServiceWsdlClass
{
    /**
     * The AccommodationKindId
     * @var long
     */
    public $AccommodationKindId;
    /**
     * The Code
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Code;
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
     * Constructor method for AccommodationKind
     * @see parent::__construct()
     * @param long $_accommodationKindId
     * @param string $_code
     * @param string $_name
     * @param string $_description
     * @return NewyseServiceStructAccommodationKind
     */
    public function __construct($_accommodationKindId = NULL,$_code = NULL,$_name = NULL,$_description = NULL)
    {
        parent::__construct(array('AccommodationKindId'=>$_accommodationKindId,'Code'=>$_code,'Name'=>$_name,'Description'=>$_description),false);
    }
    /**
     * Get AccommodationKindId value
     * @return long|null
     */
    public function getAccommodationKindId()
    {
        return $this->AccommodationKindId;
    }
    /**
     * Set AccommodationKindId value
     * @param long $_accommodationKindId the AccommodationKindId
     * @return long
     */
    public function setAccommodationKindId($_accommodationKindId)
    {
        return ($this->AccommodationKindId = $_accommodationKindId);
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
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAccommodationKind
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
