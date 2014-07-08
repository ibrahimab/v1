<?php
/**
 * File for class NewyseServiceStructReservationCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructReservationCriteria originally named ReservationCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructReservationCriteria extends NewyseServiceWsdlClass
{
    /**
     * The ReservationCategoryCode
     * @var string
     */
    public $ReservationCategoryCode;
    /**
     * The Accommodation
     * @var NewyseServiceStructAccommodation
     */
    public $Accommodation;
    /**
     * The Preferences
     * @var NewyseServiceStructPreferences
     */
    public $Preferences;
    /**
     * The Language
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Language;
    /**
     * The SubjectQuantities
     * @var NewyseServiceStructSubjectQuantities
     */
    public $SubjectQuantities;
    /**
     * The CustomerId
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var long
     */
    public $CustomerId;
    /**
     * The Remark
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Remark;
    /**
     * The Voucher
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * @var string
     */
    public $Voucher;
    /**
     * The SourceCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $SourceCode;
    /**
     * The SendMethodCode
     * Meta informations extracted from the WSDL
     * - minOccurs : 0
     * - nillable : true
     * @var string
     */
    public $SendMethodCode;
    /**
     * The Additions
     * @var NewyseServiceStructAdditions
     */
    public $Additions;
    /**
     * The ReturnBill
     * Meta informations extracted from the WSDL
     * - default : false
     * - minOccurs : 0
     * @var boolean
     */
    public $ReturnBill;
    /**
     * Constructor method for ReservationCriteria
     * @see parent::__construct()
     * @param string $_reservationCategoryCode
     * @param NewyseServiceStructAccommodation $_accommodation
     * @param NewyseServiceStructPreferences $_preferences
     * @param string $_language
     * @param NewyseServiceStructSubjectQuantities $_subjectQuantities
     * @param long $_customerId
     * @param string $_remark
     * @param string $_voucher
     * @param string $_sourceCode
     * @param string $_sendMethodCode
     * @param NewyseServiceStructAdditions $_additions
     * @param boolean $_returnBill
     * @return NewyseServiceStructReservationCriteria
     */
    public function __construct($_reservationCategoryCode = NULL,$_accommodation = NULL,$_preferences = NULL,$_language = NULL,$_subjectQuantities = NULL,$_customerId = NULL,$_remark = NULL,$_voucher = NULL,$_sourceCode = NULL,$_sendMethodCode = NULL,$_additions = NULL,$_returnBill = false)
    {
        parent::__construct(array('ReservationCategoryCode'=>$_reservationCategoryCode,'Accommodation'=>$_accommodation,'Preferences'=>$_preferences,'Language'=>$_language,'SubjectQuantities'=>$_subjectQuantities,'CustomerId'=>$_customerId,'Remark'=>$_remark,'Voucher'=>$_voucher,'SourceCode'=>$_sourceCode,'SendMethodCode'=>$_sendMethodCode,'Additions'=>$_additions,'ReturnBill'=>$_returnBill),false);
    }
    /**
     * Get ReservationCategoryCode value
     * @return string|null
     */
    public function getReservationCategoryCode()
    {
        return $this->ReservationCategoryCode;
    }
    /**
     * Set ReservationCategoryCode value
     * @param string $_reservationCategoryCode the ReservationCategoryCode
     * @return string
     */
    public function setReservationCategoryCode($_reservationCategoryCode)
    {
        return ($this->ReservationCategoryCode = $_reservationCategoryCode);
    }
    /**
     * Get Accommodation value
     * @return NewyseServiceStructAccommodation|null
     */
    public function getAccommodation()
    {
        return $this->Accommodation;
    }
    /**
     * Set Accommodation value
     * @param NewyseServiceStructAccommodation $_accommodation the Accommodation
     * @return NewyseServiceStructAccommodation
     */
    public function setAccommodation($_accommodation)
    {
        return ($this->Accommodation = $_accommodation);
    }
    /**
     * Get Preferences value
     * @return NewyseServiceStructPreferences|null
     */
    public function getPreferences()
    {
        return $this->Preferences;
    }
    /**
     * Set Preferences value
     * @param NewyseServiceStructPreferences $_preferences the Preferences
     * @return NewyseServiceStructPreferences
     */
    public function setPreferences($_preferences)
    {
        return ($this->Preferences = $_preferences);
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
     * Get SubjectQuantities value
     * @return NewyseServiceStructSubjectQuantities|null
     */
    public function getSubjectQuantities()
    {
        return $this->SubjectQuantities;
    }
    /**
     * Set SubjectQuantities value
     * @param NewyseServiceStructSubjectQuantities $_subjectQuantities the SubjectQuantities
     * @return NewyseServiceStructSubjectQuantities
     */
    public function setSubjectQuantities($_subjectQuantities)
    {
        return ($this->SubjectQuantities = $_subjectQuantities);
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
     * Get Remark value
     * @return string|null
     */
    public function getRemark()
    {
        return $this->Remark;
    }
    /**
     * Set Remark value
     * @param string $_remark the Remark
     * @return string
     */
    public function setRemark($_remark)
    {
        return ($this->Remark = $_remark);
    }
    /**
     * Get Voucher value
     * @return string|null
     */
    public function getVoucher()
    {
        return $this->Voucher;
    }
    /**
     * Set Voucher value
     * @param string $_voucher the Voucher
     * @return string
     */
    public function setVoucher($_voucher)
    {
        return ($this->Voucher = $_voucher);
    }
    /**
     * Get SourceCode value
     * @return string|null
     */
    public function getSourceCode()
    {
        return $this->SourceCode;
    }
    /**
     * Set SourceCode value
     * @param string $_sourceCode the SourceCode
     * @return string
     */
    public function setSourceCode($_sourceCode)
    {
        return ($this->SourceCode = $_sourceCode);
    }
    /**
     * Get SendMethodCode value
     * @return string|null
     */
    public function getSendMethodCode()
    {
        return $this->SendMethodCode;
    }
    /**
     * Set SendMethodCode value
     * @param string $_sendMethodCode the SendMethodCode
     * @return string
     */
    public function setSendMethodCode($_sendMethodCode)
    {
        return ($this->SendMethodCode = $_sendMethodCode);
    }
    /**
     * Get Additions value
     * @return NewyseServiceStructAdditions|null
     */
    public function getAdditions()
    {
        return $this->Additions;
    }
    /**
     * Set Additions value
     * @param NewyseServiceStructAdditions $_additions the Additions
     * @return NewyseServiceStructAdditions
     */
    public function setAdditions($_additions)
    {
        return ($this->Additions = $_additions);
    }
    /**
     * Get ReturnBill value
     * @return boolean|null
     */
    public function getReturnBill()
    {
        return $this->ReturnBill;
    }
    /**
     * Set ReturnBill value
     * @param boolean $_returnBill the ReturnBill
     * @return boolean
     */
    public function setReturnBill($_returnBill)
    {
        return ($this->ReturnBill = $_returnBill);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructReservationCriteria
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
