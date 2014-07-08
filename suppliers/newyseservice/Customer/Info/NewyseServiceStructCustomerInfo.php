<?php
/**
 * File for class NewyseServiceStructCustomerInfo
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructCustomerInfo originally named CustomerInfo
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructCustomerInfo extends NewyseServiceWsdlClass
{
    /**
     * The CustomerId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $CustomerId;
    /**
     * The Firstname
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Firstname;
    /**
     * The Middlename
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Middlename;
    /**
     * The Lastname
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Lastname;
    /**
     * Constructor method for CustomerInfo
     * @see parent::__construct()
     * @param long $_customerId
     * @param string $_firstname
     * @param string $_middlename
     * @param string $_lastname
     * @return NewyseServiceStructCustomerInfo
     */
    public function __construct($_customerId = NULL,$_firstname = NULL,$_middlename = NULL,$_lastname = NULL)
    {
        parent::__construct(array('CustomerId'=>$_customerId,'Firstname'=>$_firstname,'Middlename'=>$_middlename,'Lastname'=>$_lastname),false);
    }
    /**
     * Get CustomerId value
     * @return long|null
     */
    public function getCustomerId()
    {
        return $this->CustomerId;
    }
    /**
     * Set CustomerId value
     * @param long $_customerId the CustomerId
     * @return long
     */
    public function setCustomerId($_customerId)
    {
        return ($this->CustomerId = $_customerId);
    }
    /**
     * Get Firstname value
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->Firstname;
    }
    /**
     * Set Firstname value
     * @param string $_firstname the Firstname
     * @return string
     */
    public function setFirstname($_firstname)
    {
        return ($this->Firstname = $_firstname);
    }
    /**
     * Get Middlename value
     * @return string|null
     */
    public function getMiddlename()
    {
        return $this->Middlename;
    }
    /**
     * Set Middlename value
     * @param string $_middlename the Middlename
     * @return string
     */
    public function setMiddlename($_middlename)
    {
        return ($this->Middlename = $_middlename);
    }
    /**
     * Get Lastname value
     * @return string|null
     */
    public function getLastname()
    {
        return $this->Lastname;
    }
    /**
     * Set Lastname value
     * @param string $_lastname the Lastname
     * @return string
     */
    public function setLastname($_lastname)
    {
        return ($this->Lastname = $_lastname);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructCustomerInfo
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
