<?php
/**
 * File for class NewyseServiceStructPincodeRegistration
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructPincodeRegistration originally named PincodeRegistration
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructPincodeRegistration extends NewyseServiceWsdlClass
{
    /**
     * The StatusCode
     * @var string
     */
    public $StatusCode;
    /**
     * The Timestamp
     * @var dateTime
     */
    public $Timestamp;
    /**
     * The LockCode
     * @var string
     */
    public $LockCode;
    /**
     * The LockAddress
     * @var string
     */
    public $LockAddress;
    /**
     * The Pincode
     * @var string
     */
    public $Pincode;
    /**
     * The ResortCode
     * @var string
     */
    public $ResortCode;
    /**
     * Constructor method for PincodeRegistration
     * @see parent::__construct()
     * @param string $_statusCode
     * @param dateTime $_timestamp
     * @param string $_lockCode
     * @param string $_lockAddress
     * @param string $_pincode
     * @param string $_resortCode
     * @return NewyseServiceStructPincodeRegistration
     */
    public function __construct($_statusCode = NULL,$_timestamp = NULL,$_lockCode = NULL,$_lockAddress = NULL,$_pincode = NULL,$_resortCode = NULL)
    {
        parent::__construct(array('StatusCode'=>$_statusCode,'Timestamp'=>$_timestamp,'LockCode'=>$_lockCode,'LockAddress'=>$_lockAddress,'Pincode'=>$_pincode,'ResortCode'=>$_resortCode),false);
    }
    /**
     * Get StatusCode value
     * @return string|null
     */
    public function getStatusCode()
    {
        return $this->StatusCode;
    }
    /**
     * Set StatusCode value
     * @param string $_statusCode the StatusCode
     * @return string
     */
    public function setStatusCode($_statusCode)
    {
        return ($this->StatusCode = $_statusCode);
    }
    /**
     * Get Timestamp value
     * @return dateTime|null
     */
    public function getTimestamp()
    {
        return $this->Timestamp;
    }
    /**
     * Set Timestamp value
     * @param dateTime $_timestamp the Timestamp
     * @return dateTime
     */
    public function setTimestamp($_timestamp)
    {
        return ($this->Timestamp = $_timestamp);
    }
    /**
     * Get LockCode value
     * @return string|null
     */
    public function getLockCode()
    {
        return $this->LockCode;
    }
    /**
     * Set LockCode value
     * @param string $_lockCode the LockCode
     * @return string
     */
    public function setLockCode($_lockCode)
    {
        return ($this->LockCode = $_lockCode);
    }
    /**
     * Get LockAddress value
     * @return string|null
     */
    public function getLockAddress()
    {
        return $this->LockAddress;
    }
    /**
     * Set LockAddress value
     * @param string $_lockAddress the LockAddress
     * @return string
     */
    public function setLockAddress($_lockAddress)
    {
        return ($this->LockAddress = $_lockAddress);
    }
    /**
     * Get Pincode value
     * @return string|null
     */
    public function getPincode()
    {
        return $this->Pincode;
    }
    /**
     * Set Pincode value
     * @param string $_pincode the Pincode
     * @return string
     */
    public function setPincode($_pincode)
    {
        return ($this->Pincode = $_pincode);
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
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructPincodeRegistration
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
