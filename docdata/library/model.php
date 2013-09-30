<?php

/**
 * Default Model class; it is extended by all application models
 * Instantiates the connection to the database
 *
 */
class Model extends DB_Sql {

	const STATE_NEW        = 'new';
	const STATE_PROCESSING = 'processing';
	const STATE_COMPLETE   = 'complete';
	const STATE_CLOSED     = 'closed';
	const STATE_CANCELED   = 'canceled';
	const STATE_HOLDED     = 'holded';

	protected $db;

	function __construct() {
		// DB_NAME, DB_HOST, DB_USER, DB_PASSWORD are defined in /docdata/config/config.php
		$this->db = $this->connect(DB_NAME, DB_HOST, DB_USER, DB_PASSWORD);
	}

}