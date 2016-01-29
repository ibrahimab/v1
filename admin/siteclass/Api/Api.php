<?php

namespace Chalet\Api;

use Chalet\Api\Exception as ApiException;

/**
 * @author  Ibrahim Abdullah
 * @package Chalet
 */
class Api
{
    /**
     * @var integer
     */
    const API_ENDPOINT_ADDITIONAL_COSTS = 1;

    /**
     * @var integer
     */
    const API_ENDPOINT_BOOKING          = 2;

    /**
     * @var integer
     */
    private $endpoint;

    /**
     * @var integer
     */
    private $method;

    /**
     * @var array
     */
    private $allowedEndpoints = [

        self::API_ENDPOINT_ADDITIONAL_COSTS,
        self::API_ENDPOINT_BOOKING,
    ];

    /**
     * @param string $endpoint
     */
    public function __construct($endpoint, $method)
    {
        if (!in_array($endpoint, $this->allowedEndpoints)) {
            throw new ApiException(sprintf('Your chosen endpoint %s is not allowed', $endpoint));
        }

        $this->endpoint = $endpoint;
        $this->method   = $method;
    }

    /**
     * @param array $data
     * @return Endpoint
     */
    public function getEndpoint($data)
    {
        switch ($this->endpoint) {

            case self::API_ENDPOINT_ADDITIONAL_COSTS:
                return new AdditionalCosts($this->method, $data);
            break;

            case self::API_ENDPOINT_BOOKING:
                return new Booking($this->method, $data);
            break;
        }
    }
}