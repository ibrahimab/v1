<?php
/**
 * File for class NewyseServiceStructTipTrip
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructTipTrip originally named TipTrip
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructTipTrip extends NewyseServiceWsdlClass
{
    /**
     * The TipsTripsId
     * @var long
     */
    public $TipsTripsId;
    /**
     * The TipsTripsCategoryId
     * @var long
     */
    public $TipsTripsCategoryId;
    /**
     * The AddressmanagerId
     * @var long
     */
    public $AddressmanagerId;
    /**
     * The ImagemanagerId
     * @var long
     */
    public $ImagemanagerId;
    /**
     * The Name
     * @var string
     */
    public $Name;
    /**
     * The HeadText
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $HeadText;
    /**
     * The Text
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Text;
    /**
     * The Description
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Description;
    /**
     * The Location
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Location;
    /**
     * The Price
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Price;
    /**
     * The PriceChild
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $PriceChild;
    /**
     * The PriceValuePoint
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $PriceValuePoint;
    /**
     * The PriceAdult
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $PriceAdult;
    /**
     * The StartDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var dateTime
     */
    public $StartDate;
    /**
     * The EndDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var dateTime
     */
    public $EndDate;
    /**
     * The Url
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Url;
    /**
     * The Resorts
     * @var NewyseServiceStructResorts
     */
    public $Resorts;
    /**
     * Constructor method for TipTrip
     * @see parent::__construct()
     * @param long $_tipsTripsId
     * @param long $_tipsTripsCategoryId
     * @param long $_addressmanagerId
     * @param long $_imagemanagerId
     * @param string $_name
     * @param string $_headText
     * @param string $_text
     * @param string $_description
     * @param string $_location
     * @param string $_price
     * @param string $_priceChild
     * @param string $_priceValuePoint
     * @param string $_priceAdult
     * @param dateTime $_startDate
     * @param dateTime $_endDate
     * @param string $_url
     * @param NewyseServiceStructResorts $_resorts
     * @return NewyseServiceStructTipTrip
     */
    public function __construct($_tipsTripsId = NULL,$_tipsTripsCategoryId = NULL,$_addressmanagerId = NULL,$_imagemanagerId = NULL,$_name = NULL,$_headText = NULL,$_text = NULL,$_description = NULL,$_location = NULL,$_price = NULL,$_priceChild = NULL,$_priceValuePoint = NULL,$_priceAdult = NULL,$_startDate = NULL,$_endDate = NULL,$_url = NULL,$_resorts = NULL)
    {
        parent::__construct(array('TipsTripsId'=>$_tipsTripsId,'TipsTripsCategoryId'=>$_tipsTripsCategoryId,'AddressmanagerId'=>$_addressmanagerId,'ImagemanagerId'=>$_imagemanagerId,'Name'=>$_name,'HeadText'=>$_headText,'Text'=>$_text,'Description'=>$_description,'Location'=>$_location,'Price'=>$_price,'PriceChild'=>$_priceChild,'PriceValuePoint'=>$_priceValuePoint,'PriceAdult'=>$_priceAdult,'StartDate'=>$_startDate,'EndDate'=>$_endDate,'Url'=>$_url,'Resorts'=>$_resorts),false);
    }
    /**
     * Get TipsTripsId value
     * @return long|null
     */
    public function getTipsTripsId()
    {
        return $this->TipsTripsId;
    }
    /**
     * Set TipsTripsId value
     * @param long $_tipsTripsId the TipsTripsId
     * @return long
     */
    public function setTipsTripsId($_tipsTripsId)
    {
        return ($this->TipsTripsId = $_tipsTripsId);
    }
    /**
     * Get TipsTripsCategoryId value
     * @return long|null
     */
    public function getTipsTripsCategoryId()
    {
        return $this->TipsTripsCategoryId;
    }
    /**
     * Set TipsTripsCategoryId value
     * @param long $_tipsTripsCategoryId the TipsTripsCategoryId
     * @return long
     */
    public function setTipsTripsCategoryId($_tipsTripsCategoryId)
    {
        return ($this->TipsTripsCategoryId = $_tipsTripsCategoryId);
    }
    /**
     * Get AddressmanagerId value
     * @return long|null
     */
    public function getAddressmanagerId()
    {
        return $this->AddressmanagerId;
    }
    /**
     * Set AddressmanagerId value
     * @param long $_addressmanagerId the AddressmanagerId
     * @return long
     */
    public function setAddressmanagerId($_addressmanagerId)
    {
        return ($this->AddressmanagerId = $_addressmanagerId);
    }
    /**
     * Get ImagemanagerId value
     * @return long|null
     */
    public function getImagemanagerId()
    {
        return $this->ImagemanagerId;
    }
    /**
     * Set ImagemanagerId value
     * @param long $_imagemanagerId the ImagemanagerId
     * @return long
     */
    public function setImagemanagerId($_imagemanagerId)
    {
        return ($this->ImagemanagerId = $_imagemanagerId);
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
     * Get HeadText value
     * @return string|null
     */
    public function getHeadText()
    {
        return $this->HeadText;
    }
    /**
     * Set HeadText value
     * @param string $_headText the HeadText
     * @return string
     */
    public function setHeadText($_headText)
    {
        return ($this->HeadText = $_headText);
    }
    /**
     * Get Text value
     * @return string|null
     */
    public function getText()
    {
        return $this->Text;
    }
    /**
     * Set Text value
     * @param string $_text the Text
     * @return string
     */
    public function setText($_text)
    {
        return ($this->Text = $_text);
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
     * Get Location value
     * @return string|null
     */
    public function getLocation()
    {
        return $this->Location;
    }
    /**
     * Set Location value
     * @param string $_location the Location
     * @return string
     */
    public function setLocation($_location)
    {
        return ($this->Location = $_location);
    }
    /**
     * Get Price value
     * @return string|null
     */
    public function getPrice()
    {
        return $this->Price;
    }
    /**
     * Set Price value
     * @param string $_price the Price
     * @return string
     */
    public function setPrice($_price)
    {
        return ($this->Price = $_price);
    }
    /**
     * Get PriceChild value
     * @return string|null
     */
    public function getPriceChild()
    {
        return $this->PriceChild;
    }
    /**
     * Set PriceChild value
     * @param string $_priceChild the PriceChild
     * @return string
     */
    public function setPriceChild($_priceChild)
    {
        return ($this->PriceChild = $_priceChild);
    }
    /**
     * Get PriceValuePoint value
     * @return string|null
     */
    public function getPriceValuePoint()
    {
        return $this->PriceValuePoint;
    }
    /**
     * Set PriceValuePoint value
     * @param string $_priceValuePoint the PriceValuePoint
     * @return string
     */
    public function setPriceValuePoint($_priceValuePoint)
    {
        return ($this->PriceValuePoint = $_priceValuePoint);
    }
    /**
     * Get PriceAdult value
     * @return string|null
     */
    public function getPriceAdult()
    {
        return $this->PriceAdult;
    }
    /**
     * Set PriceAdult value
     * @param string $_priceAdult the PriceAdult
     * @return string
     */
    public function setPriceAdult($_priceAdult)
    {
        return ($this->PriceAdult = $_priceAdult);
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
     * Get Url value
     * @return string|null
     */
    public function getUrl()
    {
        return $this->Url;
    }
    /**
     * Set Url value
     * @param string $_url the Url
     * @return string
     */
    public function setUrl($_url)
    {
        return ($this->Url = $_url);
    }
    /**
     * Get Resorts value
     * @return NewyseServiceStructResorts|null
     */
    public function getResorts()
    {
        return $this->Resorts;
    }
    /**
     * Set Resorts value
     * @param NewyseServiceStructResorts $_resorts the Resorts
     * @return NewyseServiceStructResorts
     */
    public function setResorts($_resorts)
    {
        return ($this->Resorts = $_resorts);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructTipTrip
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
