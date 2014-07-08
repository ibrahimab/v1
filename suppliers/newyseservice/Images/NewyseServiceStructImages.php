<?php
/**
 * File for class NewyseServiceStructImages
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructImages originally named Images
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructImages extends NewyseServiceWsdlClass
{
    /**
     * The ImageItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructImage
     */
    public $ImageItem;
    /**
     * Constructor method for Images
     * @see parent::__construct()
     * @param NewyseServiceStructImage $_imageItem
     * @return NewyseServiceStructImages
     */
    public function __construct($_imageItem = NULL)
    {
        parent::__construct(array('ImageItem'=>$_imageItem),false);
    }
    /**
     * Get ImageItem value
     * @return NewyseServiceStructImage|null
     */
    public function getImageItem()
    {
        return $this->ImageItem;
    }
    /**
     * Set ImageItem value
     * @param NewyseServiceStructImage $_imageItem the ImageItem
     * @return NewyseServiceStructImage
     */
    public function setImageItem($_imageItem)
    {
        return ($this->ImageItem = $_imageItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructImages
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
