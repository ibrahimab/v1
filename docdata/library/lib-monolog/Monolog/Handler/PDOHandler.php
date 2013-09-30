<?php
namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class PDOHandler extends AbstractProcessingHandler
{
	private $initialized = false;
	private $pdo;
	private $statement;

	public function __construct($pdo, $level = Logger::DEBUG, $bubble = true)
	{
		$this->pdo = $pdo;
		parent::__construct($level, $bubble);
	}

	protected function write(array $record)
	{
		if (!$this->initialized) {
			$this->initialize();
		}

		$this->statement->execute(array(
			'boeking_id' => $record['orderId'],
			'channel' => $record['channel'],
			'level' => $record['level'],
			'message' => $record['formatted'],
			'time' => $record['datetime']->format('U'),
		));
	}

	private function initialize()
	{
		$this->pdo->exec(
			'CREATE TABLE IF NOT EXISTS docdata_log '
			.'(boeking_id INTEGER, channel VARCHAR(255), level INTEGER, message LONGTEXT, time INTEGER UNSIGNED)'
		);
		$this->statement = $this->pdo->prepare(
			'INSERT INTO docdata_log (boeking_id, channel, level, message, time) VALUES (:boeking_id, :channel, :level, :message, :time)'
		);

		$this->initialized = true;
	}
}