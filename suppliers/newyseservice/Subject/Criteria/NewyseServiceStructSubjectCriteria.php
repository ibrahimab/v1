<?php
/**
 * File for class NewyseServiceStructSubjectCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructSubjectCriteria originally named SubjectCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructSubjectCriteria extends NewyseServiceWsdlClass
{
    /**
     * The ResortId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $ResortId;
    /**
     * The ResortCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $ResortCode;
    /**
     * The Type
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Type;
    /**
     * Constructor method for SubjectCriteria
     * @see parent::__construct()
     * @param long $_resortId
     * @param string $_resortCode
     * @param string $_type
     * @return NewyseServiceStructSubjectCriteria
     */
    public function __construct($_resortId = NULL,$_resortCode = NULL,$_type = NULL)
    {
        parent::__construct(array('ResortId'=>$_resortId,'ResortCode'=>$_resortCode,'Type'=>$_type),false);
    }
    /**
     * Get ResortId value
     * @return long|null
     */
    public function getResortId()
    {
        return $this->ResortId;
    }
    /**
     * Set ResortId value
     * @param long $_resortId the ResortId
     * @return long
     */
    public function setResortId($_resortId)
    {
        return ($this->ResortId = $_resortId);
    }
    /**
     * Get ResortCode value
     * @return string|null
     */
    public function getResortCode()
    {
        return $this->ResortCode;
    }
    /**
     * Set ResortCode value
     * @param string $_resortCode the ResortCode
     * @return string
     */
    public function setResortCode($_resortCode)
    {
        return ($this->ResortCode = $_resortCode);
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
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructSubjectCriteria
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
