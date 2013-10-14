<?php
/**
 * File for class IHomeServiceSearch
 * @package IHome
 * @subpackage Services
 *
 */
/**
 * This class stands for IHomeServiceSearch originally named Search
 * @package IHome
 * @subpackage Services
 *
 */
class IHomeServiceSearch extends IHomeWsdlClass
{
	/**
	 * Method to call the operation originally named Search
	 * Documentation : Search Request
	 * Meta informations extracted from the WSDL
	 * - SOAPHeader : required
	 * - SOAPHeaderNames : ServiceAuthHeader,ServiceAuthHeader
	 * - SOAPHeaderNamespace : http://www.interhome.com/webservice
	 * - SOAPHeaderTypes : {@link IHomeStructServiceAuthHeader},{@link IHomeStructServiceAuthHeader}
	 * @uses IHomeWsdlClass::getSoapClient()
	 * @uses IHomeWsdlClass::setResult()
	 * @uses IHomeWsdlClass::getResult()
	 * @uses IHomeWsdlClass::saveLastError()
	 * @uses IHomeStructSearch::getInputValue()
	 * @param IHomeStructSearch $_ihomeStructSearch
	 * @return IHomeStructSearchResponse
	 */
	public function Search(IHomeStructSearch $_ihomeStructSearch)
	{
		try
		{
			$this->setResult(new IHomeStructSearchResponse(self::getSoapClient()->Search(array('inputValue'=>$_ihomeStructSearch->getInputValue()))));
		}
		catch(SoapFault $soapFault)
		{
			return !$this->saveLastError(__METHOD__,$soapFault);
		}
		return $this->getResult();
	}
	/**
	 * Returns the result
	 * @see IHomeWsdlClass::getResult()
	 * @return IHomeStructSearchResponse
	 */
	public function getResult()
	{
		return parent::getResult();
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