<?php
/**
 * File for class NewyseServiceStructPincodeItem
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructPincodeItem originally named PincodeItem
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructPincodeItem extends NewyseServiceWsdlClass
{
    /**
     * The ArrivalDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var dateTime
     */
    public $ArrivalDate;
    /**
     * The LockCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $LockCode;
    /**
     * The Pincodes
     * @var NewyseServiceStructPincodes
     */
    public $Pincodes;
    /**
     * Constructor method for PincodeItem
     * @see parent::__construct()
     * @param dateTime $_arrivalDate
     * @param string $_lockCode
     * @param NewyseServiceStructPincodes $_pincodes
     * @return NewyseServiceStructPincodeItem
     */
    public function __construct($_arrivalDate = NULL,$_lockCode = NULL,$_pincodes = NULL)
    {
        parent::__construct(array('ArrivalDate'=>$_arrivalDate,'LockCode'=>$_lockCode,'Pincodes'=>$_pincodes),false);
    }
    /**
     * Get ArrivalDate value
     * @return dateTime|null
     */
    public function getArrivalDate()
    {
        return $this->ArrivalDate;
    }
    /**
     * Set ArrivalDate value
     * @param dateTime $_arrivalDate the ArrivalDate
     * @return dateTime
     */
    public function setArrivalDate($_arrivalDate)
    {
        return ($this->ArrivalDate = $_arrivalDate);
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
     * Get Pincodes value
     * @return NewyseServiceStructPincodes|null
     */
    public function getPincodes()
    {
        return $this->Pincodes;
    }
    /**
     * Set Pincodes value
     * @param NewyseServiceStructPincodes $_pincodes the Pincodes
     * @return NewyseServiceStructPincodes
     */
    public function setPincodes($_pincodes)
    {
        return ($this->Pincodes = $_pincodes);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructPincodeItem
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
