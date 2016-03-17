<?php
namespace Chalet\Test\Unit\Frontend\ExtraDistances;

use Chalet\Test\Unit\TestCase as UnitTestCase;
use DB_Sql;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class TestCase extends UnitTestCase
{
    /**
     * setting up db connection
     *
     * @return void
     */
    public function setup()
    {
        parent::setup();

        $this->db = new DB_Sql();
    }

    /**
     * helper method to get accommodation with distances
     *
     * @param array       $fields
     *
     * @return array|bool
     */
    protected function getAccommodation($fields)
    {
        $this->db->query("SELECT " . implode(', ', $fields) . "
                          FROM accommodatie a
                          WHERE (SELECT COUNT(aed.accommodatie_id)
                                 FROM accommodatie_extra_distance aed
                                 WHERE aed.accommodatie_id = a.accommodatie_id
                          ) > 0
                          ORDER BY RAND() ASC
                          LIMIT 1");

        $this->db->next_record();

        return $this->db->Record;
    }
}