<?php
/**
 * File for class NewyseServiceStructDebitCardConsumption
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructDebitCardConsumption originally named DebitCardConsumption
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructDebitCardConsumption extends NewyseServiceWsdlClass
{
    /**
     * The GroupNumber
     * @var long
     */
    public $GroupNumber;
    /**
     * The Consumed
     * @var double
     */
    public $Consumed;
    /**
     * The ProductDescription
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $ProductDescription;
    /**
     * Constructor method for DebitCardConsumption
     * @see parent::__construct()
     * @param long $_groupNumber
     * @param double $_consumed
     * @param string $_productDescription
     * @return NewyseServiceStructDebitCardConsumption
     */
    public function __construct($_groupNumber = NULL,$_consumed = NULL,$_productDescription = NULL)
    {
        parent::__construct(array('GroupNumber'=>$_groupNumber,'Consumed'=>$_consumed,'ProductDescription'=>$_productDescription),false);
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
     * Get Consumed value
     * @return double|null
     */
    public function getConsumed()
    {
        return $this->Consumed;
    }
    /**
     * Set Consumed value
     * @param double $_consumed the Consumed
     * @return double
     */
    public function setConsumed($_consumed)
    {
        return ($this->Consumed = $_consumed);
    }
    /**
     * Get ProductDescription value
     * @return string|null
     */
    public function getProductDescription()
    {
        return $this->ProductDescription;
    }
    /**
     * Set ProductDescription value
     * @param string $_productDescription the ProductDescription
     * @return string
     */
    public function setProductDescription($_productDescription)
    {
        return ($this->ProductDescription = $_productDescription);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructDebitCardConsumption
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
