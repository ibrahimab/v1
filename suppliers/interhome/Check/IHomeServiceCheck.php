<?php
/**
 * File for class IHomeServiceCheck
 * @package IHome
 * @subpackage Services
 *
 */
/**
 * This class stands for IHomeServiceCheck originally named Check
 * @package IHome
 * @subpackage Services
 *
 */
class IHomeServiceCheck extends IHomeWsdlClass
{
	/**
	 * Method to call the operation originally named CheckServerHealth
	 * Documentation : This method is used to Monitor if the WebService is available
	 * @uses IHomeWsdlClass::getSoapClient()
	 * @uses IHomeWsdlClass::setResult()
	 * @uses IHomeWsdlClass::getResult()
	 * @uses IHomeWsdlClass::saveLastError()
	 * @return IHomeStructCheckServerHealthResponse
	 */
	public function CheckServerHealth()
	{
		try
		{
			$this->setResult(new IHomeStructCheckServerHealthResponse(self::getSoapClient()->CheckServerHealth()));
		}
		catch(SoapFault $soapFault)
		{
			return !$this->saveLastError(__METHOD__,$soapFault);
		}
		return $this->getResult();
	}
	/**
	 * Method to call the operation originally named CheckServerHealthV2
	 * Documentation : This method is used to Monitor if the WebService is available. The 'type' parameter defines what to perform.
	 * @uses IHomeWsdlClass::getSoapClient()
	 * @uses IHomeWsdlClass::setResult()
	 * @uses IHomeWsdlClass::getResult()
	 * @uses IHomeWsdlClass::saveLastError()
	 * @uses IHomeStructCheckServerHealthV2::getType()
	 * @param IHomeStructCheckServerHealthV2 $_ihomeStructCheckServerHealthV2
	 * @return IHomeStructCheckServerHealthV2Response
	 */
	public function CheckServerHealthV2(IHomeStructCheckServerHealthV2 $_ihomeStructCheckServerHealthV2)
	{
		try
		{
			$this->setResult(new IHomeStructCheckServerHealthV2Response(self::getSoapClient()->CheckServerHealthV2(array('type'=>$_ihomeStructCheckServerHealthV2->getType()))));
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
	 * @return IHomeStructCheckServerHealthResponse|IHomeStructCheckServerHealthV2Response
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