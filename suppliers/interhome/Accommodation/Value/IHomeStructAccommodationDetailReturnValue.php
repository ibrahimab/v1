<?php
/**
 * File for class IHomeStructAccommodationDetailReturnValue
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructAccommodationDetailReturnValue originally named AccommodationDetailReturnValue
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructAccommodationDetailReturnValue extends IHomeStructReturnValue
{
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
	 * The Location
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Location;
	/**
	 * The Interior
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Interior;
	/**
	 * The Tranquility
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Tranquility;
	/**
	 * The Kitchen
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $Kitchen;
	/**
	 * The AccessRoad
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 1
	 * @var int
	 */
	public $AccessRoad;
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
	 * The HouseName
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $HouseName;
	/**
	 * The Country
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Country;
	/**
	 * The Region
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Region;
	/**
	 * The Place
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Place;
	/**
	 * The Zip
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Zip;
	/**
	 * The Type
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Type;
	/**
	 * The PoolFrom
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $PoolFrom;
	/**
	 * The PoolTo
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $PoolTo;
	/**
	 * The InsideDescription
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $InsideDescription;
	/**
	 * The OutsideDescription
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $OutsideDescription;
	/**
	 * The Pictures
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var IHomeStructArrayOfString
	 */
	public $Pictures;
	/**
	 * Constructor method for AccommodationDetailReturnValue
	 * @see parent::__construct()
	 * @param int $_quality
	 * @param int $_pax
	 * @param int $_rooms
	 * @param int $_bedRooms
	 * @param int $_location
	 * @param int $_interior
	 * @param int $_tranquility
	 * @param int $_kitchen
	 * @param int $_accessRoad
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
	 * @param string $_houseName
	 * @param string $_country
	 * @param string $_region
	 * @param string $_place
	 * @param string $_zip
	 * @param string $_type
	 * @param string $_poolFrom
	 * @param string $_poolTo
	 * @param string $_insideDescription
	 * @param string $_outsideDescription
	 * @param IHomeStructArrayOfString $_pictures
	 * @return IHomeStructAccommodationDetailReturnValue
	 */
	public function __construct($_quality,$_pax,$_rooms,$_bedRooms,$_location,$_interior,$_tranquility,$_kitchen,$_accessRoad,$_pets,$_cots,$_additionBeds,$_parking,$_tV,$_dishwasher,$_washingmachine,$_aircondition,$_pool,$_tennis,$_sauna,$_wheelchair,$_geoLng,$_geoLat,$_accommodationCode = NULL,$_houseName = NULL,$_country = NULL,$_region = NULL,$_place = NULL,$_zip = NULL,$_type = NULL,$_poolFrom = NULL,$_poolTo = NULL,$_insideDescription = NULL,$_outsideDescription = NULL,$_pictures = NULL)
	{
		IHomeWsdlClass::__construct(array('Quality'=>$_quality,'Pax'=>$_pax,'Rooms'=>$_rooms,'BedRooms'=>$_bedRooms,'Location'=>$_location,'Interior'=>$_interior,'Tranquility'=>$_tranquility,'Kitchen'=>$_kitchen,'AccessRoad'=>$_accessRoad,'Pets'=>$_pets,'Cots'=>$_cots,'AdditionBeds'=>$_additionBeds,'Parking'=>$_parking,'TV'=>$_tV,'Dishwasher'=>$_dishwasher,'Washingmachine'=>$_washingmachine,'Aircondition'=>$_aircondition,'Pool'=>$_pool,'Tennis'=>$_tennis,'Sauna'=>$_sauna,'Wheelchair'=>$_wheelchair,'GeoLng'=>$_geoLng,'GeoLat'=>$_geoLat,'AccommodationCode'=>$_accommodationCode,'HouseName'=>$_houseName,'Country'=>$_country,'Region'=>$_region,'Place'=>$_place,'Zip'=>$_zip,'Type'=>$_type,'PoolFrom'=>$_poolFrom,'PoolTo'=>$_poolTo,'InsideDescription'=>$_insideDescription,'OutsideDescription'=>$_outsideDescription,'Pictures'=>($_pictures instanceof IHomeStructArrayOfString)?$_pictures:new IHomeStructArrayOfString($_pictures)));
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
	 * Get Location value
	 * @return int
	 */
	public function getLocation()
	{
		return $this->Location;
	}
	/**
	 * Set Location value
	 * @param int the Location
	 * @return int
	 */
	public function setLocation($_location)
	{
		return ($this->Location = $_location);
	}
	/**
	 * Get Interior value
	 * @return int
	 */
	public function getInterior()
	{
		return $this->Interior;
	}
	/**
	 * Set Interior value
	 * @param int the Interior
	 * @return int
	 */
	public function setInterior($_interior)
	{
		return ($this->Interior = $_interior);
	}
	/**
	 * Get Tranquility value
	 * @return int
	 */
	public function getTranquility()
	{
		return $this->Tranquility;
	}
	/**
	 * Set Tranquility value
	 * @param int the Tranquility
	 * @return int
	 */
	public function setTranquility($_tranquility)
	{
		return ($this->Tranquility = $_tranquility);
	}
	/**
	 * Get Kitchen value
	 * @return int
	 */
	public function getKitchen()
	{
		return $this->Kitchen;
	}
	/**
	 * Set Kitchen value
	 * @param int the Kitchen
	 * @return int
	 */
	public function setKitchen($_kitchen)
	{
		return ($this->Kitchen = $_kitchen);
	}
	/**
	 * Get AccessRoad value
	 * @return int
	 */
	public function getAccessRoad()
	{
		return $this->AccessRoad;
	}
	/**
	 * Set AccessRoad value
	 * @param int the AccessRoad
	 * @return int
	 */
	public function setAccessRoad($_accessRoad)
	{
		return ($this->AccessRoad = $_accessRoad);
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
	 * Get HouseName value
	 * @return string|null
	 */
	public function getHouseName()
	{
		return $this->HouseName;
	}
	/**
	 * Set HouseName value
	 * @param string the HouseName
	 * @return string
	 */
	public function setHouseName($_houseName)
	{
		return ($this->HouseName = $_houseName);
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
	 * Get PoolFrom value
	 * @return string|null
	 */
	public function getPoolFrom()
	{
		return $this->PoolFrom;
	}
	/**
	 * Set PoolFrom value
	 * @param string the PoolFrom
	 * @return string
	 */
	public function setPoolFrom($_poolFrom)
	{
		return ($this->PoolFrom = $_poolFrom);
	}
	/**
	 * Get PoolTo value
	 * @return string|null
	 */
	public function getPoolTo()
	{
		return $this->PoolTo;
	}
	/**
	 * Set PoolTo value
	 * @param string the PoolTo
	 * @return string
	 */
	public function setPoolTo($_poolTo)
	{
		return ($this->PoolTo = $_poolTo);
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
	 * Get OutsideDescription value
	 * @return string|null
	 */
	public function getOutsideDescription()
	{
		return $this->OutsideDescription;
	}
	/**
	 * Set OutsideDescription value
	 * @param string the OutsideDescription
	 * @return string
	 */
	public function setOutsideDescription($_outsideDescription)
	{
		return ($this->OutsideDescription = $_outsideDescription);
	}
	/**
	 * Get Pictures value
	 * @return IHomeStructArrayOfString|null
	 */
	public function getPictures()
	{
		return $this->Pictures;
	}
	/**
	 * Set Pictures value
	 * @param IHomeStructArrayOfString the Pictures
	 * @return IHomeStructArrayOfString
	 */
	public function setPictures($_pictures)
	{
		return ($this->Pictures = $_pictures);
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