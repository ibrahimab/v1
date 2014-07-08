<?php
/**
 * File for class NewyseServiceStructSpecial
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructSpecial originally named Special
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructSpecial extends NewyseServiceWsdlClass
{
    /**
     * The SpecialPrice
     * @var double
     */
    public $SpecialPrice;
    /**
     * The SpecialPriceInclusive
     * @var double
     */
    public $SpecialPriceInclusive;
    /**
     * The Quantity
     * @var int
     */
    public $Quantity;
    /**
     * The SpecialName
     * @var string
     */
    public $SpecialName;
    /**
     * The SpecialId
     * @var long
     */
    public $SpecialId;
    /**
     * The SpecialPolicy
     * @var string
     */
    public $SpecialPolicy;
    /**
     * The SpecialCode
     * @var string
     */
    public $SpecialCode;
    /**
     * The MinAge
     * @var int
     */
    public $MinAge;
    /**
     * The MaxAge
     * @var int
     */
    public $MaxAge;
    /**
     * Constructor method for Special
     * @see parent::__construct()
     * @param double $_specialPrice
     * @param double $_specialPriceInclusive
     * @param int $_quantity
     * @param string $_specialName
     * @param long $_specialId
     * @param string $_specialPolicy
     * @param string $_specialCode
     * @param int $_minAge
     * @param int $_maxAge
     * @return NewyseServiceStructSpecial
     */
    public function __construct($_specialPrice = NULL,$_specialPriceInclusive = NULL,$_quantity = NULL,$_specialName = NULL,$_specialId = NULL,$_specialPolicy = NULL,$_specialCode = NULL,$_minAge = NULL,$_maxAge = NULL)
    {
        parent::__construct(array('SpecialPrice'=>$_specialPrice,'SpecialPriceInclusive'=>$_specialPriceInclusive,'Quantity'=>$_quantity,'SpecialName'=>$_specialName,'SpecialId'=>$_specialId,'SpecialPolicy'=>$_specialPolicy,'SpecialCode'=>$_specialCode,'MinAge'=>$_minAge,'MaxAge'=>$_maxAge),false);
    }
    /**
     * Get SpecialPrice value
     * @return double|null
     */
    public function getSpecialPrice()
    {
        return $this->SpecialPrice;
    }
    /**
     * Set SpecialPrice value
     * @param double $_specialPrice the SpecialPrice
     * @return double
     */
    public function setSpecialPrice($_specialPrice)
    {
        return ($this->SpecialPrice = $_specialPrice);
    }
    /**
     * Get SpecialPriceInclusive value
     * @return double|null
     */
    public function getSpecialPriceInclusive()
    {
        return $this->SpecialPriceInclusive;
    }
    /**
     * Set SpecialPriceInclusive value
     * @param double $_specialPriceInclusive the SpecialPriceInclusive
     * @return double
     */
    public function setSpecialPriceInclusive($_specialPriceInclusive)
    {
        return ($this->SpecialPriceInclusive = $_specialPriceInclusive);
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
     * Get SpecialName value
     * @return string|null
     */
    public function getSpecialName()
    {
        return $this->SpecialName;
    }
    /**
     * Set SpecialName value
     * @param string $_specialName the SpecialName
     * @return string
     */
    public function setSpecialName($_specialName)
    {
        return ($this->SpecialName = $_specialName);
    }
    /**
     * Get SpecialId value
     * @return long|null
     */
    public function getSpecialId()
    {
        return $this->SpecialId;
    }
    /**
     * Set SpecialId value
     * @param long $_specialId the SpecialId
     * @return long
     */
    public function setSpecialId($_specialId)
    {
        return ($this->SpecialId = $_specialId);
    }
    /**
     * Get SpecialPolicy value
     * @return string|null
     */
    public function getSpecialPolicy()
    {
        return $this->SpecialPolicy;
    }
    /**
     * Set SpecialPolicy value
     * @param string $_specialPolicy the SpecialPolicy
     * @return string
     */
    public function setSpecialPolicy($_specialPolicy)
    {
        return ($this->SpecialPolicy = $_specialPolicy);
    }
    /**
     * Get SpecialCode value
     * @return string|null
     */
    public function getSpecialCode()
    {
        return $this->SpecialCode;
    }
    /**
     * Set SpecialCode value
     * @param string $_specialCode the SpecialCode
     * @return string
     */
    public function setSpecialCode($_specialCode)
    {
        return ($this->SpecialCode = $_specialCode);
    }
    /**
     * Get MinAge value
     * @return int|null
     */
    public function getMinAge()
    {
        return $this->MinAge;
    }
    /**
     * Set MinAge value
     * @param int $_minAge the MinAge
     * @return int
     */
    public function setMinAge($_minAge)
    {
        return ($this->MinAge = $_minAge);
    }
    /**
     * Get MaxAge value
     * @return int|null
     */
    public function getMaxAge()
    {
        return $this->MaxAge;
    }
    /**
     * Set MaxAge value
     * @param int $_maxAge the MaxAge
     * @return int
     */
    public function setMaxAge($_maxAge)
    {
        return ($this->MaxAge = $_maxAge);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructSpecial
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
