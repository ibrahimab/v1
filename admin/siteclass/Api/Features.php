<?php
namespace Chalet\Api;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class Features extends Endpoint
{
    /**
     * @var integer
     */
    const API_METHOD_ALL = 1;

    /**
     * @var array
     */
    protected $methods = [

        self::API_METHOD_ALL => [

            'method'   => 'getStartingPrices',
            'required' => ['type_id'],
        ],
    ];

    /**
     * @return array
     */
    public function getStartingPrices()
    {
        $features = new \kenmerken();

        return $features->get_kenmerken($this->request->query->get('type_id'), $this->request->query->get('data'));
    }
}