<?php
/**
 * File for class NewyseServiceStructObjects
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructObjects originally named Objects
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructObjects extends NewyseServiceWsdlClass
{
    /**
     * The ObjectItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructObject
     */
    public $ObjectItem;
    /**
     * Constructor method for Objects
     * @see parent::__construct()
     * @param NewyseServiceStructObject $_objectItem
     * @return NewyseServiceStructObjects
     */
    public function __construct($_objectItem = NULL)
    {
        parent::__construct(array('ObjectItem'=>$_objectItem),false);
    }
    /**
     * Get ObjectItem value
     * @return NewyseServiceStructObject|null
     */
    public function getObjectItem()
    {
        return $this->ObjectItem;
    }
    /**
     * Set ObjectItem value
     * @param NewyseServiceStructObject $_objectItem the ObjectItem
     * @return NewyseServiceStructObject
     */
    public function setObjectItem($_objectItem)
    {
        return ($this->ObjectItem = $_objectItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructObjects
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
