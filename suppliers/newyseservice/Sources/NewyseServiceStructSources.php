<?php
/**
 * File for class NewyseServiceStructSources
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructSources originally named Sources
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructSources extends NewyseServiceWsdlClass
{
    /**
     * The SourceItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructWSSource
     */
    public $SourceItem;
    /**
     * Constructor method for Sources
     * @see parent::__construct()
     * @param NewyseServiceStructWSSource $_sourceItem
     * @return NewyseServiceStructSources
     */
    public function __construct($_sourceItem = NULL)
    {
        parent::__construct(array('SourceItem'=>$_sourceItem),false);
    }
    /**
     * Get SourceItem value
     * @return NewyseServiceStructWSSource|null
     */
    public function getSourceItem()
    {
        return $this->SourceItem;
    }
    /**
     * Set SourceItem value
     * @param NewyseServiceStructWSSource $_sourceItem the SourceItem
     * @return NewyseServiceStructWSSource
     */
    public function setSourceItem($_sourceItem)
    {
        return ($this->SourceItem = $_sourceItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructSources
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
