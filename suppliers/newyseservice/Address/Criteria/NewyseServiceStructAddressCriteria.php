<?php
/**
 * File for class NewyseServiceStructAddressCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAddressCriteria originally named AddressCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAddressCriteria extends NewyseServiceWsdlClass
{
    /**
     * The AddressManagerId
     * @var long
     */
    public $AddressManagerId;
    /**
     * Constructor method for AddressCriteria
     * @see parent::__construct()
     * @param long $_addressManagerId
     * @return NewyseServiceStructAddressCriteria
     */
    public function __construct($_addressManagerId = NULL)
    {
        parent::__construct(array('AddressManagerId'=>$_addressManagerId),false);
    }
    /**
     * Get AddressManagerId value
     * @return long|null
     */
    public function getAddressManagerId()
    {
        return $this->AddressManagerId;
    }
    /**
     * Set AddressManagerId value
     * @param long $_addressManagerId the AddressManagerId
     * @return long
     */
    public function setAddressManagerId($_addressManagerId)
    {
        return ($this->AddressManagerId = $_addressManagerId);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAddressCriteria
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
