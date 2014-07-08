<?php
/**
 * File for class NewyseServiceStructWSCustomerTitle
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructWSCustomerTitle originally named WSCustomerTitle
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructWSCustomerTitle extends NewyseServiceWsdlClass
{
    /**
     * The CustomerTitleId
     * @var long
     */
    public $CustomerTitleId;
    /**
     * The Code
     * @var string
     */
    public $Code;
    /**
     * The Priority
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $Priority;
    /**
     * The Title
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var string
     */
    public $Title;
    /**
     * Constructor method for WSCustomerTitle
     * @see parent::__construct()
     * @param long $_customerTitleId
     * @param string $_code
     * @param long $_priority
     * @param string $_title
     * @return NewyseServiceStructWSCustomerTitle
     */
    public function __construct($_customerTitleId = NULL,$_code = NULL,$_priority = NULL,$_title = NULL)
    {
        parent::__construct(array('CustomerTitleId'=>$_customerTitleId,'Code'=>$_code,'Priority'=>$_priority,'Title'=>$_title),false);
    }
    /**
     * Get CustomerTitleId value
     * @return long|null
     */
    public function getCustomerTitleId()
    {
        return $this->CustomerTitleId;
    }
    /**
     * Set CustomerTitleId value
     * @param long $_customerTitleId the CustomerTitleId
     * @return long
     */
    public function setCustomerTitleId($_customerTitleId)
    {
        return ($this->CustomerTitleId = $_customerTitleId);
    }
    /**
     * Get Code value
     * @return string|null
     */
    public function getCode()
    {
        return $this->Code;
    }
    /**
     * Set Code value
     * @param string $_code the Code
     * @return string
     */
    public function setCode($_code)
    {
        return ($this->Code = $_code);
    }
    /**
     * Get Priority value
     * @return long|null
     */
    public function getPriority()
    {
        return $this->Priority;
    }
    /**
     * Set Priority value
     * @param long $_priority the Priority
     * @return long
     */
    public function setPriority($_priority)
    {
        return ($this->Priority = $_priority);
    }
    /**
     * Get Title value
     * @return string|null
     */
    public function getTitle()
    {
        return $this->Title;
    }
    /**
     * Set Title value
     * @param string $_title the Title
     * @return string
     */
    public function setTitle($_title)
    {
        return ($this->Title = $_title);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructWSCustomerTitle
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
