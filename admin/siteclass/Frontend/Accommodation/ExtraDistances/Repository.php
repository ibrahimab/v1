<?php
namespace Chalet\Frontend\Accommodation\ExtraDistances;

use DB_Sql;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class Repository implements RepositoryInterface
{
	/**
	 * @var DB_Sql
	 */
	private $db;

	/**
	 * @var string
	 */
	private $languageField;

	/**
	 * @param DB_Sql $db
	 */
	public function __construct(DB_Sql $db, $language)
	{
		$this->db            = $db;
		$this->languageField = ($language === 'nl' ? '' : ('_' . $language));
	}

	/**
	 * @param integer $id
	 *
	 * @return array ['counter', 'name', 'distance', 'addition']
	 */
	public function all($id)
	{
		$this->db->query("SELECT counter, name" . $this->languageField . " AS name, distance, addition" . $this->languageField . " AS addition
		                  FROM   accommodatie_extra_distance
		                  WHERE  accommodatie_id = " . intval($id) . "
		                  ORDER BY counter ASC");

		$distances = [];

		while ($this->db->next_record()) {

			$distances[] = [

				'counter'  => intval($this->db->f('counter')),
				'name'     => $this->db->f('name'),
				'distance' => $this->db->f('distance'),
				'addition' => $this->db->f('addition'),
			];
		}

		return $distances;
	}
}