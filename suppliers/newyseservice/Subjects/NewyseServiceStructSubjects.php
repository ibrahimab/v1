<?php
/**
 * File for class NewyseServiceStructSubjects
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructSubjects originally named Subjects
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructSubjects extends NewyseServiceWsdlClass
{
    /**
     * The SubjectItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructSubject
     */
    public $SubjectItem;
    /**
     * Constructor method for Subjects
     * @see parent::__construct()
     * @param NewyseServiceStructSubject $_subjectItem
     * @return NewyseServiceStructSubjects
     */
    public function __construct($_subjectItem = NULL)
    {
        parent::__construct(array('SubjectItem'=>$_subjectItem),false);
    }
    /**
     * Get SubjectItem value
     * @return NewyseServiceStructSubject|null
     */
    public function getSubjectItem()
    {
        return $this->SubjectItem;
    }
    /**
     * Set SubjectItem value
     * @param NewyseServiceStructSubject $_subjectItem the SubjectItem
     * @return NewyseServiceStructSubject
     */
    public function setSubjectItem($_subjectItem)
    {
        return ($this->SubjectItem = $_subjectItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructSubjects
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
