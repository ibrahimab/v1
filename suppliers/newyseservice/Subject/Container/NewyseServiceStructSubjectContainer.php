<?php
/**
 * File for class NewyseServiceStructSubjectContainer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructSubjectContainer originally named SubjectContainer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructSubjectContainer extends NewyseServiceWsdlClass
{
    /**
     * The Subjects
     * @var NewyseServiceStructSubjects
     */
    public $Subjects;
    /**
     * Constructor method for SubjectContainer
     * @see parent::__construct()
     * @param NewyseServiceStructSubjects $_subjects
     * @return NewyseServiceStructSubjectContainer
     */
    public function __construct($_subjects = NULL)
    {
        parent::__construct(array('Subjects'=>$_subjects),false);
    }
    /**
     * Get Subjects value
     * @return NewyseServiceStructSubjects|null
     */
    public function getSubjects()
    {
        return $this->Subjects;
    }
    /**
     * Set Subjects value
     * @param NewyseServiceStructSubjects $_subjects the Subjects
     * @return NewyseServiceStructSubjects
     */
    public function setSubjects($_subjects)
    {
        return ($this->Subjects = $_subjects);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructSubjectContainer
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
