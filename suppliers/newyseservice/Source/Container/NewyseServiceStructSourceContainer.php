<?php
/**
 * File for class NewyseServiceStructSourceContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructSourceContainer originally named SourceContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructSourceContainer extends NewyseServiceWsdlClass
{
    /**
     * The Sources
     * @var NewyseServiceStructSources
     */
    public $Sources;
    /**
     * Constructor method for SourceContainer
     * @see parent::__construct()
     * @param NewyseServiceStructSources $_sources
     * @return NewyseServiceStructSourceContainer
     */
    public function __construct($_sources = NULL)
    {
        parent::__construct(array('Sources'=>$_sources),false);
    }
    /**
     * Get Sources value
     * @return NewyseServiceStructSources|null
     */
    public function getSources()
    {
        return $this->Sources;
    }
    /**
     * Set Sources value
     * @param NewyseServiceStructSources $_sources the Sources
     * @return NewyseServiceStructSources
     */
    public function setSources($_sources)
    {
        return ($this->Sources = $_sources);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructSourceContainer
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
