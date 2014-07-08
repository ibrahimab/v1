<?php
/**
 * File for class NewyseServiceStructCustomer
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructCustomer originally named Customer
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructCustomer extends NewyseServiceWsdlClass
{
    /**
     * The CustomerId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $CustomerId;
    /**
     * The TitleCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $TitleCode;
    /**
     * The Firstname
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Firstname;
    /**
     * The Middle
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Middle;
    /**
     * The Lastname
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Lastname;
    /**
     * The BirthDate
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var dateTime
     */
    public $BirthDate;
    /**
     * The BankAccountTypeId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var long
     */
    public $BankAccountTypeId;
    /**
     * The BankAccountNumber
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var int
     */
    public $BankAccountNumber;
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
     * The MobilePhone
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $MobilePhone;
    /**
     * The MailAllowed
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var boolean
     */
    public $MailAllowed;
    /**
     * The EmailAllowed
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var boolean
     */
    public $EmailAllowed;
    /**
     * The AttentionOf
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $AttentionOf;
    /**
     * The Sex
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Sex;
    /**
     * The IbanNumber
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $IbanNumber;
    /**
     * The VatNumber
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $VatNumber;
    /**
     * The IsCompany
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var boolean
     */
    public $IsCompany;
    /**
     * The CompanyName
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $CompanyName;
    /**
     * The Department
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Department;
    /**
     * The District
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $District;
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
     * The Fax
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Fax;
    /**
     * The Address1
     * Meta informations extracted from the WSDL
     * - nillable : true
     * @var string
     */
    public $Address1;
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
     * The City
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $City;
    /**
     * The Zipcode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Zipcode;
    /**
     * The Country
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $Country;
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
     * Constructor method for Customer
     * @see parent::__construct()
     * @param long $_customerId
     * @param string $_titleCode
     * @param string $_firstname
     * @param string $_middle
     * @param string $_lastname
     * @param dateTime $_birthDate
     * @param long $_bankAccountTypeId
     * @param int $_bankAccountNumber
     * @param string $_privatePhone
     * @param string $_workPhone
     * @param string $_mobilePhone
     * @param boolean $_mailAllowed
     * @param boolean $_emailAllowed
     * @param string $_attentionOf
     * @param string $_sex
     * @param string $_ibanNumber
     * @param string $_vatNumber
     * @param boolean $_isCompany
     * @param string $_companyName
     * @param string $_department
     * @param string $_district
     * @param string $_poBox
     * @param string $_poBoxZipcode
     * @param string $_fax
     * @param string $_address1
     * @param string $_housenumber
     * @param string $_address2
     * @param string $_city
     * @param string $_zipcode
     * @param string $_country
     * @param double $_latitude
     * @param double $_longitude
     * @param string $_email
     * @return NewyseServiceStructCustomer
     */
    public function __construct($_customerId = NULL,$_titleCode = NULL,$_firstname = NULL,$_middle = NULL,$_lastname = NULL,$_birthDate = NULL,$_bankAccountTypeId = NULL,$_bankAccountNumber = NULL,$_privatePhone = NULL,$_workPhone = NULL,$_mobilePhone = NULL,$_mailAllowed = NULL,$_emailAllowed = NULL,$_attentionOf = NULL,$_sex = NULL,$_ibanNumber = NULL,$_vatNumber = NULL,$_isCompany = NULL,$_companyName = NULL,$_department = NULL,$_district = NULL,$_poBox = NULL,$_poBoxZipcode = NULL,$_fax = NULL,$_address1 = NULL,$_housenumber = NULL,$_address2 = NULL,$_city = NULL,$_zipcode = NULL,$_country = NULL,$_latitude = NULL,$_longitude = NULL,$_email = NULL)
    {
        parent::__construct(array('CustomerId'=>$_customerId,'TitleCode'=>$_titleCode,'Firstname'=>$_firstname,'Middle'=>$_middle,'Lastname'=>$_lastname,'BirthDate'=>$_birthDate,'BankAccountTypeId'=>$_bankAccountTypeId,'BankAccountNumber'=>$_bankAccountNumber,'PrivatePhone'=>$_privatePhone,'WorkPhone'=>$_workPhone,'MobilePhone'=>$_mobilePhone,'MailAllowed'=>$_mailAllowed,'EmailAllowed'=>$_emailAllowed,'AttentionOf'=>$_attentionOf,'Sex'=>$_sex,'IbanNumber'=>$_ibanNumber,'VatNumber'=>$_vatNumber,'IsCompany'=>$_isCompany,'CompanyName'=>$_companyName,'Department'=>$_department,'District'=>$_district,'PoBox'=>$_poBox,'PoBoxZipcode'=>$_poBoxZipcode,'Fax'=>$_fax,'Address1'=>$_address1,'Housenumber'=>$_housenumber,'Address2'=>$_address2,'City'=>$_city,'Zipcode'=>$_zipcode,'Country'=>$_country,'Latitude'=>$_latitude,'Longitude'=>$_longitude,'Email'=>$_email),false);
    }
    /**
     * Get CustomerId value
     * @return long|null
     */
    public function getCustomerId()
    {
        return $this->CustomerId;
    }
    /**
     * Set CustomerId value
     * @param long $_customerId the CustomerId
     * @return long
     */
    public function setCustomerId($_customerId)
    {
        return ($this->CustomerId = $_customerId);
    }
    /**
     * Get TitleCode value
     * @return string|null
     */
    public function getTitleCode()
    {
        return $this->TitleCode;
    }
    /**
     * Set TitleCode value
     * @param string $_titleCode the TitleCode
     * @return string
     */
    public function setTitleCode($_titleCode)
    {
        return ($this->TitleCode = $_titleCode);
    }
    /**
     * Get Firstname value
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->Firstname;
    }
    /**
     * Set Firstname value
     * @param string $_firstname the Firstname
     * @return string
     */
    public function setFirstname($_firstname)
    {
        return ($this->Firstname = $_firstname);
    }
    /**
     * Get Middle value
     * @return string|null
     */
    public function getMiddle()
    {
        return $this->Middle;
    }
    /**
     * Set Middle value
     * @param string $_middle the Middle
     * @return string
     */
    public function setMiddle($_middle)
    {
        return ($this->Middle = $_middle);
    }
    /**
     * Get Lastname value
     * @return string|null
     */
    public function getLastname()
    {
        return $this->Lastname;
    }
    /**
     * Set Lastname value
     * @param string $_lastname the Lastname
     * @return string
     */
    public function setLastname($_lastname)
    {
        return ($this->Lastname = $_lastname);
    }
    /**
     * Get BirthDate value
     * @return dateTime|null
     */
    public function getBirthDate()
    {
        return $this->BirthDate;
    }
    /**
     * Set BirthDate value
     * @param dateTime $_birthDate the BirthDate
     * @return dateTime
     */
    public function setBirthDate($_birthDate)
    {
        return ($this->BirthDate = $_birthDate);
    }
    /**
     * Get BankAccountTypeId value
     * @return long|null
     */
    public function getBankAccountTypeId()
    {
        return $this->BankAccountTypeId;
    }
    /**
     * Set BankAccountTypeId value
     * @param long $_bankAccountTypeId the BankAccountTypeId
     * @return long
     */
    public function setBankAccountTypeId($_bankAccountTypeId)
    {
        return ($this->BankAccountTypeId = $_bankAccountTypeId);
    }
    /**
     * Get BankAccountNumber value
     * @return int|null
     */
    public function getBankAccountNumber()
    {
        return $this->BankAccountNumber;
    }
    /**
     * Set BankAccountNumber value
     * @param int $_bankAccountNumber the BankAccountNumber
     * @return int
     */
    public function setBankAccountNumber($_bankAccountNumber)
    {
        return ($this->BankAccountNumber = $_bankAccountNumber);
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
     * Get MailAllowed value
     * @return boolean|null
     */
    public function getMailAllowed()
    {
        return $this->MailAllowed;
    }
    /**
     * Set MailAllowed value
     * @param boolean $_mailAllowed the MailAllowed
     * @return boolean
     */
    public function setMailAllowed($_mailAllowed)
    {
        return ($this->MailAllowed = $_mailAllowed);
    }
    /**
     * Get EmailAllowed value
     * @return boolean|null
     */
    public function getEmailAllowed()
    {
        return $this->EmailAllowed;
    }
    /**
     * Set EmailAllowed value
     * @param boolean $_emailAllowed the EmailAllowed
     * @return boolean
     */
    public function setEmailAllowed($_emailAllowed)
    {
        return ($this->EmailAllowed = $_emailAllowed);
    }
    /**
     * Get AttentionOf value
     * @return string|null
     */
    public function getAttentionOf()
    {
        return $this->AttentionOf;
    }
    /**
     * Set AttentionOf value
     * @param string $_attentionOf the AttentionOf
     * @return string
     */
    public function setAttentionOf($_attentionOf)
    {
        return ($this->AttentionOf = $_attentionOf);
    }
    /**
     * Get Sex value
     * @return string|null
     */
    public function getSex()
    {
        return $this->Sex;
    }
    /**
     * Set Sex value
     * @param string $_sex the Sex
     * @return string
     */
    public function setSex($_sex)
    {
        return ($this->Sex = $_sex);
    }
    /**
     * Get IbanNumber value
     * @return string|null
     */
    public function getIbanNumber()
    {
        return $this->IbanNumber;
    }
    /**
     * Set IbanNumber value
     * @param string $_ibanNumber the IbanNumber
     * @return string
     */
    public function setIbanNumber($_ibanNumber)
    {
        return ($this->IbanNumber = $_ibanNumber);
    }
    /**
     * Get VatNumber value
     * @return string|null
     */
    public function getVatNumber()
    {
        return $this->VatNumber;
    }
    /**
     * Set VatNumber value
     * @param string $_vatNumber the VatNumber
     * @return string
     */
    public function setVatNumber($_vatNumber)
    {
        return ($this->VatNumber = $_vatNumber);
    }
    /**
     * Get IsCompany value
     * @return boolean|null
     */
    public function getIsCompany()
    {
        return $this->IsCompany;
    }
    /**
     * Set IsCompany value
     * @param boolean $_isCompany the IsCompany
     * @return boolean
     */
    public function setIsCompany($_isCompany)
    {
        return ($this->IsCompany = $_isCompany);
    }
    /**
     * Get CompanyName value
     * @return string|null
     */
    public function getCompanyName()
    {
        return $this->CompanyName;
    }
    /**
     * Set CompanyName value
     * @param string $_companyName the CompanyName
     * @return string
     */
    public function setCompanyName($_companyName)
    {
        return ($this->CompanyName = $_companyName);
    }
    /**
     * Get Department value
     * @return string|null
     */
    public function getDepartment()
    {
        return $this->Department;
    }
    /**
     * Set Department value
     * @param string $_department the Department
     * @return string
     */
    public function setDepartment($_department)
    {
        return ($this->Department = $_department);
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
     * Get Country value
     * @return string|null
     */
    public function getCountry()
    {
        return $this->Country;
    }
    /**
     * Set Country value
     * @param string $_country the Country
     * @return string
     */
    public function setCountry($_country)
    {
        return ($this->Country = $_country);
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
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructCustomer
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
