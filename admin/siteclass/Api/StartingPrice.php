<?php
namespace Chalet\Api;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class StartingPrice extends Endpoint
{
    /**
     * @var integer
     */
    const API_METHOD_GET_STARTING_PRICES = 1;

    /**
     * @var array
     */
    protected $methods = [

        self::API_METHOD_GET_STARTING_PRICES => [

            'method'   => 'getStartingPrices',
            'required' => ['type_id'],
        ],
    ];

    /**
     * @return array
     */
    public function getStartingPrices()
    {
        $startingPrice = new \vanafprijs();

        return $startingPrice->get_vanafprijs(implode(',', $this->request->query->get('type_id')));
    }
}