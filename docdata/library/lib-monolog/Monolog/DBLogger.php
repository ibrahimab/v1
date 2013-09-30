<?php

namespace Monolog;

use Monolog\Logger;

class DBLogger extends Logger {

	/**
	 * Adds a log record.
	 *
	 * @param  integer $level   The logging level
	 * @param  string  $message The log message
	 * @param  array   $context The log context
	 * @param integer $orderId The booking id
	 *
	 * @return Boolean Whether the record has been processed
	 */
	public function addRecord($level, $message, array $context = array(), $orderId = NULL)
	{
		if (!$this->handlers) {
			$this->pushHandler(new StreamHandler('php://stderr', static::DEBUG));
		}

		if (!static::$timezone) {
			static::$timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
		}

		$record = array(
			'orderId'	=> (int) $orderId,
			'message' => (string) $message,
			'context' => $context,
			'level' => $level,
			'level_name' => static::getLevelName($level),
			'channel' => $this->name,
			'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), static::$timezone)->setTimezone(static::$timezone),
			'extra' => array(),
		);
		// check if any handler will handle this message
		$handlerKey = null;
		foreach ($this->handlers as $key => $handler) {
			if ($handler->isHandling($record)) {
				$handlerKey = $key;
				break;
			}
		}
		// none found
		if (null === $handlerKey) {
			return false;
		}

		// found at least one, process message and dispatch it
		foreach ($this->processors as $processor) {
			$record = call_user_func($processor, $record);
		}
		while (isset($this->handlers[$handlerKey]) &&
			false === $this->handlers[$handlerKey]->handle($record)) {
			$handlerKey++;
		}

		return true;
	}


}