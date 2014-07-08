<?php
/**
 * File for class NewyseServiceStructObjectContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructObjectContainer originally named ObjectContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructObjectContainer extends NewyseServiceWsdlClass
{
    /**
     * The Objects
     * @var NewyseServiceStructObjects
     */
    public $Objects;
    /**
     * Constructor method for ObjectContainer
     * @see parent::__construct()
     * @param NewyseServiceStructObjects $_objects
     * @return NewyseServiceStructObjectContainer
     */
    public function __construct($_objects = NULL)
    {
        parent::__construct(array('Objects'=>$_objects),false);
    }
    /**
     * Get Objects value
     * @return NewyseServiceStructObjects|null
     */
    public function getObjects()
    {
        return $this->Objects;
    }
    /**
     * Set Objects value
     * @param NewyseServiceStructObjects $_objects the Objects
     * @return NewyseServiceStructObjects
     */
    public function setObjects($_objects)
    {
        return ($this->Objects = $_objects);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructObjectContainer
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
