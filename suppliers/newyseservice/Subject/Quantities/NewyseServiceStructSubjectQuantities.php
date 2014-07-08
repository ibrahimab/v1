<?php
/**
 * File for class NewyseServiceStructSubjectQuantities
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructSubjectQuantities originally named SubjectQuantities
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructSubjectQuantities extends NewyseServiceWsdlClass
{
    /**
     * The SubjectQuantityItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * - nillable : true
     * @var NewyseServiceStructSubjectQuantity
     */
    public $SubjectQuantityItem;
    /**
     * Constructor method for SubjectQuantities
     * @see parent::__construct()
     * @param NewyseServiceStructSubjectQuantity $_subjectQuantityItem
     * @return NewyseServiceStructSubjectQuantities
     */
    public function __construct($_subjectQuantityItem = NULL)
    {
        parent::__construct(array('SubjectQuantityItem'=>$_subjectQuantityItem),false);
    }
    /**
     * Get SubjectQuantityItem value
     * @return NewyseServiceStructSubjectQuantity|null
     */
    public function getSubjectQuantityItem()
    {
        return $this->SubjectQuantityItem;
    }
    /**
     * Set SubjectQuantityItem value
     * @param NewyseServiceStructSubjectQuantity $_subjectQuantityItem the SubjectQuantityItem
     * @return NewyseServiceStructSubjectQuantity
     */
    public function setSubjectQuantityItem($_subjectQuantityItem)
    {
        return ($this->SubjectQuantityItem = $_subjectQuantityItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructSubjectQuantities
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
