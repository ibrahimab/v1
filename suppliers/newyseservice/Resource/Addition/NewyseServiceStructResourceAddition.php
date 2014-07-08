<?php
/**
 * File for class NewyseServiceStructResourceAddition
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructResourceAddition originally named ResourceAddition
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructResourceAddition extends NewyseServiceWsdlClass
{
    /**
     * The ResourceId
     * @var long
     */
    public $ResourceId;
    /**
     * The ResortId
     * @var long
     */
    public $ResortId;
    /**
     * The Type
     * @var string
     */
    public $Type;
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
     * The MaxQuantity
     * @var int
     */
    public $MaxQuantity;
    /**
     * The MaxReservable
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var int
     */
    public $MaxReservable;
    /**
     * The MinQuantity
     * @var int
     */
    public $MinQuantity;
    /**
     * The ImageManagerId
     * @var long
     */
    public $ImageManagerId;
    /**
     * Constructor method for ResourceAddition
     * @see parent::__construct()
     * @param long $_resourceId
     * @param long $_resortId
     * @param string $_type
     * @param string $_code
     * @param string $_name
     * @param string $_shortDescription
     * @param string $_description
     * @param int $_maxQuantity
     * @param int $_maxReservable
     * @param int $_minQuantity
     * @param long $_imageManagerId
     * @return NewyseServiceStructResourceAddition
     */
    public function __construct($_resourceId = NULL,$_resortId = NULL,$_type = NULL,$_code = NULL,$_name = NULL,$_shortDescription = NULL,$_description = NULL,$_maxQuantity = NULL,$_maxReservable = NULL,$_minQuantity = NULL,$_imageManagerId = NULL)
    {
        parent::__construct(array('ResourceId'=>$_resourceId,'ResortId'=>$_resortId,'Type'=>$_type,'Code'=>$_code,'Name'=>$_name,'ShortDescription'=>$_shortDescription,'Description'=>$_description,'MaxQuantity'=>$_maxQuantity,'MaxReservable'=>$_maxReservable,'MinQuantity'=>$_minQuantity,'ImageManagerId'=>$_imageManagerId),false);
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
     * Get Type value
     * @return string|null
     */
    public function getType()
    {
        return $this->Type;
    }
    /**
     * Set Type value
     * @param string $_type the Type
     * @return string
     */
    public function setType($_type)
    {
        return ($this->Type = $_type);
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
     * Get MaxQuantity value
     * @return int|null
     */
    public function getMaxQuantity()
    {
        return $this->MaxQuantity;
    }
    /**
     * Set MaxQuantity value
     * @param int $_maxQuantity the MaxQuantity
     * @return int
     */
    public function setMaxQuantity($_maxQuantity)
    {
        return ($this->MaxQuantity = $_maxQuantity);
    }
    /**
     * Get MaxReservable value
     * @return int|null
     */
    public function getMaxReservable()
    {
        return $this->MaxReservable;
    }
    /**
     * Set MaxReservable value
     * @param int $_maxReservable the MaxReservable
     * @return int
     */
    public function setMaxReservable($_maxReservable)
    {
        return ($this->MaxReservable = $_maxReservable);
    }
    /**
     * Get MinQuantity value
     * @return int|null
     */
    public function getMinQuantity()
    {
        return $this->MinQuantity;
    }
    /**
     * Set MinQuantity value
     * @param int $_minQuantity the MinQuantity
     * @return int
     */
    public function setMinQuantity($_minQuantity)
    {
        return ($this->MinQuantity = $_minQuantity);
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
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructResourceAddition
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
