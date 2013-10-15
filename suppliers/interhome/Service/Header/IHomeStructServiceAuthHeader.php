<?php
/**
 * File for class IHomeStructServiceAuthHeader
 * @package IHome
 * @subpackage Structs
 *
 */
/**
 * This class stands for IHomeStructServiceAuthHeader originally named ServiceAuthHeader
 * @package IHome
 * @subpackage Structs
 *
 */
class IHomeStructServiceAuthHeader extends IHomeWsdlClass
{
	/**
	 * The Username
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Username;
	/**
	 * The Password
	 * Meta informations extracted from the WSDL
	 * - maxOccurs : 1
	 * - minOccurs : 0
	 * @var string
	 */
	public $Password;
	/**
	 * Constructor method for ServiceAuthHeader
	 * @see parent::__construct()
	 * @param string $_username
	 * @param string $_password
	 * @return IHomeStructServiceAuthHeader
	 */
	public function __construct($_username = NULL,$_password = NULL)
	{
		parent::__construct(array('Username'=>$_username,'Password'=>$_password));
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
	 * @param string the Username
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
	 * @param string the Password
	 * @return string
	 */
	public function setPassword($_password)
	{
		return ($this->Password = $_password);
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