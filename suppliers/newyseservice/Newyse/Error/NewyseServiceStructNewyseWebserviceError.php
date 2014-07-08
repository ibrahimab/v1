<?php
/**
 * File for class NewyseServiceStructNewyseWebserviceError
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructNewyseWebserviceError originally named NewyseWebserviceError
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructNewyseWebserviceError extends NewyseServiceWsdlClass
{
    /**
     * The message
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $message;
    /**
     * Constructor method for NewyseWebserviceError
     * @see parent::__construct()
     * @param string $_message
     * @return NewyseServiceStructNewyseWebserviceError
     */
    public function __construct($_message = NULL)
    {
        parent::__construct(array('message'=>$_message),false);
    }
    /**
     * Get message value
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }
    /**
     * Set message value
     * @param string $_message the message
     * @return string
     */
    public function setMessage($_message)
    {
        return ($this->message = $_message);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructNewyseWebserviceError
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
