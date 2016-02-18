<?php
namespace Chalet\Api;

use Symfony\Component\HttpFoundation\Request;

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
     * @var integer
     */
    const API_METHOD_GET_COMPLETE_CACHE = 2;

    /**
     * @var integer
     */
    const API_METHOD_GET_COMPLETE_CACHE_PER_PERSONS = 3;

    /**
     * @var integer
     */
    const API_METHOD_GET_COSTS = 4;

    /**
     * @var array
     */
    protected $methods = [

        self::API_METHOD_GET_BOOKING_DATA => [

            'method'   => 'getBookingData',
            'required' => ['type_id', 'booking_id', 'season_id'],
        ],

        self::API_METHOD_GET_COMPLETE_CACHE => [

            'method'   => 'getCompleteCache',
            'required' => ['season_type'],
        ],

        self::API_METHOD_GET_COMPLETE_CACHE_PER_PERSONS => [

            'method'   => 'getCompleteCachePerPersons',
            'required' => ['season_type', 'persons'],
        ],

        self::API_METHOD_GET_COSTS => [

            'method'   => 'getCosts',
            'required' => ['id', 'type', 'season_id', 'popup'],
            'optional' => ['arrangement'],
        ],
    ];

    /**
     * @return array
     */
    public function getBookingData()
    {
        $additionalCosts = new \bijkomendekosten($this->request->query->getInt('type_id'), 'type');
        $additionalCosts->setRedis(new \wt_redis);
        $additionalCosts->newWebsite = true;
        $additionalCosts->seizoen_id = $this->request->query->getInt('season_id');

        $get     = array_replace($_GET, ['endpoint' => Api::API_ENDPOINT_BOOKING, 'method' => Booking::API_METHOD_GET_BOOKING_INFO]);
        $request = new Request($get);

        $booking = new Booking($request);
        $result  = json_decode($booking->result(), true);

        return $additionalCosts->get_booking_data($result, true);
    }

    /**
     * @return array
     */
    public function getCompleteCache()
    {
        $additionalCosts = new \bijkomendekosten();
        $additionalCosts->setRedis(new \wt_redis);

        return $additionalCosts->get_complete_cache($this->request->query->get('season_type'));
    }

    /**
     * @return array
     */
    public function getCompleteCachePerPersons()
    {
        $additionalCosts = new \bijkomendekosten();
        $additionalCosts->setRedis(new \wt_redis);

        return $additionalCosts->get_complete_cache_per_persons($this->request->query->get('season_type'), $this->request->query->get('persons'));
    }

    /**
     * @return array
     */
    public function getCosts()
    {
        $additionalCosts = new \bijkomendekosten($this->request->query->get('id'), $this->request->query->get('type'));
        $additionalCosts->setRedis(new \wt_redis);
        $additionalCosts->seizoen_id = $this->request->query->get('season_id');

        if (true === $this->request->query->getBoolean('arrangement', false)) {
            $additionalCosts->arrangement = true;
        }

        if (true === $this->request->query->getBoolean('popup')) {
            $additionalCosts->zoek_en_boek_popup = true;
        }

        return $additionalCosts->get_costs();
    }
}
