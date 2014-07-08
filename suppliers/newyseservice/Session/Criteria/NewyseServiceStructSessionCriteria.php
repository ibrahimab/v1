<?php
/**
 * File for class NewyseServiceStructSessionCriteria
 * @package NewyseService
 * @subpackage Structs
 */
/**
 * This class stands for NewyseServiceStructSessionCriteria originally named SessionCriteria
 * @package NewyseService
 * @subpackage Structs
 */
class NewyseServiceStructSessionCriteria extends NewyseServiceWsdlClass
{
    /**
     * The Type
     * Meta informations extracted from the WSDL
     * - default : normal
     * - minOccurs : 0
     * @var string
     */
    public $Type;
    /**
     * The Username
     * @var string
     */
    public $Username;
    /**
     * The Password
     * @var string
     */
    public $Password;
    /**
     * The LanguageCode
     * Meta informations extracted from the WSDL
     * - default : nl
     * - minOccurs : 0
     * @var string
     */
    public $LanguageCode;
    /**
     * The DistributionchannelCode
     * @var string
     */
    public $DistributionchannelCode;
    /**
     * Constructor method for SessionCriteria
     * @see parent::__construct()
     * @param string $_type
     * @param string $_username
     * @param string $_password
     * @param string $_languageCode
     * @param string $_distributionchannelCode
     * @return NewyseServiceStructSessionCriteria
     */
    public function __construct($_type = 'normal',$_username = NULL,$_password = NULL,$_languageCode = 'nl',$_distributionchannelCode = NULL)
    {
        parent::__construct(array('Type'=>$_type,'Username'=>$_username,'Password'=>$_password,'LanguageCode'=>$_languageCode,'DistributionchannelCode'=>$_distributionchannelCode),false);
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
     * Get Username value
     * @return string|null
     */
    public function getUsername()
    {
        return $this->Username;
    }
    /**
     * Set Username value
     * @param string $_username the Username
     * @return string
     */
    public function setUsername($_username)
    {
        return ($this->Username = $_username);
    }
    /**
     * Get Password value
     * @return string|null
     */
    public function getPassword()
    {
        return $this->Password;
    }
    /**
     * Set Password value
     * @param string $_password the Password
     * @return string
     */
    public function setPassword($_password)
    {
        return ($this->Password = $_password);
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
     * @param string $_languageCode the LanguageCode
     * @return string
     */
    public function setLanguageCode($_languageCode)
    {
        return ($this->LanguageCode = $_languageCode);
    }
    /**
     * Get DistributionchannelCode value
     * @return string|null
     */
    public function getDistributionchannelCode()
    {
        return $this->DistributionchannelCode;
    }
    /**
     * Set DistributionchannelCode value
     * @param string $_distributionchannelCode the DistributionchannelCode
     * @return string
     */
    public function setDistributionchannelCode($_distributionchannelCode)
    {
        return ($this->DistributionchannelCode = $_distributionchannelCode);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NewyseServiceWsdlClass::__set_state()
     * @uses NewyseServiceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NewyseServiceStructSessionCriteria
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
