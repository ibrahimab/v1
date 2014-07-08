<?php
/**
 * File for class NewyseServiceStructSubjectQuantity
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructSubjectQuantity originally named SubjectQuantity
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructSubjectQuantity extends NewyseServiceWsdlClass
{
    /**
     * The SubjectId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $SubjectId;
    /**
     * The Quantity
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var int
     */
    public $Quantity;
    /**
     * Constructor method for SubjectQuantity
     * @see parent::__construct()
     * @param long $_subjectId
     * @param int $_quantity
     * @return NewyseServiceStructSubjectQuantity
     */
    public function __construct($_subjectId = NULL,$_quantity = NULL)
    {
        parent::__construct(array('SubjectId'=>$_subjectId,'Quantity'=>$_quantity),false);
    }
    /**
     * Get SubjectId value
     * @return long|null
     */
    public function getSubjectId()
    {
        return $this->SubjectId;
    }
    /**
     * Set SubjectId value
     * @param long $_subjectId the SubjectId
     * @return long
     */
    public function setSubjectId($_subjectId)
    {
        return ($this->SubjectId = $_subjectId);
    }
    /**
     * Get Quantity value
     * @return int|null
     */
    public function getQuantity()
    {
        return $this->Quantity;
    }
    /**
     * Set Quantity value
     * @param int $_quantity the Quantity
     * @return int
     */
    public function setQuantity($_quantity)
    {
        return ($this->Quantity = $_quantity);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructSubjectQuantity
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
