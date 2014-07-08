<?php
/**
 * File for class NewyseServiceStructResortActivity
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructResortActivity originally named ResortActivity
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructResortActivity extends NewyseServiceWsdlClass
{
    /**
     * The ResortActivityId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $ResortActivityId;
    /**
     * The Title
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Title;
    /**
     * The Language
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Language;
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
     * The Reserve
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Reserve;
    /**
     * The ImageURL
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $ImageURL;
    /**
     * The ImagemanagerId
     * @var string
     */
    public $ImagemanagerId;
    /**
     * The PriceChild
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $PriceChild;
    /**
     * The PriceValuePoints
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $PriceValuePoints;
    /**
     * The PriceAdult
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $PriceAdult;
    /**
     * The MinAttendees
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var int
     */
    public $MinAttendees;
    /**
     * The MaxAttendees
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var int
     */
    public $MaxAttendees;
    /**
     * The MinAge
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var int
     */
    public $MinAge;
    /**
     * The MaxAge
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var int
     */
    public $MaxAge;
    /**
     * The ActivityCategories
     * @var NewyseServiceStructActivityCategories
     */
    public $ActivityCategories;
    /**
     * The Resorts
     * @var NewyseServiceStructResorts
     */
    public $Resorts;
    /**
     * The OpeningTimes
     * @var NewyseServiceStructOpeningTimes
     */
    public $OpeningTimes;
    /**
     * Constructor method for ResortActivity
     * @see parent::__construct()
     * @param long $_resortActivityId
     * @param string $_title
     * @param string $_language
     * @param string $_headText
     * @param string $_text
     * @param string $_description
     * @param string $_reserve
     * @param string $_imageURL
     * @param string $_imagemanagerId
     * @param string $_priceChild
     * @param string $_priceValuePoints
     * @param string $_priceAdult
     * @param int $_minAttendees
     * @param int $_maxAttendees
     * @param int $_minAge
     * @param int $_maxAge
     * @param NewyseServiceStructActivityCategories $_activityCategories
     * @param NewyseServiceStructResorts $_resorts
     * @param NewyseServiceStructOpeningTimes $_openingTimes
     * @return NewyseServiceStructResortActivity
     */
    public function __construct($_resortActivityId = NULL,$_title = NULL,$_language = NULL,$_headText = NULL,$_text = NULL,$_description = NULL,$_reserve = NULL,$_imageURL = NULL,$_imagemanagerId = NULL,$_priceChild = NULL,$_priceValuePoints = NULL,$_priceAdult = NULL,$_minAttendees = NULL,$_maxAttendees = NULL,$_minAge = NULL,$_maxAge = NULL,$_activityCategories = NULL,$_resorts = NULL,$_openingTimes = NULL)
    {
        parent::__construct(array('ResortActivityId'=>$_resortActivityId,'Title'=>$_title,'Language'=>$_language,'HeadText'=>$_headText,'Text'=>$_text,'Description'=>$_description,'Reserve'=>$_reserve,'ImageURL'=>$_imageURL,'ImagemanagerId'=>$_imagemanagerId,'PriceChild'=>$_priceChild,'PriceValuePoints'=>$_priceValuePoints,'PriceAdult'=>$_priceAdult,'MinAttendees'=>$_minAttendees,'MaxAttendees'=>$_maxAttendees,'MinAge'=>$_minAge,'MaxAge'=>$_maxAge,'ActivityCategories'=>$_activityCategories,'Resorts'=>$_resorts,'OpeningTimes'=>$_openingTimes),false);
    }
    /**
     * Get ResortActivityId value
     * @return long|null
     */
    public function getResortActivityId()
    {
        return $this->ResortActivityId;
    }
    /**
     * Set ResortActivityId value
     * @param long $_resortActivityId the ResortActivityId
     * @return long
     */
    public function setResortActivityId($_resortActivityId)
    {
        return ($this->ResortActivityId = $_resortActivityId);
    }
    /**
     * Get Title value
     * @return string|null
     */
    public function getTitle()
    {
        return $this->Title;
    }
    /**
     * Set Title value
     * @param string $_title the Title
     * @return string
     */
    public function setTitle($_title)
    {
        return ($this->Title = $_title);
    }
    /**
     * Get Language value
     * @return string|null
     */
    public function getLanguage()
    {
        return $this->Language;
    }
    /**
     * Set Language value
     * @param string $_language the Language
     * @return string
     */
    public function setLanguage($_language)
    {
        return ($this->Language = $_language);
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
     * Get Reserve value
     * @return string|null
     */
    public function getReserve()
    {
        return $this->Reserve;
    }
    /**
     * Set Reserve value
     * @param string $_reserve the Reserve
     * @return string
     */
    public function setReserve($_reserve)
    {
        return ($this->Reserve = $_reserve);
    }
    /**
     * Get ImageURL value
     * @return string|null
     */
    public function getImageURL()
    {
        return $this->ImageURL;
    }
    /**
     * Set ImageURL value
     * @param string $_imageURL the ImageURL
     * @return string
     */
    public function setImageURL($_imageURL)
    {
        return ($this->ImageURL = $_imageURL);
    }
    /**
     * Get ImagemanagerId value
     * @return string|null
     */
    public function getImagemanagerId()
    {
        return $this->ImagemanagerId;
    }
    /**
     * Set ImagemanagerId value
     * @param string $_imagemanagerId the ImagemanagerId
     * @return string
     */
    public function setImagemanagerId($_imagemanagerId)
    {
        return ($this->ImagemanagerId = $_imagemanagerId);
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
     * Get PriceValuePoints value
     * @return string|null
     */
    public function getPriceValuePoints()
    {
        return $this->PriceValuePoints;
    }
    /**
     * Set PriceValuePoints value
     * @param string $_priceValuePoints the PriceValuePoints
     * @return string
     */
    public function setPriceValuePoints($_priceValuePoints)
    {
        return ($this->PriceValuePoints = $_priceValuePoints);
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
     * Get MinAttendees value
     * @return int|null
     */
    public function getMinAttendees()
    {
        return $this->MinAttendees;
    }
    /**
     * Set MinAttendees value
     * @param int $_minAttendees the MinAttendees
     * @return int
     */
    public function setMinAttendees($_minAttendees)
    {
        return ($this->MinAttendees = $_minAttendees);
    }
    /**
     * Get MaxAttendees value
     * @return int|null
     */
    public function getMaxAttendees()
    {
        return $this->MaxAttendees;
    }
    /**
     * Set MaxAttendees value
     * @param int $_maxAttendees the MaxAttendees
     * @return int
     */
    public function setMaxAttendees($_maxAttendees)
    {
        return ($this->MaxAttendees = $_maxAttendees);
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
     * Get ActivityCategories value
     * @return NewyseServiceStructActivityCategories|null
     */
    public function getActivityCategories()
    {
        return $this->ActivityCategories;
    }
    /**
     * Set ActivityCategories value
     * @param NewyseServiceStructActivityCategories $_activityCategories the ActivityCategories
     * @return NewyseServiceStructActivityCategories
     */
    public function setActivityCategories($_activityCategories)
    {
        return ($this->ActivityCategories = $_activityCategories);
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
     * Get OpeningTimes value
     * @return NewyseServiceStructOpeningTimes|null
     */
    public function getOpeningTimes()
    {
        return $this->OpeningTimes;
    }
    /**
     * Set OpeningTimes value
     * @param NewyseServiceStructOpeningTimes $_openingTimes the OpeningTimes
     * @return NewyseServiceStructOpeningTimes
     */
    public function setOpeningTimes($_openingTimes)
    {
        return ($this->OpeningTimes = $_openingTimes);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructResortActivity
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
