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
            'required' => ['type_id', 'booking_id', 'season_id'],
        ],
    ];

    /**
     * @return array
     */
    public function getBookingData()
    {
        $additionalCosts = new \bijkomendekosten($this->request->query->getInt('type_id'), 'type');
        $additionalCosts->seizoen_id = $this->request->query->getInt('season_id');

        $booking = new Booking(Booking::API_METHOD_GET_BOOKING_INFO, $this->request);
        $result  = json_decode($booking->result(), true);

        return $additionalCosts->get_booking_data($result, true);
    }
}