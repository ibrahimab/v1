<?php
namespace Chalet\Api;

use Chalet\Api\Exception as ApiException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author  Ibrahim Abdullah
 * @package Chalet
 */
class Api
{
    /** @var integer */
    const API_ENDPOINT_ADDITIONAL_COSTS = 1;

    /** @var integer */
    const API_ENDPOINT_BOOKING          = 2;

    /** @var integer */
    const API_ENDPOINT_PRICE_TABLE      = 3;

    /** @var integer */
    const API_ENDPOINT_STARTING_PRICE   = 4;

    /** @var integer */
    const API_ENDPOINT_FEATURES         = 5;

    /** @var integer */
    const API_ENDPOINT_ACCOMMODATION    = 6;

    /** @var integer */
    const API_ENDPOINT_TRAVELSUM        = 7;

    /**
     * @var integer
     */
    private $endpoint;

    /**
     * @var integer
     */
    private $method;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var array
     */
    private $allowedEndpoints = [

        self::API_ENDPOINT_ADDITIONAL_COSTS,
        self::API_ENDPOINT_BOOKING,
        self::API_ENDPOINT_PRICE_TABLE,
        self::API_ENDPOINT_STARTING_PRICE,
        self::API_ENDPOINT_FEATURES,
        self::API_ENDPOINT_ACCOMMODATION,
        self::API_ENDPOINT_TRAVELSUM,
    ];

    /**
     * @param string $endpoint
     */
    public function __construct(Request $request)
    {
        $this->endpoint = $request->query->getInt('endpoint');
        $this->method   = $request->query->getInt('method');
        $this->request  = $request;

        if (!in_array($this->endpoint, $this->allowedEndpoints)) {
            throw new ApiException(sprintf('Your chosen endpoint %s is not allowed', $this->endpoint));
        }
    }

    /**
     * @param array $data
     * @return Endpoint
     */
    public function getEndpoint()
    {
        switch ($this->endpoint) {

            case self::API_ENDPOINT_ADDITIONAL_COSTS:
                return new AdditionalCosts($this->request);
            break;

            case self::API_ENDPOINT_BOOKING:
                return new Booking($this->request);
            break;

            case self::API_ENDPOINT_PRICE_TABLE:
                return new PriceTable($this->request);
            break;

            case self::API_ENDPOINT_STARTING_PRICE:
                return new StartingPrice($this->request);
            break;

            case self::API_ENDPOINT_FEATURES:
                return new Features($this->request);
            break;

            case self::API_ENDPOINT_ACCOMMODATION:
                return new Accommodation($this->request);
            break;

            case self::API_ENDPOINT_TRAVELSUM:
                return new Travelsum($this->request);
            break;
        }
    }
}