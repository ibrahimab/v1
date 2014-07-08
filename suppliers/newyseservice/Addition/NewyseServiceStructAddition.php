<?php
/**
 * File for class NewyseServiceStructAddition
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAddition originally named Addition
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAddition extends NewyseServiceWsdlClass
{
    /**
     * The ResourceId
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var long
     */
    public $ResourceId;
    /**
     * The StartDate
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var dateTime
     */
    public $StartDate;
    /**
     * The EndDate
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var dateTime
     */
    public $EndDate;
    /**
     * The Quantity
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var int
     */
    public $Quantity;
    /**
     * Constructor method for Addition
     * @see parent::__construct()
     * @param long $_resourceId
     * @param dateTime $_startDate
     * @param dateTime $_endDate
     * @param int $_quantity
     * @return NewyseServiceStructAddition
     */
    public function __construct($_resourceId = NULL,$_startDate = NULL,$_endDate = NULL,$_quantity = NULL)
    {
        parent::__construct(array('ResourceId'=>$_resourceId,'StartDate'=>$_startDate,'EndDate'=>$_endDate,'Quantity'=>$_quantity),false);
    }
    /**
     * Get ResourceId value
     * @return long|null
     */
    public function getResourceId()
    {
        return $this->ResourceId;
    }
    /**
     * Set ResourceId value
     * @param long $_resourceId the ResourceId
     * @return long
     */
    public function setResourceId($_resourceId)
    {
        return ($this->ResourceId = $_resourceId);
    }
    /**
     * Get StartDate value
     * @return dateTime|null
     */
    public function getStartDate()
    {
        return $this->StartDate;
    }
    /**
     * Set StartDate value
     * @param dateTime $_startDate the StartDate
     * @return dateTime
     */
    public function setStartDate($_startDate)
    {
        return ($this->StartDate = $_startDate);
    }
    /**
     * Get EndDate value
     * @return dateTime|null
     */
    public function getEndDate()
    {
        return $this->EndDate;
    }
    /**
     * Set EndDate value
     * @param dateTime $_endDate the EndDate
     * @return dateTime
     */
    public function setEndDate($_endDate)
    {
        return ($this->EndDate = $_endDate);
    }
    /**
     * Get Quantity value
     * @return int|null
     */
    public function getQuantity()
    {
        return $this->Quantity;
    }
    /**
     * Set Quantity value
     * @param int $_quantity the Quantity
     * @return int
     */
    public function setQuantity($_quantity)
    {
        return ($this->Quantity = $_quantity);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAddition
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
