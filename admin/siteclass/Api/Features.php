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
     * @var integer: get all features for the front-end
     *
     */
    const API_METHOD_FRONTEND = 1;

    /**
     * @var integer: get all features for the back-end (used in CMS)
     */
    const API_METHOD_BACKEND = 2;

    /**
     * @var array
     */
    protected $methods = [

        self::API_METHOD_FRONTEND => [

            'method'   => 'getFeatures',
            'required' => ['type_id'],
        ],

        self::API_METHOD_BACKEND => [

            'method'   => 'getFeaturesBackEnd',
            'required' => ['type_id'],
        ],

    ];

    /**
     * @return array
     */
    public function getFeatures()
    {
        $features = new \kenmerken();
        $features->newWebsite = true;

        return $features->get_kenmerken($this->request->query->get('type_id'), $this->request->query->get('data'));
    }

    /**
     * @return array
     */
    public function getFeaturesBackEnd()
    {
        $features = new \kenmerken();
        $features->newWebsite = true;

        return $features->get_kenmerken_backend($this->request->query->get('type_id'), $this->request->query->get('data'));
    }
}
