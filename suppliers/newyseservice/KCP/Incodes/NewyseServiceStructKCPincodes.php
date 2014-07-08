<?php
/**
 * File for class NewyseServiceStructKCPincodes
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructKCPincodes originally named KCPincodes
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructKCPincodes extends NewyseServiceWsdlClass
{
    /**
     * The ResortCode
     * @var string
     */
    public $ResortCode;
    /**
     * The PincodeLength
     * @var int
     */
    public $PincodeLength;
    /**
     * The LockCode
     * @var string
     */
    public $LockCode;
    /**
     * The PincodeItems
     * @var NewyseServiceStructPincodeItems
     */
    public $PincodeItems;
    /**
     * Constructor method for KCPincodes
     * @see parent::__construct()
     * @param string $_resortCode
     * @param int $_pincodeLength
     * @param string $_lockCode
     * @param NewyseServiceStructPincodeItems $_pincodeItems
     * @return NewyseServiceStructKCPincodes
     */
    public function __construct($_resortCode = NULL,$_pincodeLength = NULL,$_lockCode = NULL,$_pincodeItems = NULL)
    {
        parent::__construct(array('ResortCode'=>$_resortCode,'PincodeLength'=>$_pincodeLength,'LockCode'=>$_lockCode,'PincodeItems'=>$_pincodeItems),false);
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
     * Get PincodeLength value
     * @return int|null
     */
    public function getPincodeLength()
    {
        return $this->PincodeLength;
    }
    /**
     * Set PincodeLength value
     * @param int $_pincodeLength the PincodeLength
     * @return int
     */
    public function setPincodeLength($_pincodeLength)
    {
        return ($this->PincodeLength = $_pincodeLength);
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
     * Get PincodeItems value
     * @return NewyseServiceStructPincodeItems|null
     */
    public function getPincodeItems()
    {
        return $this->PincodeItems;
    }
    /**
     * Set PincodeItems value
     * @param NewyseServiceStructPincodeItems $_pincodeItems the PincodeItems
     * @return NewyseServiceStructPincodeItems
     */
    public function setPincodeItems($_pincodeItems)
    {
        return ($this->PincodeItems = $_pincodeItems);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructKCPincodes
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
