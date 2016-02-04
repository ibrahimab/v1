<?php

namespace Chalet\Api;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class Booking extends Endpoint
{
    /**
     * @var integer
     */
    const API_METHOD_GET_BOOKING_INFO = 1;

    /**
     * @var array
     */
    protected $methods = [

        self::API_METHOD_GET_BOOKING_INFO => [

            'method'   => 'getBookingInfo',
            'required' => ['booking_id'],
        ],
    ];

    /**
     * @return array
     */
    public function getBookingInfo()
    {
        return \get_boekinginfo($this->request->query->getInt('booking_id'));
    }
}