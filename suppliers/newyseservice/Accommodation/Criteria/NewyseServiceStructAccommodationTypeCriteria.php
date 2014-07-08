<?php
/**
 * File for class NewyseServiceStructAccommodationTypeCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAccommodationTypeCriteria originally named AccommodationTypeCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAccommodationTypeCriteria extends NewyseServiceWsdlClass
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
     * The ResortCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $ResortCode;
    /**
     * The OnlyBookable
     * Meta informations extracted from the WSDL
     * - default : true
     * - minOccurs : 0
     * - nillable : true
     * @var boolean
     */
    public $OnlyBookable;
    /**
     * Constructor method for AccommodationTypeCriteria
     * @see parent::__construct()
     * @param long $_resourceId
     * @param string $_resortCode
     * @param boolean $_onlyBookable
     * @return NewyseServiceStructAccommodationTypeCriteria
     */
    public function __construct($_resourceId = NULL,$_resortCode = NULL,$_onlyBookable = true)
    {
        parent::__construct(array('ResourceId'=>$_resourceId,'ResortCode'=>$_resortCode,'OnlyBookable'=>$_onlyBookable),false);
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
     * Get OnlyBookable value
     * @return boolean|null
     */
    public function getOnlyBookable()
    {
        return $this->OnlyBookable;
    }
    /**
     * Set OnlyBookable value
     * @param boolean $_onlyBookable the OnlyBookable
     * @return boolean
     */
    public function setOnlyBookable($_onlyBookable)
    {
        return ($this->OnlyBookable = $_onlyBookable);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAccommodationTypeCriteria
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
