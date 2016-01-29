<?php

namespace Chalet\Api;

use Chalet\Api\Exception as ApiException;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
abstract class Endpoint
{
	/**
	 * @var integer
	 */
	private $method;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var array
	 */
	private $requiredFields;

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

		$this->method = $method;
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
		$result = $this->{$this->methods[$this->method]['method']};

		return json_encode($result);
	}
}