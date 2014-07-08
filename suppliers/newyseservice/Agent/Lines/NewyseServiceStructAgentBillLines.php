<?php
/**
 * File for class NewyseServiceStructAgentBillLines
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAgentBillLines originally named AgentBillLines
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAgentBillLines extends NewyseServiceWsdlClass
{
    /**
     * The AgentBillLineItem
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NewyseServiceStructReservationBillLine
     */
    public $AgentBillLineItem;
    /**
     * Constructor method for AgentBillLines
     * @see parent::__construct()
     * @param NewyseServiceStructReservationBillLine $_agentBillLineItem
     * @return NewyseServiceStructAgentBillLines
     */
    public function __construct($_agentBillLineItem = NULL)
    {
        parent::__construct(array('AgentBillLineItem'=>$_agentBillLineItem),false);
    }
    /**
     * Get AgentBillLineItem value
     * @return NewyseServiceStructReservationBillLine|null
     */
    public function getAgentBillLineItem()
    {
        return $this->AgentBillLineItem;
    }
    /**
     * Set AgentBillLineItem value
     * @param NewyseServiceStructReservationBillLine $_agentBillLineItem the AgentBillLineItem
     * @return NewyseServiceStructReservationBillLine
     */
    public function setAgentBillLineItem($_agentBillLineItem)
    {
        return ($this->AgentBillLineItem = $_agentBillLineItem);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAgentBillLines
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
