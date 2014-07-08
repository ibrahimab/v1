<?php
/**
 * File for class NewyseServiceStructReservationBillLine
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructReservationBillLine originally named ReservationBillLine
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructReservationBillLine extends NewyseServiceWsdlClass
{
    /**
     * The CashFlowRuleName
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $CashFlowRuleName;
    /**
     * The Value
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var double
     */
    public $Value;
    /**
     * The Quantity
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var int
     */
    public $Quantity;
    /**
     * The Multiplier
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var int
     */
    public $Multiplier;
    /**
     * The Total
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var double
     */
    public $Total;
    /**
     * The Sequence
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var int
     */
    public $Sequence;
    /**
     * The BillLineType
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var int
     */
    public $BillLineType;
    /**
     * The ResourceId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $ResourceId;
    /**
     * Constructor method for ReservationBillLine
     * @see parent::__construct()
     * @param string $_cashFlowRuleName
     * @param double $_value
     * @param int $_quantity
     * @param int $_multiplier
     * @param double $_total
     * @param int $_sequence
     * @param int $_billLineType
     * @param long $_resourceId
     * @return NewyseServiceStructReservationBillLine
     */
    public function __construct($_cashFlowRuleName = NULL,$_value = NULL,$_quantity = NULL,$_multiplier = NULL,$_total = NULL,$_sequence = NULL,$_billLineType = NULL,$_resourceId = NULL)
    {
        parent::__construct(array('CashFlowRuleName'=>$_cashFlowRuleName,'Value'=>$_value,'Quantity'=>$_quantity,'Multiplier'=>$_multiplier,'Total'=>$_total,'Sequence'=>$_sequence,'BillLineType'=>$_billLineType,'ResourceId'=>$_resourceId),false);
    }
    /**
     * Get CashFlowRuleName value
     * @return string|null
     */
    public function getCashFlowRuleName()
    {
        return $this->CashFlowRuleName;
    }
    /**
     * Set CashFlowRuleName value
     * @param string $_cashFlowRuleName the CashFlowRuleName
     * @return string
     */
    public function setCashFlowRuleName($_cashFlowRuleName)
    {
        return ($this->CashFlowRuleName = $_cashFlowRuleName);
    }
    /**
     * Get Value value
     * @return double|null
     */
    public function getValue()
    {
        return $this->Value;
    }
    /**
     * Set Value value
     * @param double $_value the Value
     * @return double
     */
    public function setValue($_value)
    {
        return ($this->Value = $_value);
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
     * Get Multiplier value
     * @return int|null
     */
    public function getMultiplier()
    {
        return $this->Multiplier;
    }
    /**
     * Set Multiplier value
     * @param int $_multiplier the Multiplier
     * @return int
     */
    public function setMultiplier($_multiplier)
    {
        return ($this->Multiplier = $_multiplier);
    }
    /**
     * Get Total value
     * @return double|null
     */
    public function getTotal()
    {
        return $this->Total;
    }
    /**
     * Set Total value
     * @param double $_total the Total
     * @return double
     */
    public function setTotal($_total)
    {
        return ($this->Total = $_total);
    }
    /**
     * Get Sequence value
     * @return int|null
     */
    public function getSequence()
    {
        return $this->Sequence;
    }
    /**
     * Set Sequence value
     * @param int $_sequence the Sequence
     * @return int
     */
    public function setSequence($_sequence)
    {
        return ($this->Sequence = $_sequence);
    }
    /**
     * Get BillLineType value
     * @return int|null
     */
    public function getBillLineType()
    {
        return $this->BillLineType;
    }
    /**
     * Set BillLineType value
     * @param int $_billLineType the BillLineType
     * @return int
     */
    public function setBillLineType($_billLineType)
    {
        return ($this->BillLineType = $_billLineType);
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
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructReservationBillLine
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
