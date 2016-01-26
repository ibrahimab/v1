<?php
/**
 * This class is responsible for handling refunds
 * It can add new ones, remove old ones, mark them as paid,
 * list refunds based on filters.
 *
 * @author Ibrahim Abdullah <ibrahim@chalet.nl>
 * @since  2015-02-04 17:14
 */
class RefundRequest {

	/**
	 * @var DB_sql
	 */
	protected static $db;

	/**
	 * Setting Dependency
	 *
	 * @param DB_sql $db
	 */
	public function __construct(DB_sql $db) {
		self::setDb($db);
	}

	/**
	 * This creates a new refund request, which by default is open
	 *
	 * @param array $data
	 */
	public function create($data) {

		$values = array(

			'boeking_id'  => $data['boeking_id'],
			'name'		  => $data['name'],
			'iban'		  => $data['iban'],
			'description' => $data['description'],
			'amount'	  => $data['amount'],
		);

		self::query('INSERT INTO `boeking_retour` (`boeking_id`, `naam`, `iban`, `omschrijving`, `openstaand`)
					 VALUES (:boeking_id, :name, :iban, :description, :amount)', $values);
	}

	/**
	 * This updates an existing refund request
	 *
	 * @param array $data
	 */
	public function update($data, $id) {

		$values = array(

			'boeking_retour_id' => $id,
			'name'		        => $data['name'],
			'iban'		        => $data['iban'],
			'description'       => $data['description'],
			'amount'	        => $data['amount'],
		);

		$q = self::query('UPDATE `boeking_retour` SET `naam` = :name, `iban` = :iban, `omschrijving` = :description, `openstaand` = :amount
					 WHERE  `boeking_retour_id` = :boeking_retour_id', $values);
	}

	/**
	 * This method selects all the refund requests. Based on the filters you pass,
	 * You can actually creates multiple views with the same method.
	 *
	 * @param array $filters
	 */
	public function all($boeking_id = null, $filters = []) {

		$sql[]= 'SELECT `br`.`boeking_retour_id`, `br`.`boeking_id`, `br`.`naam` AS `name`,
				   	    `b`.`boekingsnummer` AS `reservation_number`, `br`.`iban`,
				 	    `br`.`omschrijving` AS `description`, `br`.`openstaand` AS `amount`,
				 	    DATE_FORMAT(`br`.`aangemaakt_op`, \'%d-%m-%Y %H:%i:%s\') AS `created_at`,
				 	    DATE_FORMAT(`br`.`betaald_op`, \'%d-%m-%Y %H:%i:%s\') AS `paid_at`
			     FROM `boeking_retour` AS `br`
			 	 INNER JOIN `boeking` AS `b`
			  	 ON (`br`.`boeking_id` = `b`.`boeking_id`)
				 WHERE 1';

		if (null !== $boeking_id) {
			$sql[] = 'AND `b`.`boeking_id` = :boeking_id';
		}

		foreach ($filters as $field => $filter) {

			$sql[] = 'AND ' . $field . ' ' . (is_array($filter) ? (isset($filter['$ne']) ? '!=' : ' IN ') : (true === is_null($filter) ? '' : '='))
					. (true === is_null($filter) ? ' IS NULL' : ':' . $field);

            if (isset($filter['$ne'])) {
                $filters[$field] = $filter['$ne'];
            }
		}

		self::query(implode("\n", $sql), array_merge(['boeking_id' => $boeking_id], $filters));
		return self::getDb();
	}

    /**
     * Get all open refund requests
     *
     * @return array
     */
    public function open() {

		$sql = "SELECT `br`.`boeking_id`, `br`.`iban`, `br`.`betaald_op`, `br`.`openstaand`
	            FROM   `boeking_retour` AS `br`
                WHERE  `br`.`ingetrokken_op` IS NULL";

		self::query($sql);
        $ids = [];
        $db  = self::getDB();
        while ($db->next_record()) {

        	if (!$ids[$db->f('boeking_id')]) {
        		$ids[$db->f('boeking_id')]['counter'] = 0;
        		$ids[$db->f('boeking_id')]['betaald_op_counter'] = 0;
        		$ids[$db->f('boeking_id')]['iban_counter'] = 0;
        		$ids[$db->f('boeking_id')]['openstaand'] = 0;
        	}

        	$ids[$db->f('boeking_id')]['counter'] ++;

        	if ($db->f('betaald_op') !== null) {
        		$ids[$db->f('boeking_id')]['betaald_op_counter'] ++;
        	}
        	if ($db->f('iban') !== null && $db->f('iban') !== 'n.n.b.') {
        		$ids[$db->f('boeking_id')]['iban_counter'] ++;
        	}

        	$ids[$db->f('boeking_id')]['openstaand'] += $db->f('openstaand');

        }

        return $ids;
    }

    /**
     * Count all the open refund requests
     *
     * @return integer
     */
    public function countOpen()
    {
		$sql = "SELECT COUNT(`br`.`boeking_id`) AS total
	            FROM   `boeking_retour` AS `br`
                WHERE  `br`.`ingetrokken_op` IS NULL
                AND    `br`.`betaald_op`     IS NULL
                AND    `br`.`iban` != 'n.n.b.'";

		self::query($sql);
        $db  = self::getDB();

        if ($db->next_record()) {
            return $db->f('total');
        }

        return 0;
    }

	/**
	 * Marking request as paid
	 *
	 * @param  int $id
	 * @return DB_sql
	 */
	public function markAsPaid($id) {
		return $this->mark('betaald_op', $id);
	}

    /**
     * Marking request as cancelled
     *
     * @param  int $id
     * @return DB_sql
     */
	public function markAsCancelled($id) {
		return $this->mark('ingetrokken_op', $id);
	}

	/**
	 * Abstract function that handles marking a refund
	 *
	 * @param string $field
	 * @param int    $id
	 */
	public function mark($field, $id) {
		return self::query('UPDATE `boeking_retour` SET `' . $field . '` = CURRENT_TIMESTAMP() WHERE `boeking_retour_id` = :id', ['id' => $id]);
	}

	/**
	 * The actual query function of the DB_sql did not support bindings.
	 * This method is just a wrapper that takes care of escaping queries and
	 * binding parameters to the query.
	 *
	 * @param  string $sql
	 * @param  array  $params
	 * @return DB_sql
	 */
	public static function query($sql, $params = []) {

		if (null === self::getDb()) {
			throw new \Exception('No database connection found');
		}

		self::getDb()->query(self::sanitize($sql, $params));
		return self::getDb();
	}

	/**
	 * A simple sanitizer which checks the common types used in this class
	 * and applies the correct sanitizing before returning the query back
	 *
	 * @param  string $sql
	 * @param  array  $params
	 * @return string
	 */
	public static function sanitize($sql, $params = []) {

		$sanitized = array();
		foreach ($params as $param => $value) {

			switch (true) {

				case (is_float($value) || (is_numeric($value) && ((float) $value != (int) $value))):

					$sanitized[':' . $param] = (float)$value;
					break;

				case (is_numeric($value)):

					$sanitized[':' . $param] = (int)$value;
					break;

				case is_bool($value):

					$sanitized[':' . $param] = (int)$value;
					break;

				case is_array($value):

					$sanitized[':' . $param] = '(' . implode(', ', $value) . ')';
					break;

				case is_null($value) || strtolower($value) === 'null':

					$sanitized[':' . $param] = 'NULL';
					break;

				default:

					$sanitized[':' . $param] = '\'' . mysql_real_escape_string($value) . '\'';
					break;
			}
		}

		return str_replace(array_keys($sanitized), array_values($sanitized), $sql);
	}

	/**
	 * Helper method to set DB_sql dependency
	 *
	 * @param DB_sql $db
	 */
	public static function setDb(DB_sql $db) {
		self::$db = $db;
	}

	/**
	 * Helper method to get DB_sql object
	 *
	 * @return DB_sql
	 */
	public static function getDb() {
		return self::$db;
	}
}
