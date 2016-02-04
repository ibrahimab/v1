<?php
namespace Chalet\Api\Auth;

use Chalet\Api\Auth\UnauthorizedException;
use Chalet\RedisInterface;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class Auth
{
	/**
	 * @var RedisInterface
	 */
	private $redis;

	/**
	 * @param RedisInterface $redis
	 */
	public function __construct(RedisInterface $redis)
	{
		$this->redis = $redis;
	}

	/**
	 * Check if user exists in our database by calculating
	 * signature. If authenticated, generate token to be sent back
	 * for a single request.
	 *
	 * @param string $user
	 * @param string $signature
	 *
	 * @return string
	 * @throws AuthenticationFailedException
	 */
	public function authenticate($token)
	{
		if (false === $this->redis->exists('api:legacy:token')) {
			throw new UnauthorizedException('Invalid token');
		}

		if ($token !== $this->redis->get('api:legacy:token')) {
			throw new UnauthorizedException('Invalid token');
		}

		$newToken = $this->generateToken();

		$this->redis->set('api:legacy:token', $newToken);

		return true;
	}

	/**
	 * @param string $user
	 * @param string $signature
	 *
	 * @return string
	 */
	private function generateToken()
	{
		$secret = getenv('API_LEGACY_SECRET') ?: 'chalet-v2';
		return hash_hmac('sha256', time() . $key, $key);
	}
}