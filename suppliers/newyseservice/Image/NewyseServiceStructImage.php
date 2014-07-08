<?php
/**
 * File for class NewyseServiceStructImage
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructImage originally named Image
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructImage extends NewyseServiceWsdlClass
{
    /**
     * The ImageId
     * @var long
     */
    public $ImageId;
    /**
     * The ImageType
     * @var string
     */
    public $ImageType;
    /**
     * The ImageData
     * @var base64Binary
     */
    public $ImageData;
    /**
     * The MimeType
     * @var string
     */
    public $MimeType;
    /**
     * The Filename
     * @var string
     */
    public $Filename;
    /**
     * The ImagemanagerId
     * @var long
     */
    public $ImagemanagerId;
    /**
     * The DefaultImage
     * @var boolean
     */
    public $DefaultImage;
    /**
     * The ImageUrl
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $ImageUrl;
    /**
     * Constructor method for Image
     * @see parent::__construct()
     * @param long $_imageId
     * @param string $_imageType
     * @param base64Binary $_imageData
     * @param string $_mimeType
     * @param string $_filename
     * @param long $_imagemanagerId
     * @param boolean $_defaultImage
     * @param string $_imageUrl
     * @return NewyseServiceStructImage
     */
    public function __construct($_imageId = NULL,$_imageType = NULL,$_imageData = NULL,$_mimeType = NULL,$_filename = NULL,$_imagemanagerId = NULL,$_defaultImage = NULL,$_imageUrl = NULL)
    {
        parent::__construct(array('ImageId'=>$_imageId,'ImageType'=>$_imageType,'ImageData'=>$_imageData,'MimeType'=>$_mimeType,'Filename'=>$_filename,'ImagemanagerId'=>$_imagemanagerId,'DefaultImage'=>$_defaultImage,'ImageUrl'=>$_imageUrl),false);
    }
    /**
     * Get ImageId value
     * @return long|null
     */
    public function getImageId()
    {
        return $this->ImageId;
    }
    /**
     * Set ImageId value
     * @param long $_imageId the ImageId
     * @return long
     */
    public function setImageId($_imageId)
    {
        return ($this->ImageId = $_imageId);
    }
    /**
     * Get ImageType value
     * @return string|null
     */
    public function getImageType()
    {
        return $this->ImageType;
    }
    /**
     * Set ImageType value
     * @param string $_imageType the ImageType
     * @return string
     */
    public function setImageType($_imageType)
    {
        return ($this->ImageType = $_imageType);
    }
    /**
     * Get ImageData value
     * @return base64Binary|null
     */
    public function getImageData()
    {
        return $this->ImageData;
    }
    /**
     * Set ImageData value
     * @param base64Binary $_imageData the ImageData
     * @return base64Binary
     */
    public function setImageData($_imageData)
    {
        return ($this->ImageData = $_imageData);
    }
    /**
     * Get MimeType value
     * @return string|null
     */
    public function getMimeType()
    {
        return $this->MimeType;
    }
    /**
     * Set MimeType value
     * @param string $_mimeType the MimeType
     * @return string
     */
    public function setMimeType($_mimeType)
    {
        return ($this->MimeType = $_mimeType);
    }
    /**
     * Get Filename value
     * @return string|null
     */
    public function getFilename()
    {
        return $this->Filename;
    }
    /**
     * Set Filename value
     * @param string $_filename the Filename
     * @return string
     */
    public function setFilename($_filename)
    {
        return ($this->Filename = $_filename);
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
     * Get DefaultImage value
     * @return boolean|null
     */
    public function getDefaultImage()
    {
        return $this->DefaultImage;
    }
    /**
     * Set DefaultImage value
     * @param boolean $_defaultImage the DefaultImage
     * @return boolean
     */
    public function setDefaultImage($_defaultImage)
    {
        return ($this->DefaultImage = $_defaultImage);
    }
    /**
     * Get ImageUrl value
     * @return string|null
     */
    public function getImageUrl()
    {
        return $this->ImageUrl;
    }
    /**
     * Set ImageUrl value
     * @param string $_imageUrl the ImageUrl
     * @return string
     */
    public function setImageUrl($_imageUrl)
    {
        return ($this->ImageUrl = $_imageUrl);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructImage
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
