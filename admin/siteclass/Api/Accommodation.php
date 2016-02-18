<?php
namespace Chalet\Api;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class Accommodation extends Endpoint
{
    /**
     * @var integer
     */
    const API_METHOD_GET_INFO = 1;

    /**
     * @var array
     */
    protected $methods = [

        self::API_METHOD_GET_INFO => [

            'method'   => 'getInfo',
            'required' => ['type_id', 'arrival_date', 'persons'],
        ],
    ];

    /**
     * @return array
     */
    public function getInfo()
    {
        $data = $this->request->query->all();

        return \accinfo($data['type_id'], $data['arrival_date'], $data['persons']);
    }
}
