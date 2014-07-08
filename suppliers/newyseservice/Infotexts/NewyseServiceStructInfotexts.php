<?php
/**
 * File for class NewyseServiceStructInfotexts
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructInfotexts originally named Infotexts
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructInfotexts extends NewyseServiceWsdlClass
{
    /**
     * The InfotextItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var string
     */
    public $InfotextItem;
    /**
     * Constructor method for Infotexts
     * @see parent::__construct()
     * @param string $_infotextItem
     * @return NewyseServiceStructInfotexts
     */
    public function __construct($_infotextItem = NULL)
    {
        parent::__construct(array('InfotextItem'=>$_infotextItem),false);
    }
    /**
     * Get InfotextItem value
     * @return string|null
     */
    public function getInfotextItem()
    {
        return $this->InfotextItem;
    }
    /**
     * Set InfotextItem value
     * @param string $_infotextItem the InfotextItem
     * @return string
     */
    public function setInfotextItem($_infotextItem)
    {
        return ($this->InfotextItem = $_infotextItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructInfotexts
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
