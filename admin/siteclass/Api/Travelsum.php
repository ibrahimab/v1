<?php
namespace Chalet\Api;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class Travelsum extends Endpoint
{
    /**
     * @var integer
     */
    const API_METHOD_TABLE = 1;

    /**
     * @var array
     */
    protected $methods = [

        self::API_METHOD_TABLE => [

            'method'   => 'table',
            'required' => ['booking_id', 'type_id', 'arrival_date', 'persons'],
        ],
    ];

    /**
     * @return array
     */
    public function table()
    {
        $request = new Request([

            'endpoint'   => Api::API_ENDPOINT_BOOKING,
            'method'     => Booking::API_METHOD_GET_BOOKING_INFO,
            'booking_id' => $this->request->query->get('booking_id')
        ]);

        $bookingRequest = new Booking($request);
        $booking        = $bookingRequest->result();

        $request = new Request([

            'endpoint'     => Api::API_ENDPOINT_ACCOMMODATION,
            'method'       => Accommodation::API_METHOD_GET_INFO,
            'type_id'      => $this->request->query->get('type_id'),
            'arrival_date' => $this->request->query->get('arrival_date'),
            'persons'      => $this->request->query->get('persons'),
        ]);

        $accommodationRequest = new Accommodation($request);
        $accommodation        = $accommodationRequest->getInfo();

        $table = reissom_tabel($booking, $accommodation);

        return [

            'booking'       => $booking,
            'accommodation' => $accommodation,
            'table'         => $table,
        ];
    }
}
