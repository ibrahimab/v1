<?php
/**
 * File for class NewyseServiceStructSubject
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructSubject originally named Subject
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructSubject extends NewyseServiceWsdlClass
{
    /**
     * The SubjectId
     * @var long
     */
    public $SubjectId;
    /**
     * The Name
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Name;
    /**
     * The Type
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Type;
    /**
     * The MinAge
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $MinAge;
    /**
     * The MaxAge
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $MaxAge;
    /**
     * Constructor method for Subject
     * @see parent::__construct()
     * @param long $_subjectId
     * @param string $_name
     * @param string $_type
     * @param long $_minAge
     * @param long $_maxAge
     * @return NewyseServiceStructSubject
     */
    public function __construct($_subjectId = NULL,$_name = NULL,$_type = NULL,$_minAge = NULL,$_maxAge = NULL)
    {
        parent::__construct(array('SubjectId'=>$_subjectId,'Name'=>$_name,'Type'=>$_type,'MinAge'=>$_minAge,'MaxAge'=>$_maxAge),false);
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
     * Get Name value
     * @return string|null
     */
    public function getName()
    {
        return $this->Name;
    }
    /**
     * Set Name value
     * @param string $_name the Name
     * @return string
     */
    public function setName($_name)
    {
        return ($this->Name = $_name);
    }
    /**
     * Get Type value
     * @return string|null
     */
    public function getType()
    {
        return $this->Type;
    }
    /**
     * Set Type value
     * @param string $_type the Type
     * @return string
     */
    public function setType($_type)
    {
        return ($this->Type = $_type);
    }
    /**
     * Get MinAge value
     * @return long|null
     */
    public function getMinAge()
    {
        return $this->MinAge;
    }
    /**
     * Set MinAge value
     * @param long $_minAge the MinAge
     * @return long
     */
    public function setMinAge($_minAge)
    {
        return ($this->MinAge = $_minAge);
    }
    /**
     * Get MaxAge value
     * @return long|null
     */
    public function getMaxAge()
    {
        return $this->MaxAge;
    }
    /**
     * Set MaxAge value
     * @param long $_maxAge the MaxAge
     * @return long
     */
    public function setMaxAge($_maxAge)
    {
        return ($this->MaxAge = $_maxAge);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructSubject
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
