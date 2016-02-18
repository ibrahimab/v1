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
            'required' => [],
        ],
    ];

    /**
     * @return array
     */
    public function getStartingPrices()
    {
        $startingPrice = new \vanafprijs();
        $startingPrice->newWebsite = true;

        $data = json_decode($this->request->getContent(), true);

        return (count($data) > 0 ? ($startingPrice->get_vanafprijs(implode(',', $data['type_id']))) : []);
    }
}
