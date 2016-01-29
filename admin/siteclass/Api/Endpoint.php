<?php

namespace Chalet\Api;

use Chalet\Api\Exception as ApiException;
use Chalet\Encoder\Encoder;

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
	 * @var array
	 */
	protected $data;

	/**
	 * @param integer $method
	 * @param array $data
	 * @throws ApiException
	 */
	public function __construct($method, $data)
	{
		if (!array_key_exists($method, $this->methods)) {
			throw new ApiException(sprintf('Could not find method %s for api endpoint %s', $method, static::class));
		}

		$this->method = (int)$method;
		$this->data   = $data;

		$this->checkRequired();
	}

	/**
	 * @throws ApiException
	 * @return void
	 */
	public function checkRequired()
	{
		foreach ($this->methods[$this->method]['required'] as $field) {

			if (!array_key_exists($field, $this->data)) {
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
		$result  = $encoder->fix($result);

		return json_encode($result);
	}
}