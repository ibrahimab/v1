<?php
namespace Chalet\Api\Auth;

use Chalet\Api\Auth\UnauthorizedException;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class Auth
{
	/**
	 * @var array
	 */
	private $allowed;

	/**
	 * @param RedisInterface $redis
	 */
	public function __construct(array $allowed)
	{
		$this->allowed = $allowed;
	}

	/**
	 * Check if ip address is allowed to send api requests
	 *
	 * @param string $user
	 * @param string $signature
	 *
	 * @return string
	 * @throws AuthenticationFailedException
	 */
	public function authenticate($address)
	{
		if (false === in_array($address, $this->allowed)) {
			throw new UnauthorizedException('IP address is not allowed to send requests');
		}

		return true;
	}
}