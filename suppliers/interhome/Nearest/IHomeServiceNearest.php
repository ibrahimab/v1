<?php
/**
 * File for class IHomeServiceNearest
 * @package IHome
 * @subpackage Services
 */
/**
 * This class stands for IHomeServiceNearest originally named Nearest
 * @package IHome
 * @subpackage Services
 */
class IHomeServiceNearest extends IHomeWsdlClass
{
	/**
	 * Method to call the operation originally named NearestBookingDate
	 * Documentation : NearestBookingDate Request
	 * Meta informations extracted from the WSDL
	 * - SOAPHeader : required
	 * - SOAPHeaderNames : ServiceAuthHeader,ServiceAuthHeader
	 * - SOAPHeaderNamespace : http://www.interhome.com/webservice
	 * - SOAPHeaderTypes : {@link IHomeStructServiceAuthHeader},{@link IHomeStructServiceAuthHeader}
	 * @uses IHomeWsdlClass::getSoapClient()
	 * @uses IHomeWsdlClass::setResult()
	 * @uses IHomeWsdlClass::getResult()
	 * @uses IHomeWsdlClass::saveLastError()
	 * @uses IHomeStructNearestBookingDate::getInputValue()
	 * @param IHomeStructNearestBookingDate $_ihomeStructNearestBookingDate
	 * @return IHomeStructNearestBookingDateResponse
	 */
	public function NearestBookingDate(IHomeStructNearestBookingDate $_ihomeStructNearestBookingDate)
	{
		try
		{
			$this->setResult(new IHomeStructNearestBookingDateResponse(self::getSoapClient()->NearestBookingDate(array('inputValue'=>$_ihomeStructNearestBookingDate->getInputValue()))));
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
	 * @return IHomeStructNearestBookingDateResponse
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