<?php
/**
 * File for class NewyseServiceStructResourceAdditionContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructResourceAdditionContainer originally named ResourceAdditionContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructResourceAdditionContainer extends NewyseServiceWsdlClass
{
    /**
     * The ResourceAdditions
     * @var NewyseServiceStructResourceAdditions
     */
    public $ResourceAdditions;
    /**
     * Constructor method for ResourceAdditionContainer
     * @see parent::__construct()
     * @param NewyseServiceStructResourceAdditions $_resourceAdditions
     * @return NewyseServiceStructResourceAdditionContainer
     */
    public function __construct($_resourceAdditions = NULL)
    {
        parent::__construct(array('ResourceAdditions'=>$_resourceAdditions),false);
    }
    /**
     * Get ResourceAdditions value
     * @return NewyseServiceStructResourceAdditions|null
     */
    public function getResourceAdditions()
    {
        return $this->ResourceAdditions;
    }
    /**
     * Set ResourceAdditions value
     * @param NewyseServiceStructResourceAdditions $_resourceAdditions the ResourceAdditions
     * @return NewyseServiceStructResourceAdditions
     */
    public function setResourceAdditions($_resourceAdditions)
    {
        return ($this->ResourceAdditions = $_resourceAdditions);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructResourceAdditionContainer
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
