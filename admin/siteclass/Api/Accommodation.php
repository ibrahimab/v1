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
            'required' => ['type_id'],
        ],
    ];

    /**
     * @return array
     */
    public function getInfo()
    {
        $typeId      = $this->request->query->get('type_id');
        $arrivalDate = $this->request->query->get('arrival_date', 0);
        $persons     = $this->request->query->get('persons', 0);

        return \accinfo($typeId, $arrivalDate, $persons);
    }
}
