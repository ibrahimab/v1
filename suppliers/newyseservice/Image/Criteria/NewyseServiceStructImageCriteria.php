<?php
/**
 * File for class NewyseServiceStructImageCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructImageCriteria originally named ImageCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructImageCriteria extends NewyseServiceWsdlClass
{
    /**
     * The ImageManagerId
     * @var long
     */
    public $ImageManagerId;
    /**
     * The OnlyImageUrl
     * Meta informations extracted from the WSDL
     * - default : false
     * - minOccurs : 0
     * - nillable : true
     * @var boolean
     */
    public $OnlyImageUrl;
    /**
     * Constructor method for ImageCriteria
     * @see parent::__construct()
     * @param long $_imageManagerId
     * @param boolean $_onlyImageUrl
     * @return NewyseServiceStructImageCriteria
     */
    public function __construct($_imageManagerId = NULL,$_onlyImageUrl = false)
    {
        parent::__construct(array('ImageManagerId'=>$_imageManagerId,'OnlyImageUrl'=>$_onlyImageUrl),false);
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
     * Get OnlyImageUrl value
     * @return boolean|null
     */
    public function getOnlyImageUrl()
    {
        return $this->OnlyImageUrl;
    }
    /**
     * Set OnlyImageUrl value
     * @param boolean $_onlyImageUrl the OnlyImageUrl
     * @return boolean
     */
    public function setOnlyImageUrl($_onlyImageUrl)
    {
        return ($this->OnlyImageUrl = $_onlyImageUrl);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructImageCriteria
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
