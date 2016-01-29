<?php

namespace Chalet\Api;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class AdditionalCosts extends Endpoint
{
	/**
	 * @var integer
	 */
	const API_METHOD_GET_BOOKING_DATA = 1;

	/**
	 * @var array
	 */
	protected $methods = [

		self::API_METHOD_GET_BOOKING_DATA => [

			'method'   => 'getBookingData',
			'required' => ['typeid', 'bookingid'],
		],
	];

	/**
	 * @return array
	 */
	public function getBookingData()
	{
		$additionalCosts = new \bijkomendekosten($this->data['typeid'], 'type');
		$additionalCosts->seizoen_id = $seasonId;

		$booking = new Booking(Booking::API_METHOD_GET_BOOKING_INFO, $this->data);
		$result  = $booking->result();

		return $additionalCosts->get_booking_data(json_decode($result, true));
	}
}