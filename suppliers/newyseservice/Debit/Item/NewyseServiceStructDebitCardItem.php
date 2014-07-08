<?php
/**
 * File for class NewyseServiceStructDebitCardItem
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructDebitCardItem originally named DebitCardItem
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructDebitCardItem extends NewyseServiceWsdlClass
{
    /**
     * The GroupNumber
     * @var long
     */
    public $GroupNumber;
    /**
     * The Type
     * @var string
     */
    public $Type;
    /**
     * The AvailableLimit
     * @var double
     */
    public $AvailableLimit;
    /**
     * The InitialValue
     * @var double
     */
    public $InitialValue;
    /**
     * The InternalPrice
     * @var double
     */
    public $InternalPrice;
    /**
     * The Description
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Description;
    /**
     * The StartDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $StartDate;
    /**
     * The EndDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $EndDate;
    /**
     * Constructor method for DebitCardItem
     * @see parent::__construct()
     * @param long $_groupNumber
     * @param string $_type
     * @param double $_availableLimit
     * @param double $_initialValue
     * @param double $_internalPrice
     * @param string $_description
     * @param string $_startDate
     * @param string $_endDate
     * @return NewyseServiceStructDebitCardItem
     */
    public function __construct($_groupNumber = NULL,$_type = NULL,$_availableLimit = NULL,$_initialValue = NULL,$_internalPrice = NULL,$_description = NULL,$_startDate = NULL,$_endDate = NULL)
    {
        parent::__construct(array('GroupNumber'=>$_groupNumber,'Type'=>$_type,'AvailableLimit'=>$_availableLimit,'InitialValue'=>$_initialValue,'InternalPrice'=>$_internalPrice,'Description'=>$_description,'StartDate'=>$_startDate,'EndDate'=>$_endDate),false);
    }
    /**
     * Get GroupNumber value
     * @return long|null
     */
    public function getGroupNumber()
    {
        return $this->GroupNumber;
    }
    /**
     * Set GroupNumber value
     * @param long $_groupNumber the GroupNumber
     * @return long
     */
    public function setGroupNumber($_groupNumber)
    {
        return ($this->GroupNumber = $_groupNumber);
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
     * Get AvailableLimit value
     * @return double|null
     */
    public function getAvailableLimit()
    {
        return $this->AvailableLimit;
    }
    /**
     * Set AvailableLimit value
     * @param double $_availableLimit the AvailableLimit
     * @return double
     */
    public function setAvailableLimit($_availableLimit)
    {
        return ($this->AvailableLimit = $_availableLimit);
    }
    /**
     * Get InitialValue value
     * @return double|null
     */
    public function getInitialValue()
    {
        return $this->InitialValue;
    }
    /**
     * Set InitialValue value
     * @param double $_initialValue the InitialValue
     * @return double
     */
    public function setInitialValue($_initialValue)
    {
        return ($this->InitialValue = $_initialValue);
    }
    /**
     * Get InternalPrice value
     * @return double|null
     */
    public function getInternalPrice()
    {
        return $this->InternalPrice;
    }
    /**
     * Set InternalPrice value
     * @param double $_internalPrice the InternalPrice
     * @return double
     */
    public function setInternalPrice($_internalPrice)
    {
        return ($this->InternalPrice = $_internalPrice);
    }
    /**
     * Get Description value
     * @return string|null
     */
    public function getDescription()
    {
        return $this->Description;
    }
    /**
     * Set Description value
     * @param string $_description the Description
     * @return string
     */
    public function setDescription($_description)
    {
        return ($this->Description = $_description);
    }
    /**
     * Get StartDate value
     * @return string|null
     */
    public function getStartDate()
    {
        return $this->StartDate;
    }
    /**
     * Set StartDate value
     * @param string $_startDate the StartDate
     * @return string
     */
    public function setStartDate($_startDate)
    {
        return ($this->StartDate = $_startDate);
    }
    /**
     * Get EndDate value
     * @return string|null
     */
    public function getEndDate()
    {
        return $this->EndDate;
    }
    /**
     * Set EndDate value
     * @param string $_endDate the EndDate
     * @return string
     */
    public function setEndDate($_endDate)
    {
        return ($this->EndDate = $_endDate);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructDebitCardItem
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
