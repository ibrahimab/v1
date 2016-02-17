<?php
namespace Chalet\XML\Import\DirektHolidays;

/**
 * @author	Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class FeedFetcher
{
	/**
	 * @const integer
	 */
	const TYPE_AVAILABILITY	 = 1;

	/**
	 * @const integer
	 */
	const TYPE_PRICES		 = 2;

	/**
	 * @const integer
	 */
	const TYPE_PRODUCTS		 = 3;

	/**
	 * @var string
	 */
	private $availabilityUrl = 'https://www.direktholidays.at/OTA/OTA_HotelAvailRQ?agencyId=ota_chaletnl&agencyPin=2840579';

	/**
	 * @var string
	 */
	private $pricesUrl		 = 'https://www.direktholidays.at/OTA/OTA_HotelRatePlanRQ?agencyId=ota_chaletnl&agencyPin=2840579';

	/**
	 * @var string
	 */
	private $productsUrl	 = 'https://www.direktholidays.at/OTA/OTA_HotelProductRQ?agencyId=ota_chaletnl&agencyPin=2840579';

	/**
	 * @var string
	 */
	private $availabilityFile;

	/**
	 * @var string
	 */
	private $pricesFile;

	/**
	 * @var string
	 */
	private $productsFile;

	/**
	 * @param boolean $test
	 */
	public function __construct($test)
	{
		$this->test = $test;
	}

	/**
	 * @param string $filename
	 * @return FeedFetcher
	 */
	public function setAvailabilityFile($filename)
	{
		$this->availabilityFile = $filename;

		return $this;
	}

	/**
	 * @param string $filename
	 * @return FeedFetcher
	 */
	public function setPricesFile($filename)
	{
		$this->pricesFile = $filename;

		return $this;
	}

	/**
	 * @param string $filename
	 * @return FeedFetcher
	 */
	public function setProductsFile($filename)
	{
		$this->productsFile = $filename;

		return $this;
	}

	/**
	 * @param integer $type
	 * @return SimpleXMLElement
	 */
	public function fetch($type)
	{
		switch ($type) {

			case self::TYPE_AVAILABILITY:
				$xml = $this->getAvailability();
			break;

			case self::TYPE_PRICES:
				$xml = $this->getPrices();
			break;

			case self::TYPE_PRODUCTS:
				$xml = $this->getProducts();
			break;
		}

		return $xml;
	}

	/**
	 * @return SimpleXMLElement
	 */
	public function getAvailability()
	{
		if (null === $this->availabilityFile && true === $this->test) {
			throw new \Exception('Availability feed from DirektHolidays is not available');
		}

		if (true === $this->test) {
			return $this->loadXml($this->availabilityFile);
		}

		$data = <<<XML
		<OTA_HotelAvailRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.000">
			<AvailRequestSegments>
				<AvailRequestSegment/>
			</AvailRequestSegments>
		</OTA_HotelAvailRQ>
XML;
		return $this->loadXml($this->post($this->availabilityUrl, $data));
	}

	/**
	 * @return SimpleXMLElement
	 */
	public function getPrices()
	{
		if (null === $this->pricesFile && true === $this->test) {
			throw new \Exception('Prices feed from DirektHolidays is not available');
		}

		if (true === $this->test) {
			return $this->loadXml($this->pricesFile);
		}

		$data = <<<XML
		<OTA_HotelRatePlanRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.000">
			<RatePlans>
				<RatePlan/>
			</RatePlans>
		</OTA_HotelRatePlanRQ>
XML;
		return $this->loadXml($this->post($this->pricesUrl, $data));
	}

	/**
	 * @return SimpleXMLElement
	 */
	public function getProducts()
	{
		if (null === $this->productsFile && true === $this->test) {
			throw new \Exception('Products feed from DirektHolidays is not available');
		}

		if (true === $this->test) {
			return $this->loadXml($this->productsFile);
		}

		$data = <<<XML
		<OTA_HotelProductRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.000">
			<HotelProducts>
				<HotelProduct/>
			</HotelProducts>
		</OTA_HotelProductRQ>
XML;
		return $this->loadXml($this->post($this->productsUrl, $data));
	}

	/**
	 * @param string $url
	 * @param string $body
	 * @return string
	 */
	public function post($url, $body)
	{
		$request = curl_init();

		curl_setopt($request, CURLOPT_URL, $url);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request, CURLOPT_POSTFIELDS, $body);
		curl_setopt($request, CURLOPT_HEADER, false);
		curl_setopt($request, CURLOPT_HTTPHEADER, [

			'Content-type: application/xml',
			'Content-length: ' . strlen($body),
		]);

		$response = curl_exec($request);
		curl_close($request);

		return $response;
	}

	/**
	 * @param string $xml
	 *
	 * @return SimpleXMLElement
	 * @throws InvalidArgumentException
	 */
	public function loadXml($xml)
	{
		// supress php errors
		libxml_use_internal_errors(true);

		// try to load xml from string
		$xml = simplexml_load_string($xml);

		// getting errors
		$errors = libxml_get_errors();

		// and clearing the errors
		libxml_clear_errors();

		// enable php warnings for other parts of the system
		libxml_use_internal_errors(false);

		// throw exception if error arises
		if (count($errors) > 0) {
			throw new \InvalidArgumentException('XML string being loaded is not valid');
		}

		return $xml;
	}
}