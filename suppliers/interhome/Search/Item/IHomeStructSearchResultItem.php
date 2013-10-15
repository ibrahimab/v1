<?php
/**
 * File for class IHomeStructSearchResultItem
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructSearchResultItem originally named SearchResultItem
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructSearchResultItem extends IHomeWsdlClass
{
	/**
	 * The Price
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var decimal
	 */
	public $Price;
	/**
	 * The Quality
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Quality;
	/**
	 * The Pax
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Pax;
	/**
	 * The Rooms
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Rooms;
	/**
	 * The BedRooms
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $BedRooms;
	/**
	 * The Pets
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Pets;
	/**
	 * The Cots
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Cots;
	/**
	 * The AdditionBeds
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $AdditionBeds;
	/**
	 * The Parking
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $Parking;
	/**
	 * The TV
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $TV;
	/**
	 * The Dishwasher
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $Dishwasher;
	/**
	 * The Washingmachine
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $Washingmachine;
	/**
	 * The Aircondition
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $Aircondition;
	/**
	 * The Pool
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $Pool;
	/**
	 * The Tennis
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $Tennis;
	/**
	 * The Sauna
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $Sauna;
	/**
	 * The Wheelchair
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var boolean
	 */
	public $Wheelchair;
	/**
	 * The GeoLng
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var decimal
	 */
	public $GeoLng;
	/**
	 * The GeoLat
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var decimal
	 */
	public $GeoLat;
	/**
	 * The AccommodationCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $AccommodationCode;
	/**
	 * The Country
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Country;
	/**
	 * The CountryCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $CountryCode;
	/**
	 * The Region
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Region;
	/**
	 * The RegionCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $RegionCode;
	/**
	 * The Place
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Place;
	/**
	 * The PlaceCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $PlaceCode;
	/**
	 * The Zip
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Zip;
	/**
	 * The CurrencyCode
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $CurrencyCode;
	/**
	 * The Type
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Type;
	/**
	 * The InsideDescription
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $InsideDescription;
	/**
	 * The Picture
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Picture;
	/**
	 * Constructor method for SearchResultItem
	 * @see parent::__construct()
	 * @param decimal $_price
	 * @param int $_quality
	 * @param int $_pax
	 * @param int $_rooms
	 * @param int $_bedRooms
	 * @param int $_pets
	 * @param int $_cots
	 * @param int $_additionBeds
	 * @param boolean $_parking
	 * @param boolean $_tV
	 * @param boolean $_dishwasher
	 * @param boolean $_washingmachine
	 * @param boolean $_aircondition
	 * @param boolean $_pool
	 * @param boolean $_tennis
	 * @param boolean $_sauna
	 * @param boolean $_wheelchair
	 * @param decimal $_geoLng
	 * @param decimal $_geoLat
	 * @param string $_accommodationCode
	 * @param string $_country
	 * @param string $_countryCode
	 * @param string $_region
	 * @param string $_regionCode
	 * @param string $_place
	 * @param string $_placeCode
	 * @param string $_zip
	 * @param string $_currencyCode
	 * @param string $_type
	 * @param string $_insideDescription
	 * @param string $_picture
	 * @return IHomeStructSearchResultItem
	 */
	public function __construct($_price,$_quality,$_pax,$_rooms,$_bedRooms,$_pets,$_cots,$_additionBeds,$_parking,$_tV,$_dishwasher,$_washingmachine,$_aircondition,$_pool,$_tennis,$_sauna,$_wheelchair,$_geoLng,$_geoLat,$_accommodationCode = NULL,$_country = NULL,$_countryCode = NULL,$_region = NULL,$_regionCode = NULL,$_place = NULL,$_placeCode = NULL,$_zip = NULL,$_currencyCode = NULL,$_type = NULL,$_insideDescription = NULL,$_picture = NULL)
	{
		parent::__construct(array('Price'=>$_price,'Quality'=>$_quality,'Pax'=>$_pax,'Rooms'=>$_rooms,'BedRooms'=>$_bedRooms,'Pets'=>$_pets,'Cots'=>$_cots,'AdditionBeds'=>$_additionBeds,'Parking'=>$_parking,'TV'=>$_tV,'Dishwasher'=>$_dishwasher,'Washingmachine'=>$_washingmachine,'Aircondition'=>$_aircondition,'Pool'=>$_pool,'Tennis'=>$_tennis,'Sauna'=>$_sauna,'Wheelchair'=>$_wheelchair,'GeoLng'=>$_geoLng,'GeoLat'=>$_geoLat,'AccommodationCode'=>$_accommodationCode,'Country'=>$_country,'CountryCode'=>$_countryCode,'Region'=>$_region,'RegionCode'=>$_regionCode,'Place'=>$_place,'PlaceCode'=>$_placeCode,'Zip'=>$_zip,'CurrencyCode'=>$_currencyCode,'Type'=>$_type,'InsideDescription'=>$_insideDescription,'Picture'=>$_picture));
	}
	/**
	 * Get Price value
	 * @return decimal
	 */
	public function getPrice()
	{
		return $this->Price;
	}
	/**
	 * Set Price value
	 * @param decimal the Price
	 * @return decimal
	 */
	public function setPrice($_price)
	{
		return ($this->Price = $_price);
	}
	/**
	 * Get Quality value
	 * @return int
	 */
	public function getQuality()
	{
		return $this->Quality;
	}
	/**
	 * Set Quality value
	 * @param int the Quality
	 * @return int
	 */
	public function setQuality($_quality)
	{
		return ($this->Quality = $_quality);
	}
	/**
	 * Get Pax value
	 * @return int
	 */
	public function getPax()
	{
		return $this->Pax;
	}
	/**
	 * Set Pax value
	 * @param int the Pax
	 * @return int
	 */
	public function setPax($_pax)
	{
		return ($this->Pax = $_pax);
	}
	/**
	 * Get Rooms value
	 * @return int
	 */
	public function getRooms()
	{
		return $this->Rooms;
	}
	/**
	 * Set Rooms value
	 * @param int the Rooms
	 * @return int
	 */
	public function setRooms($_rooms)
	{
		return ($this->Rooms = $_rooms);
	}
	/**
	 * Get BedRooms value
	 * @return int
	 */
	public function getBedRooms()
	{
		return $this->BedRooms;
	}
	/**
	 * Set BedRooms value
	 * @param int the BedRooms
	 * @return int
	 */
	public function setBedRooms($_bedRooms)
	{
		return ($this->BedRooms = $_bedRooms);
	}
	/**
	 * Get Pets value
	 * @return int
	 */
	public function getPets()
	{
		return $this->Pets;
	}
	/**
	 * Set Pets value
	 * @param int the Pets
	 * @return int
	 */
	public function setPets($_pets)
	{
		return ($this->Pets = $_pets);
	}
	/**
	 * Get Cots value
	 * @return int
	 */
	public function getCots()
	{
		return $this->Cots;
	}
	/**
	 * Set Cots value
	 * @param int the Cots
	 * @return int
	 */
	public function setCots($_cots)
	{
		return ($this->Cots = $_cots);
	}
	/**
	 * Get AdditionBeds value
	 * @return int
	 */
	public function getAdditionBeds()
	{
		return $this->AdditionBeds;
	}
	/**
	 * Set AdditionBeds value
	 * @param int the AdditionBeds
	 * @return int
	 */
	public function setAdditionBeds($_additionBeds)
	{
		return ($this->AdditionBeds = $_additionBeds);
	}
	/**
	 * Get Parking value
	 * @return boolean
	 */
	public function getParking()
	{
		return $this->Parking;
	}
	/**
	 * Set Parking value
	 * @param boolean the Parking
	 * @return boolean
	 */
	public function setParking($_parking)
	{
		return ($this->Parking = $_parking);
	}
	/**
	 * Get TV value
	 * @return boolean
	 */
	public function getTV()
	{
		return $this->TV;
	}
	/**
	 * Set TV value
	 * @param boolean the TV
	 * @return boolean
	 */
	public function setTV($_tV)
	{
		return ($this->TV = $_tV);
	}
	/**
	 * Get Dishwasher value
	 * @return boolean
	 */
	public function getDishwasher()
	{
		return $this->Dishwasher;
	}
	/**
	 * Set Dishwasher value
	 * @param boolean the Dishwasher
	 * @return boolean
	 */
	public function setDishwasher($_dishwasher)
	{
		return ($this->Dishwasher = $_dishwasher);
	}
	/**
	 * Get Washingmachine value
	 * @return boolean
	 */
	public function getWashingmachine()
	{
		return $this->Washingmachine;
	}
	/**
	 * Set Washingmachine value
	 * @param boolean the Washingmachine
	 * @return boolean
	 */
	public function setWashingmachine($_washingmachine)
	{
		return ($this->Washingmachine = $_washingmachine);
	}
	/**
	 * Get Aircondition value
	 * @return boolean
	 */
	public function getAircondition()
	{
		return $this->Aircondition;
	}
	/**
	 * Set Aircondition value
	 * @param boolean the Aircondition
	 * @return boolean
	 */
	public function setAircondition($_aircondition)
	{
		return ($this->Aircondition = $_aircondition);
	}
	/**
	 * Get Pool value
	 * @return boolean
	 */
	public function getPool()
	{
		return $this->Pool;
	}
	/**
	 * Set Pool value
	 * @param boolean the Pool
	 * @return boolean
	 */
	public function setPool($_pool)
	{
		return ($this->Pool = $_pool);
	}
	/**
	 * Get Tennis value
	 * @return boolean
	 */
	public function getTennis()
	{
		return $this->Tennis;
	}
	/**
	 * Set Tennis value
	 * @param boolean the Tennis
	 * @return boolean
	 */
	public function setTennis($_tennis)
	{
		return ($this->Tennis = $_tennis);
	}
	/**
	 * Get Sauna value
	 * @return boolean
	 */
	public function getSauna()
	{
		return $this->Sauna;
	}
	/**
	 * Set Sauna value
	 * @param boolean the Sauna
	 * @return boolean
	 */
	public function setSauna($_sauna)
	{
		return ($this->Sauna = $_sauna);
	}
	/**
	 * Get Wheelchair value
	 * @return boolean
	 */
	public function getWheelchair()
	{
		return $this->Wheelchair;
	}
	/**
	 * Set Wheelchair value
	 * @param boolean the Wheelchair
	 * @return boolean
	 */
	public function setWheelchair($_wheelchair)
	{
		return ($this->Wheelchair = $_wheelchair);
	}
	/**
	 * Get GeoLng value
	 * @return decimal
	 */
	public function getGeoLng()
	{
		return $this->GeoLng;
	}
	/**
	 * Set GeoLng value
	 * @param decimal the GeoLng
	 * @return decimal
	 */
	public function setGeoLng($_geoLng)
	{
		return ($this->GeoLng = $_geoLng);
	}
	/**
	 * Get GeoLat value
	 * @return decimal
	 */
	public function getGeoLat()
	{
		return $this->GeoLat;
	}
	/**
	 * Set GeoLat value
	 * @param decimal the GeoLat
	 * @return decimal
	 */
	public function setGeoLat($_geoLat)
	{
		return ($this->GeoLat = $_geoLat);
	}
	/**
	 * Get AccommodationCode value
	 * @return string|null
	 */
	public function getAccommodationCode()
	{
		return $this->AccommodationCode;
	}
	/**
	 * Set AccommodationCode value
	 * @param string the AccommodationCode
	 * @return string
	 */
	public function setAccommodationCode($_accommodationCode)
	{
		return ($this->AccommodationCode = $_accommodationCode);
	}
	/**
	 * Get Country value
	 * @return string|null
	 */
	public function getCountry()
	{
		return $this->Country;
	}
	/**
	 * Set Country value
	 * @param string the Country
	 * @return string
	 */
	public function setCountry($_country)
	{
		return ($this->Country = $_country);
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
	 * Get Region value
	 * @return string|null
	 */
	public function getRegion()
	{
		return $this->Region;
	}
	/**
	 * Set Region value
	 * @param string the Region
	 * @return string
	 */
	public function setRegion($_region)
	{
		return ($this->Region = $_region);
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
	 * Get Place value
	 * @return string|null
	 */
	public function getPlace()
	{
		return $this->Place;
	}
	/**
	 * Set Place value
	 * @param string the Place
	 * @return string
	 */
	public function setPlace($_place)
	{
		return ($this->Place = $_place);
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
	 * Get Zip value
	 * @return string|null
	 */
	public function getZip()
	{
		return $this->Zip;
	}
	/**
	 * Set Zip value
	 * @param string the Zip
	 * @return string
	 */
	public function setZip($_zip)
	{
		return ($this->Zip = $_zip);
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
	 * Get Type value
	 * @return string|null
	 */
	public function getType()
	{
		return $this->Type;
	}
	/**
	 * Set Type value
	 * @param string the Type
	 * @return string
	 */
	public function setType($_type)
	{
		return ($this->Type = $_type);
	}
	/**
	 * Get InsideDescription value
	 * @return string|null
	 */
	public function getInsideDescription()
	{
		return $this->InsideDescription;
	}
	/**
	 * Set InsideDescription value
	 * @param string the InsideDescription
	 * @return string
	 */
	public function setInsideDescription($_insideDescription)
	{
		return ($this->InsideDescription = $_insideDescription);
	}
	/**
	 * Get Picture value
	 * @return string|null
	 */
	public function getPicture()
	{
		return $this->Picture;
	}
	/**
	 * Set Picture value
	 * @param string the Picture
	 * @return string
	 */
	public function setPicture($_picture)
	{
		return ($this->Picture = $_picture);
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