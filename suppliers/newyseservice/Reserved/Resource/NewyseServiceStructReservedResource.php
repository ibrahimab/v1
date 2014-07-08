<?php
/**
 * File for class NewyseServiceStructReservedResource
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructReservedResource originally named ReservedResource
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructReservedResource extends NewyseServiceWsdlClass
{
    /**
     * The ResourceId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $ResourceId;
    /**
     * The Code
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Code;
    /**
     * The Name
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Name;
    /**
     * The Type
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Type;
    /**
     * The StartDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var dateTime
     */
    public $StartDate;
    /**
     * The EndDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var dateTime
     */
    public $EndDate;
    /**
     * The Quantity
     * @var int
     */
    public $Quantity;
    /**
     * The Price
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var double
     */
    public $Price;
    /**
     * Constructor method for ReservedResource
     * @see parent::__construct()
     * @param long $_resourceId
     * @param string $_code
     * @param string $_name
     * @param string $_type
     * @param dateTime $_startDate
     * @param dateTime $_endDate
     * @param int $_quantity
     * @param double $_price
     * @return NewyseServiceStructReservedResource
     */
    public function __construct($_resourceId = NULL,$_code = NULL,$_name = NULL,$_type = NULL,$_startDate = NULL,$_endDate = NULL,$_quantity = NULL,$_price = NULL)
    {
        parent::__construct(array('ResourceId'=>$_resourceId,'Code'=>$_code,'Name'=>$_name,'Type'=>$_type,'StartDate'=>$_startDate,'EndDate'=>$_endDate,'Quantity'=>$_quantity,'Price'=>$_price),false);
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
     * Get Code value
     * @return string|null
     */
    public function getCode()
    {
        return $this->Code;
    }
    /**
     * Set Code value
     * @param string $_code the Code
     * @return string
     */
    public function setCode($_code)
    {
        return ($this->Code = $_code);
    }
    /**
     * Get Name value
     * @return string|null
     */
    public function getName()
    {
        return $this->Name;
    }
    /**
     * Set Name value
     * @param string $_name the Name
     * @return string
     */
    public function setName($_name)
    {
        return ($this->Name = $_name);
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
     * Get Price value
     * @return double|null
     */
    public function getPrice()
    {
        return $this->Price;
    }
    /**
     * Set Price value
     * @param double $_price the Price
     * @return double
     */
    public function setPrice($_price)
    {
        return ($this->Price = $_price);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructReservedResource
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
