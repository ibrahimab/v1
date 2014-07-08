<?php
/**
 * File for class NewyseServiceStructImageContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructImageContainer originally named ImageContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructImageContainer extends NewyseServiceWsdlClass
{
    /**
     * The Images
     * @var NewyseServiceStructImages
     */
    public $Images;
    /**
     * Constructor method for ImageContainer
     * @see parent::__construct()
     * @param NewyseServiceStructImages $_images
     * @return NewyseServiceStructImageContainer
     */
    public function __construct($_images = NULL)
    {
        parent::__construct(array('Images'=>$_images),false);
    }
    /**
     * Get Images value
     * @return NewyseServiceStructImages|null
     */
    public function getImages()
    {
        return $this->Images;
    }
    /**
     * Set Images value
     * @param NewyseServiceStructImages $_images the Images
     * @return NewyseServiceStructImages
     */
    public function setImages($_images)
    {
        return ($this->Images = $_images);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructImageContainer
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
