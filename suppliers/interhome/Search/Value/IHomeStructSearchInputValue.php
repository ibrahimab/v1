<?php
/**
 * File for class IHomeStructSearchInputValue
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructSearchInputValue originally named SearchInputValue
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructSearchInputValue extends IHomeWsdlClass
{
	/**
	 * The Page
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Page;
	/**
	 * The PageSize
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $PageSize;
	/**
	 * The OrderDirection
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var IHomeEnumOrderDirection
	 */
	public $OrderDirection;
	/**
	 * The OrderBy
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var IHomeEnumOrderBy
	 */
	public $OrderBy;
	/**
	 * The Duration
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Duration;
	/**
	 * The ThemeFilter
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var IHomeEnumThemeFilterTypes
	 */
	public $ThemeFilter;
	/**
	 * The HouseApartmentType
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var IHomeEnumHouseApartmentTypes
	 */
	public $HouseApartmentType;
	/**
	 * The SpecialOffer
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var IHomeEnumSpecialOffers
	 */
	public $SpecialOffer;
	/**
	 * The PaxMin
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $PaxMin;
	/**
	 * The PaxMax
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $PaxMax;
	/**
	 * The RoomsMin
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $RoomsMin;
	/**
	 * The RoomsMax
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $RoomsMax;
	/**
	 * The BedroomsMin
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $BedroomsMin;
	/**
	 * The BedroomsMax
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $BedroomsMax;
	/**
	 * The BathroomsMin
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $BathroomsMin;
	/**
	 * The BathroomsMax
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $BathroomsMax;
	/**
	 * The QualityMin
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $QualityMin;
	/**
	 * The QualityMax
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $QualityMax;
	/**
	 * The DistanceToCenter
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $DistanceToCenter;
	/**
	 * The DistanceToGolfCourse
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $DistanceToGolfCourse;
	/**
	 * The DistanceToLake
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $DistanceToLake;
	/**
	 * The DistanceToSea
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $DistanceToSea;
	/**
	 * The DistanceToSeaOrLake
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $DistanceToSeaOrLake;
	/**
	 * The DistanceToSkiLifts
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $DistanceToSkiLifts;
	/**
	 * The LanguageCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $LanguageCode;
	/**
	 * The CurrencyCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $CurrencyCode;
	/**
	 * The SalesOfficeCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $SalesOfficeCode;
	/**
	 * The Quicksearch
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Quicksearch;
	/**
	 * The CountryCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $CountryCode;
	/**
	 * The RegionCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $RegionCode;
	/**
	 * The PlaceCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $PlaceCode;
	/**
	 * The CheckIn
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $CheckIn;
	/**
	 * The Facilities
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfFacilities
	 */
	public $Facilities;
	/**
	 * The Accessibilities
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfAccessibilities
	 */
	public $Accessibilities;
	/**
	 * The Activities
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfActivities
	 */
	public $Activities;
	/**
	 * The Situations
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfSituations
	 */
	public $Situations;
	/**
	 * The PropertyTypes
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfPropertyTypes
	 */
	public $PropertyTypes;
	/**
	 * Constructor method for SearchInputValue
	 * @see parent::__construct()
	 * @param int $_page
	 * @param int $_pageSize
	 * @param IHomeEnumOrderDirection $_orderDirection
	 * @param IHomeEnumOrderBy $_orderBy
	 * @param int $_duration
	 * @param IHomeEnumThemeFilterTypes $_themeFilter
	 * @param IHomeEnumHouseApartmentTypes $_houseApartmentType
	 * @param IHomeEnumSpecialOffers $_specialOffer
	 * @param int $_paxMin
	 * @param int $_paxMax
	 * @param int $_roomsMin
	 * @param int $_roomsMax
	 * @param int $_bedroomsMin
	 * @param int $_bedroomsMax
	 * @param int $_bathroomsMin
	 * @param int $_bathroomsMax
	 * @param int $_qualityMin
	 * @param int $_qualityMax
	 * @param int $_distanceToCenter
	 * @param int $_distanceToGolfCourse
	 * @param int $_distanceToLake
	 * @param int $_distanceToSea
	 * @param int $_distanceToSeaOrLake
	 * @param int $_distanceToSkiLifts
	 * @param string $_languageCode
	 * @param string $_currencyCode
	 * @param string $_salesOfficeCode
	 * @param string $_quicksearch
	 * @param string $_countryCode
	 * @param string $_regionCode
	 * @param string $_placeCode
	 * @param string $_checkIn
	 * @param IHomeStructArrayOfFacilities $_facilities
	 * @param IHomeStructArrayOfAccessibilities $_accessibilities
	 * @param IHomeStructArrayOfActivities $_activities
	 * @param IHomeStructArrayOfSituations $_situations
	 * @param IHomeStructArrayOfPropertyTypes $_propertyTypes
	 * @return IHomeStructSearchInputValue
	 */
	public function __construct($_page,$_pageSize,$_orderDirection,$_orderBy,$_duration,$_themeFilter,$_houseApartmentType,$_specialOffer,$_paxMin,$_paxMax,$_roomsMin,$_roomsMax,$_bedroomsMin,$_bedroomsMax,$_bathroomsMin,$_bathroomsMax,$_qualityMin,$_qualityMax,$_distanceToCenter,$_distanceToGolfCourse,$_distanceToLake,$_distanceToSea,$_distanceToSeaOrLake,$_distanceToSkiLifts,$_languageCode = NULL,$_currencyCode = NULL,$_salesOfficeCode = NULL,$_quicksearch = NULL,$_countryCode = NULL,$_regionCode = NULL,$_placeCode = NULL,$_checkIn = NULL,$_facilities = NULL,$_accessibilities = NULL,$_activities = NULL,$_situations = NULL,$_propertyTypes = NULL)
	{
		parent::__construct(array('Page'=>$_page,'PageSize'=>$_pageSize,'OrderDirection'=>$_orderDirection,'OrderBy'=>$_orderBy,'Duration'=>$_duration,'ThemeFilter'=>$_themeFilter,'HouseApartmentType'=>$_houseApartmentType,'SpecialOffer'=>$_specialOffer,'PaxMin'=>$_paxMin,'PaxMax'=>$_paxMax,'RoomsMin'=>$_roomsMin,'RoomsMax'=>$_roomsMax,'BedroomsMin'=>$_bedroomsMin,'BedroomsMax'=>$_bedroomsMax,'BathroomsMin'=>$_bathroomsMin,'BathroomsMax'=>$_bathroomsMax,'QualityMin'=>$_qualityMin,'QualityMax'=>$_qualityMax,'DistanceToCenter'=>$_distanceToCenter,'DistanceToGolfCourse'=>$_distanceToGolfCourse,'DistanceToLake'=>$_distanceToLake,'DistanceToSea'=>$_distanceToSea,'DistanceToSeaOrLake'=>$_distanceToSeaOrLake,'DistanceToSkiLifts'=>$_distanceToSkiLifts,'LanguageCode'=>$_languageCode,'CurrencyCode'=>$_currencyCode,'SalesOfficeCode'=>$_salesOfficeCode,'Quicksearch'=>$_quicksearch,'CountryCode'=>$_countryCode,'RegionCode'=>$_regionCode,'PlaceCode'=>$_placeCode,'CheckIn'=>$_checkIn,'Facilities'=>($_facilities instanceof IHomeStructArrayOfFacilities)?$_facilities:new IHomeStructArrayOfFacilities($_facilities),'Accessibilities'=>($_accessibilities instanceof IHomeStructArrayOfAccessibilities)?$_accessibilities:new IHomeStructArrayOfAccessibilities($_accessibilities),'Activities'=>($_activities instanceof IHomeStructArrayOfActivities)?$_activities:new IHomeStructArrayOfActivities($_activities),'Situations'=>($_situations instanceof IHomeStructArrayOfSituations)?$_situations:new IHomeStructArrayOfSituations($_situations),'PropertyTypes'=>($_propertyTypes instanceof IHomeStructArrayOfPropertyTypes)?$_propertyTypes:new IHomeStructArrayOfPropertyTypes($_propertyTypes)));
	}
	/**
	 * Get Page value
	 * @return int
	 */
	public function getPage()
	{
		return $this->Page;
	}
	/**
	 * Set Page value
	 * @param int the Page
	 * @return int
	 */
	public function setPage($_page)
	{
		return ($this->Page = $_page);
	}
	/**
	 * Get PageSize value
	 * @return int
	 */
	public function getPageSize()
	{
		return $this->PageSize;
	}
	/**
	 * Set PageSize value
	 * @param int the PageSize
	 * @return int
	 */
	public function setPageSize($_pageSize)
	{
		return ($this->PageSize = $_pageSize);
	}
	/**
	 * Get OrderDirection value
	 * @return IHomeEnumOrderDirection
	 */
	public function getOrderDirection()
	{
		return $this->OrderDirection;
	}
	/**
	 * Set OrderDirection value
	 * @uses IHomeEnumOrderDirection::valueIsValid()
	 * @param IHomeEnumOrderDirection the OrderDirection
	 * @return IHomeEnumOrderDirection
	 */
	public function setOrderDirection($_orderDirection)
	{
		if(!IHomeEnumOrderDirection::valueIsValid($_orderDirection))
		{
			return false;
		}
		return ($this->OrderDirection = $_orderDirection);
	}
	/**
	 * Get OrderBy value
	 * @return IHomeEnumOrderBy
	 */
	public function getOrderBy()
	{
		return $this->OrderBy;
	}
	/**
	 * Set OrderBy value
	 * @uses IHomeEnumOrderBy::valueIsValid()
	 * @param IHomeEnumOrderBy the OrderBy
	 * @return IHomeEnumOrderBy
	 */
	public function setOrderBy($_orderBy)
	{
		if(!IHomeEnumOrderBy::valueIsValid($_orderBy))
		{
			return false;
		}
		return ($this->OrderBy = $_orderBy);
	}
	/**
	 * Get Duration value
	 * @return int
	 */
	public function getDuration()
	{
		return $this->Duration;
	}
	/**
	 * Set Duration value
	 * @param int the Duration
	 * @return int
	 */
	public function setDuration($_duration)
	{
		return ($this->Duration = $_duration);
	}
	/**
	 * Get ThemeFilter value
	 * @return IHomeEnumThemeFilterTypes
	 */
	public function getThemeFilter()
	{
		return $this->ThemeFilter;
	}
	/**
	 * Set ThemeFilter value
	 * @uses IHomeEnumThemeFilterTypes::valueIsValid()
	 * @param IHomeEnumThemeFilterTypes the ThemeFilter
	 * @return IHomeEnumThemeFilterTypes
	 */
	public function setThemeFilter($_themeFilter)
	{
		if(!IHomeEnumThemeFilterTypes::valueIsValid($_themeFilter))
		{
			return false;
		}
		return ($this->ThemeFilter = $_themeFilter);
	}
	/**
	 * Get HouseApartmentType value
	 * @return IHomeEnumHouseApartmentTypes
	 */
	public function getHouseApartmentType()
	{
		return $this->HouseApartmentType;
	}
	/**
	 * Set HouseApartmentType value
	 * @uses IHomeEnumHouseApartmentTypes::valueIsValid()
	 * @param IHomeEnumHouseApartmentTypes the HouseApartmentType
	 * @return IHomeEnumHouseApartmentTypes
	 */
	public function setHouseApartmentType($_houseApartmentType)
	{
		if(!IHomeEnumHouseApartmentTypes::valueIsValid($_houseApartmentType))
		{
			return false;
		}
		return ($this->HouseApartmentType = $_houseApartmentType);
	}
	/**
	 * Get SpecialOffer value
	 * @return IHomeEnumSpecialOffers
	 */
	public function getSpecialOffer()
	{
		return $this->SpecialOffer;
	}
	/**
	 * Set SpecialOffer value
	 * @uses IHomeEnumSpecialOffers::valueIsValid()
	 * @param IHomeEnumSpecialOffers the SpecialOffer
	 * @return IHomeEnumSpecialOffers
	 */
	public function setSpecialOffer($_specialOffer)
	{
		if(!IHomeEnumSpecialOffers::valueIsValid($_specialOffer))
		{
			return false;
		}
		return ($this->SpecialOffer = $_specialOffer);
	}
	/**
	 * Get PaxMin value
	 * @return int
	 */
	public function getPaxMin()
	{
		return $this->PaxMin;
	}
	/**
	 * Set PaxMin value
	 * @param int the PaxMin
	 * @return int
	 */
	public function setPaxMin($_paxMin)
	{
		return ($this->PaxMin = $_paxMin);
	}
	/**
	 * Get PaxMax value
	 * @return int
	 */
	public function getPaxMax()
	{
		return $this->PaxMax;
	}
	/**
	 * Set PaxMax value
	 * @param int the PaxMax
	 * @return int
	 */
	public function setPaxMax($_paxMax)
	{
		return ($this->PaxMax = $_paxMax);
	}
	/**
	 * Get RoomsMin value
	 * @return int
	 */
	public function getRoomsMin()
	{
		return $this->RoomsMin;
	}
	/**
	 * Set RoomsMin value
	 * @param int the RoomsMin
	 * @return int
	 */
	public function setRoomsMin($_roomsMin)
	{
		return ($this->RoomsMin = $_roomsMin);
	}
	/**
	 * Get RoomsMax value
	 * @return int
	 */
	public function getRoomsMax()
	{
		return $this->RoomsMax;
	}
	/**
	 * Set RoomsMax value
	 * @param int the RoomsMax
	 * @return int
	 */
	public function setRoomsMax($_roomsMax)
	{
		return ($this->RoomsMax = $_roomsMax);
	}
	/**
	 * Get BedroomsMin value
	 * @return int
	 */
	public function getBedroomsMin()
	{
		return $this->BedroomsMin;
	}
	/**
	 * Set BedroomsMin value
	 * @param int the BedroomsMin
	 * @return int
	 */
	public function setBedroomsMin($_bedroomsMin)
	{
		return ($this->BedroomsMin = $_bedroomsMin);
	}
	/**
	 * Get BedroomsMax value
	 * @return int
	 */
	public function getBedroomsMax()
	{
		return $this->BedroomsMax;
	}
	/**
	 * Set BedroomsMax value
	 * @param int the BedroomsMax
	 * @return int
	 */
	public function setBedroomsMax($_bedroomsMax)
	{
		return ($this->BedroomsMax = $_bedroomsMax);
	}
	/**
	 * Get BathroomsMin value
	 * @return int
	 */
	public function getBathroomsMin()
	{
		return $this->BathroomsMin;
	}
	/**
	 * Set BathroomsMin value
	 * @param int the BathroomsMin
	 * @return int
	 */
	public function setBathroomsMin($_bathroomsMin)
	{
		return ($this->BathroomsMin = $_bathroomsMin);
	}
	/**
	 * Get BathroomsMax value
	 * @return int
	 */
	public function getBathroomsMax()
	{
		return $this->BathroomsMax;
	}
	/**
	 * Set BathroomsMax value
	 * @param int the BathroomsMax
	 * @return int
	 */
	public function setBathroomsMax($_bathroomsMax)
	{
		return ($this->BathroomsMax = $_bathroomsMax);
	}
	/**
	 * Get QualityMin value
	 * @return int
	 */
	public function getQualityMin()
	{
		return $this->QualityMin;
	}
	/**
	 * Set QualityMin value
	 * @param int the QualityMin
	 * @return int
	 */
	public function setQualityMin($_qualityMin)
	{
		return ($this->QualityMin = $_qualityMin);
	}
	/**
	 * Get QualityMax value
	 * @return int
	 */
	public function getQualityMax()
	{
		return $this->QualityMax;
	}
	/**
	 * Set QualityMax value
	 * @param int the QualityMax
	 * @return int
	 */
	public function setQualityMax($_qualityMax)
	{
		return ($this->QualityMax = $_qualityMax);
	}
	/**
	 * Get DistanceToCenter value
	 * @return int
	 */
	public function getDistanceToCenter()
	{
		return $this->DistanceToCenter;
	}
	/**
	 * Set DistanceToCenter value
	 * @param int the DistanceToCenter
	 * @return int
	 */
	public function setDistanceToCenter($_distanceToCenter)
	{
		return ($this->DistanceToCenter = $_distanceToCenter);
	}
	/**
	 * Get DistanceToGolfCourse value
	 * @return int
	 */
	public function getDistanceToGolfCourse()
	{
		return $this->DistanceToGolfCourse;
	}
	/**
	 * Set DistanceToGolfCourse value
	 * @param int the DistanceToGolfCourse
	 * @return int
	 */
	public function setDistanceToGolfCourse($_distanceToGolfCourse)
	{
		return ($this->DistanceToGolfCourse = $_distanceToGolfCourse);
	}
	/**
	 * Get DistanceToLake value
	 * @return int
	 */
	public function getDistanceToLake()
	{
		return $this->DistanceToLake;
	}
	/**
	 * Set DistanceToLake value
	 * @param int the DistanceToLake
	 * @return int
	 */
	public function setDistanceToLake($_distanceToLake)
	{
		return ($this->DistanceToLake = $_distanceToLake);
	}
	/**
	 * Get DistanceToSea value
	 * @return int
	 */
	public function getDistanceToSea()
	{
		return $this->DistanceToSea;
	}
	/**
	 * Set DistanceToSea value
	 * @param int the DistanceToSea
	 * @return int
	 */
	public function setDistanceToSea($_distanceToSea)
	{
		return ($this->DistanceToSea = $_distanceToSea);
	}
	/**
	 * Get DistanceToSeaOrLake value
	 * @return int
	 */
	public function getDistanceToSeaOrLake()
	{
		return $this->DistanceToSeaOrLake;
	}
	/**
	 * Set DistanceToSeaOrLake value
	 * @param int the DistanceToSeaOrLake
	 * @return int
	 */
	public function setDistanceToSeaOrLake($_distanceToSeaOrLake)
	{
		return ($this->DistanceToSeaOrLake = $_distanceToSeaOrLake);
	}
	/**
	 * Get DistanceToSkiLifts value
	 * @return int
	 */
	public function getDistanceToSkiLifts()
	{
		return $this->DistanceToSkiLifts;
	}
	/**
	 * Set DistanceToSkiLifts value
	 * @param int the DistanceToSkiLifts
	 * @return int
	 */
	public function setDistanceToSkiLifts($_distanceToSkiLifts)
	{
		return ($this->DistanceToSkiLifts = $_distanceToSkiLifts);
	}
	/**
	 * Get LanguageCode value
	 * @return string|null
	 */
	public function getLanguageCode()
	{
		return $this->LanguageCode;
	}
	/**
	 * Set LanguageCode value
	 * @param string the LanguageCode
	 * @return string
	 */
	public function setLanguageCode($_languageCode)
	{
		return ($this->LanguageCode = $_languageCode);
	}
	/**
	 * Get CurrencyCode value
	 * @return string|null
	 */
	public function getCurrencyCode()
	{
		return $this->CurrencyCode;
	}
	/**
	 * Set CurrencyCode value
	 * @param string the CurrencyCode
	 * @return string
	 */
	public function setCurrencyCode($_currencyCode)
	{
		return ($this->CurrencyCode = $_currencyCode);
	}
	/**
	 * Get SalesOfficeCode value
	 * @return string|null
	 */
	public function getSalesOfficeCode()
	{
		return $this->SalesOfficeCode;
	}
	/**
	 * Set SalesOfficeCode value
	 * @param string the SalesOfficeCode
	 * @return string
	 */
	public function setSalesOfficeCode($_salesOfficeCode)
	{
		return ($this->SalesOfficeCode = $_salesOfficeCode);
	}
	/**
	 * Get Quicksearch value
	 * @return string|null
	 */
	public function getQuicksearch()
	{
		return $this->Quicksearch;
	}
	/**
	 * Set Quicksearch value
	 * @param string the Quicksearch
	 * @return string
	 */
	public function setQuicksearch($_quicksearch)
	{
		return ($this->Quicksearch = $_quicksearch);
	}
	/**
	 * Get CountryCode value
	 * @return string|null
	 */
	public function getCountryCode()
	{
		return $this->CountryCode;
	}
	/**
	 * Set CountryCode value
	 * @param string the CountryCode
	 * @return string
	 */
	public function setCountryCode($_countryCode)
	{
		return ($this->CountryCode = $_countryCode);
	}
	/**
	 * Get RegionCode value
	 * @return string|null
	 */
	public function getRegionCode()
	{
		return $this->RegionCode;
	}
	/**
	 * Set RegionCode value
	 * @param string the RegionCode
	 * @return string
	 */
	public function setRegionCode($_regionCode)
	{
		return ($this->RegionCode = $_regionCode);
	}
	/**
	 * Get PlaceCode value
	 * @return string|null
	 */
	public function getPlaceCode()
	{
		return $this->PlaceCode;
	}
	/**
	 * Set PlaceCode value
	 * @param string the PlaceCode
	 * @return string
	 */
	public function setPlaceCode($_placeCode)
	{
		return ($this->PlaceCode = $_placeCode);
	}
	/**
	 * Get CheckIn value
	 * @return string|null
	 */
	public function getCheckIn()
	{
		return $this->CheckIn;
	}
	/**
	 * Set CheckIn value
	 * @param string the CheckIn
	 * @return string
	 */
	public function setCheckIn($_checkIn)
	{
		return ($this->CheckIn = $_checkIn);
	}
	/**
	 * Get Facilities value
	 * @return IHomeStructArrayOfFacilities|null
	 */
	public function getFacilities()
	{
		return $this->Facilities;
	}
	/**
	 * Set Facilities value
	 * @param IHomeStructArrayOfFacilities the Facilities
	 * @return IHomeStructArrayOfFacilities
	 */
	public function setFacilities($_facilities)
	{
		return ($this->Facilities = $_facilities);
	}
	/**
	 * Get Accessibilities value
	 * @return IHomeStructArrayOfAccessibilities|null
	 */
	public function getAccessibilities()
	{
		return $this->Accessibilities;
	}
	/**
	 * Set Accessibilities value
	 * @param IHomeStructArrayOfAccessibilities the Accessibilities
	 * @return IHomeStructArrayOfAccessibilities
	 */
	public function setAccessibilities($_accessibilities)
	{
		return ($this->Accessibilities = $_accessibilities);
	}
	/**
	 * Get Activities value
	 * @return IHomeStructArrayOfActivities|null
	 */
	public function getActivities()
	{
		return $this->Activities;
	}
	/**
	 * Set Activities value
	 * @param IHomeStructArrayOfActivities the Activities
	 * @return IHomeStructArrayOfActivities
	 */
	public function setActivities($_activities)
	{
		return ($this->Activities = $_activities);
	}
	/**
	 * Get Situations value
	 * @return IHomeStructArrayOfSituations|null
	 */
	public function getSituations()
	{
		return $this->Situations;
	}
	/**
	 * Set Situations value
	 * @param IHomeStructArrayOfSituations the Situations
	 * @return IHomeStructArrayOfSituations
	 */
	public function setSituations($_situations)
	{
		return ($this->Situations = $_situations);
	}
	/**
	 * Get PropertyTypes value
	 * @return IHomeStructArrayOfPropertyTypes|null
	 */
	public function getPropertyTypes()
	{
		return $this->PropertyTypes;
	}
	/**
	 * Set PropertyTypes value
	 * @param IHomeStructArrayOfPropertyTypes the PropertyTypes
	 * @return IHomeStructArrayOfPropertyTypes
	 */
	public function setPropertyTypes($_propertyTypes)
	{
		return ($this->PropertyTypes = $_propertyTypes);
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
?>