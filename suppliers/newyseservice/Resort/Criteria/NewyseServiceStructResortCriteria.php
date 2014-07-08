<?php
/**
 * File for class NewyseServiceStructResortCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructResortCriteria originally named ResortCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructResortCriteria extends NewyseServiceWsdlClass
{
    /**
     * The DistributionChannelCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $DistributionChannelCode;
    /**
     * The DistributionChannelId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $DistributionChannelId;
    /**
     * The IgnoreRentable
     * Meta informations extracted from the WSDL
     * - default : false
     * - nillable : true
     * @var boolean
     */
    public $IgnoreRentable;
    /**
     * Constructor method for ResortCriteria
     * @see parent::__construct()
     * @param string $_distributionChannelCode
     * @param long $_distributionChannelId
     * @param boolean $_ignoreRentable
     * @return NewyseServiceStructResortCriteria
     */
    public function __construct($_distributionChannelCode = NULL,$_distributionChannelId = NULL,$_ignoreRentable = false)
    {
        parent::__construct(array('DistributionChannelCode'=>$_distributionChannelCode,'DistributionChannelId'=>$_distributionChannelId,'IgnoreRentable'=>$_ignoreRentable),false);
    }
    /**
     * Get DistributionChannelCode value
     * @return string|null
     */
    public function getDistributionChannelCode()
    {
        return $this->DistributionChannelCode;
    }
    /**
     * Set DistributionChannelCode value
     * @param string $_distributionChannelCode the DistributionChannelCode
     * @return string
     */
    public function setDistributionChannelCode($_distributionChannelCode)
    {
        return ($this->DistributionChannelCode = $_distributionChannelCode);
    }
    /**
     * Get DistributionChannelId value
     * @return long|null
     */
    public function getDistributionChannelId()
    {
        return $this->DistributionChannelId;
    }
    /**
     * Set DistributionChannelId value
     * @param long $_distributionChannelId the DistributionChannelId
     * @return long
     */
    public function setDistributionChannelId($_distributionChannelId)
    {
        return ($this->DistributionChannelId = $_distributionChannelId);
    }
    /**
     * Get IgnoreRentable value
     * @return boolean|null
     */
    public function getIgnoreRentable()
    {
        return $this->IgnoreRentable;
    }
    /**
     * Set IgnoreRentable value
     * @param boolean $_ignoreRentable the IgnoreRentable
     * @return boolean
     */
    public function setIgnoreRentable($_ignoreRentable)
    {
        return ($this->IgnoreRentable = $_ignoreRentable);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructResortCriteria
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
