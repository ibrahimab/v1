<?php

namespace Chalet\Api;

use Chalet\Api\Exception as ApiException;
use Chalet\Encoder\Encoder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
abstract class Endpoint
{
    /**
     * @var integer
     */
    protected $method;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param integer $method
     * @param array $data
     * @throws ApiException
     */
    public function __construct(Request $request)
    {
        $this->method  = $request->query->getInt('method');
        $this->request = $request;

        if (!array_key_exists($this->method, $this->methods)) {
            throw new ApiException(sprintf('Could not find method %s for api endpoint %s', $this->method, static::class));
        }

        $this->checkRequired();
    }

    /**
     * @throws ApiException
     * @return void
     */
    public function checkRequired()
    {
        foreach ($this->methods[$this->method]['required'] as $field) {

            if (false === $this->request->query->has($field)) {
                throw new ApiException(sprintf('Could not find required data key %s', $field));
            }
        }
    }

    /**
     * @return string
     */
    public function result()
    {
        $encoder = new Encoder(Encoder::ENCODING_UTF8);
        $result  = $this->{$this->methods[$this->method]['method']}();

        if (is_array($result)) {
            $result = $encoder->fix($result);
        }

        return $result;
    }
}