<?php
namespace Chalet\XML\Import\DirektHolidays;

/**
 * UnavailabilityParser
 *
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class UnavailabilityParser
{
	/**
	 * @var SimpleXMLElement
	 */
	protected $xml;

	/**
	 * Constructor
	 *
	 * @param SimpleXMLElement $xml
	 */
	public function __construct($xml)
	{
		$this->xml = $xml;
	}

	/**
	 * @return array
	 */
	public function parse()
	{
		$taken			= [];
		$availabilities = $this->xml->HotelStays->HotelStay;

		if ($availabilities->count() === 0) {
			return [];
		}

		$closed     = [];
		$done       = false;
		$attributes = [];

		foreach ($availabilities as $availability) {

			$attributes['availability'] = $availability->Availability->attributes();
			$attributes['property']     = $availability->BasicPropertyInfo->attributes();

			$hotelcode = (string)$attributes['property']['HotelCode'];
			$status    = (string)$attributes['availability']['Status'];
			$start     = (string)$attributes['availability']['Start'];
			$end       = (string)$attributes['availability']['End'];

			if (!isset($closed[$hotelcode])) {
				$closed[$hotelcode] = [];
			}

			if ($status === 'Close') {
				$closed[$hotelcode][] = $this->extract($start, $end);
			}
		}

		$result = [];
		foreach ($closed as $hotelcode => $data) {
			$result[$hotelcode] = $this->calculate($data);
		}

		return $result;
	}

	/**
	 * @param string $start
	 * @param string $end
	 */
	public function extract($start, $end)
	{
		$start = \DateTime::createFromFormat('Y-m-d', $start);
		$start = dichtstbijzijnde_zaterdag($start->getTimestamp());

		$end = \DateTime::createFromFormat('Y-m-d', $end);
		$end = dichtstbijzijnde_zaterdag($end->getTimestamp());

		return ['start' => $start, 'end' => $end];
	}

	/**
	 * @param array $accommodationData
	 */
	public function calculate($accommodationData)
	{
		$result = [];
		foreach ($accommodationData as $data) {

			$weekend = $data['start'];
			while ($weekend < $data['end']) {

				$result[$weekend] = true;
				$interval		  = new \DateTime;
				$interval->setTimestamp($weekend);
				$weekend          = $interval->modify('+7 days')->getTimestamp();

				unset($interval);
			}
		}

		return $result;
	}

	public function getPrices()
	{


		$parser = new RateParser($xml->xml);
		return $parser->parse();
	}
}