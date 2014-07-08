<?php
/**
 * File for class NewyseServiceStructAddress
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructAddress originally named Address
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructAddress extends NewyseServiceWsdlClass
{
    /**
     * The AddressId
     * @var long
     */
    public $AddressId;
    /**
     * The Address1
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var string
     */
    public $Address1;
    /**
     * The ManagerId
     * @var long
     */
    public $ManagerId;
    /**
     * The Housenumber
     * @var string
     */
    public $Housenumber;
    /**
     * The Address2
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Address2;
    /**
     * The Zipcode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Zipcode;
    /**
     * The City
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $City;
    /**
     * The Latitude
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var double
     */
    public $Latitude;
    /**
     * The Longitude
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var double
     */
    public $Longitude;
    /**
     * The Email
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Email;
    /**
     * The MobilePhone
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $MobilePhone;
    /**
     * The PrivatePhone
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $PrivatePhone;
    /**
     * The WorkPhone
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $WorkPhone;
    /**
     * The Fax
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Fax;
    /**
     * The PoBox
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $PoBox;
    /**
     * The PoBoxZipcode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $PoBoxZipcode;
    /**
     * The District
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $District;
    /**
     * The Country
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var NewyseServiceStructCountry
     */
    public $Country;
    /**
     * Constructor method for Address
     * @see parent::__construct()
     * @param long $_addressId
     * @param string $_address1
     * @param long $_managerId
     * @param string $_housenumber
     * @param string $_address2
     * @param string $_zipcode
     * @param string $_city
     * @param double $_latitude
     * @param double $_longitude
     * @param string $_email
     * @param string $_mobilePhone
     * @param string $_privatePhone
     * @param string $_workPhone
     * @param string $_fax
     * @param string $_poBox
     * @param string $_poBoxZipcode
     * @param string $_district
     * @param NewyseServiceStructCountry $_country
     * @return NewyseServiceStructAddress
     */
    public function __construct($_addressId = NULL,$_address1 = NULL,$_managerId = NULL,$_housenumber = NULL,$_address2 = NULL,$_zipcode = NULL,$_city = NULL,$_latitude = NULL,$_longitude = NULL,$_email = NULL,$_mobilePhone = NULL,$_privatePhone = NULL,$_workPhone = NULL,$_fax = NULL,$_poBox = NULL,$_poBoxZipcode = NULL,$_district = NULL,$_country = NULL)
    {
        parent::__construct(array('AddressId'=>$_addressId,'Address1'=>$_address1,'ManagerId'=>$_managerId,'Housenumber'=>$_housenumber,'Address2'=>$_address2,'Zipcode'=>$_zipcode,'City'=>$_city,'Latitude'=>$_latitude,'Longitude'=>$_longitude,'Email'=>$_email,'MobilePhone'=>$_mobilePhone,'PrivatePhone'=>$_privatePhone,'WorkPhone'=>$_workPhone,'Fax'=>$_fax,'PoBox'=>$_poBox,'PoBoxZipcode'=>$_poBoxZipcode,'District'=>$_district,'Country'=>$_country),false);
    }
    /**
     * Get AddressId value
     * @return long|null
     */
    public function getAddressId()
    {
        return $this->AddressId;
    }
    /**
     * Set AddressId value
     * @param long $_addressId the AddressId
     * @return long
     */
    public function setAddressId($_addressId)
    {
        return ($this->AddressId = $_addressId);
    }
    /**
     * Get Address1 value
     * @return string|null
     */
    public function getAddress1()
    {
        return $this->Address1;
    }
    /**
     * Set Address1 value
     * @param string $_address1 the Address1
     * @return string
     */
    public function setAddress1($_address1)
    {
        return ($this->Address1 = $_address1);
    }
    /**
     * Get ManagerId value
     * @return long|null
     */
    public function getManagerId()
    {
        return $this->ManagerId;
    }
    /**
     * Set ManagerId value
     * @param long $_managerId the ManagerId
     * @return long
     */
    public function setManagerId($_managerId)
    {
        return ($this->ManagerId = $_managerId);
    }
    /**
     * Get Housenumber value
     * @return string|null
     */
    public function getHousenumber()
    {
        return $this->Housenumber;
    }
    /**
     * Set Housenumber value
     * @param string $_housenumber the Housenumber
     * @return string
     */
    public function setHousenumber($_housenumber)
    {
        return ($this->Housenumber = $_housenumber);
    }
    /**
     * Get Address2 value
     * @return string|null
     */
    public function getAddress2()
    {
        return $this->Address2;
    }
    /**
     * Set Address2 value
     * @param string $_address2 the Address2
     * @return string
     */
    public function setAddress2($_address2)
    {
        return ($this->Address2 = $_address2);
    }
    /**
     * Get Zipcode value
     * @return string|null
     */
    public function getZipcode()
    {
        return $this->Zipcode;
    }
    /**
     * Set Zipcode value
     * @param string $_zipcode the Zipcode
     * @return string
     */
    public function setZipcode($_zipcode)
    {
        return ($this->Zipcode = $_zipcode);
    }
    /**
     * Get City value
     * @return string|null
     */
    public function getCity()
    {
        return $this->City;
    }
    /**
     * Set City value
     * @param string $_city the City
     * @return string
     */
    public function setCity($_city)
    {
        return ($this->City = $_city);
    }
    /**
     * Get Latitude value
     * @return double|null
     */
    public function getLatitude()
    {
        return $this->Latitude;
    }
    /**
     * Set Latitude value
     * @param double $_latitude the Latitude
     * @return double
     */
    public function setLatitude($_latitude)
    {
        return ($this->Latitude = $_latitude);
    }
    /**
     * Get Longitude value
     * @return double|null
     */
    public function getLongitude()
    {
        return $this->Longitude;
    }
    /**
     * Set Longitude value
     * @param double $_longitude the Longitude
     * @return double
     */
    public function setLongitude($_longitude)
    {
        return ($this->Longitude = $_longitude);
    }
    /**
     * Get Email value
     * @return string|null
     */
    public function getEmail()
    {
        return $this->Email;
    }
    /**
     * Set Email value
     * @param string $_email the Email
     * @return string
     */
    public function setEmail($_email)
    {
        return ($this->Email = $_email);
    }
    /**
     * Get MobilePhone value
     * @return string|null
     */
    public function getMobilePhone()
    {
        return $this->MobilePhone;
    }
    /**
     * Set MobilePhone value
     * @param string $_mobilePhone the MobilePhone
     * @return string
     */
    public function setMobilePhone($_mobilePhone)
    {
        return ($this->MobilePhone = $_mobilePhone);
    }
    /**
     * Get PrivatePhone value
     * @return string|null
     */
    public function getPrivatePhone()
    {
        return $this->PrivatePhone;
    }
    /**
     * Set PrivatePhone value
     * @param string $_privatePhone the PrivatePhone
     * @return string
     */
    public function setPrivatePhone($_privatePhone)
    {
        return ($this->PrivatePhone = $_privatePhone);
    }
    /**
     * Get WorkPhone value
     * @return string|null
     */
    public function getWorkPhone()
    {
        return $this->WorkPhone;
    }
    /**
     * Set WorkPhone value
     * @param string $_workPhone the WorkPhone
     * @return string
     */
    public function setWorkPhone($_workPhone)
    {
        return ($this->WorkPhone = $_workPhone);
    }
    /**
     * Get Fax value
     * @return string|null
     */
    public function getFax()
    {
        return $this->Fax;
    }
    /**
     * Set Fax value
     * @param string $_fax the Fax
     * @return string
     */
    public function setFax($_fax)
    {
        return ($this->Fax = $_fax);
    }
    /**
     * Get PoBox value
     * @return string|null
     */
    public function getPoBox()
    {
        return $this->PoBox;
    }
    /**
     * Set PoBox value
     * @param string $_poBox the PoBox
     * @return string
     */
    public function setPoBox($_poBox)
    {
        return ($this->PoBox = $_poBox);
    }
    /**
     * Get PoBoxZipcode value
     * @return string|null
     */
    public function getPoBoxZipcode()
    {
        return $this->PoBoxZipcode;
    }
    /**
     * Set PoBoxZipcode value
     * @param string $_poBoxZipcode the PoBoxZipcode
     * @return string
     */
    public function setPoBoxZipcode($_poBoxZipcode)
    {
        return ($this->PoBoxZipcode = $_poBoxZipcode);
    }
    /**
     * Get District value
     * @return string|null
     */
    public function getDistrict()
    {
        return $this->District;
    }
    /**
     * Set District value
     * @param string $_district the District
     * @return string
     */
    public function setDistrict($_district)
    {
        return ($this->District = $_district);
    }
    /**
     * Get Country value
     * @return NewyseServiceStructCountry|null
     */
    public function getCountry()
    {
        return $this->Country;
    }
    /**
     * Set Country value
     * @param NewyseServiceStructCountry $_country the Country
     * @return NewyseServiceStructCountry
     */
    public function setCountry($_country)
    {
        return ($this->Country = $_country);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructAddress
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
