<?php
/**
 * File for class NewyseServiceStructPincodeItems
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructPincodeItems originally named PincodeItems
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructPincodeItems extends NewyseServiceWsdlClass
{
    /**
     * The PincodeItem
     * @var NewyseServiceStructPincodeItem
     */
    public $PincodeItem;
    /**
     * Constructor method for PincodeItems
     * @see parent::__construct()
     * @param NewyseServiceStructPincodeItem $_pincodeItem
     * @return NewyseServiceStructPincodeItems
     */
    public function __construct($_pincodeItem = NULL)
    {
        parent::__construct(array('PincodeItem'=>$_pincodeItem),false);
    }
    /**
     * Get PincodeItem value
     * @return NewyseServiceStructPincodeItem|null
     */
    public function getPincodeItem()
    {
        return $this->PincodeItem;
    }
    /**
     * Set PincodeItem value
     * @param NewyseServiceStructPincodeItem $_pincodeItem the PincodeItem
     * @return NewyseServiceStructPincodeItem
     */
    public function setPincodeItem($_pincodeItem)
    {
        return ($this->PincodeItem = $_pincodeItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructPincodeItems
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
