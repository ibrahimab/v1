<?php
namespace Chalet\Test\Unit\ExtraDistances;

use Chalet\Frontend\Accommodation\ExtraDistances\Repository;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class RepositoryTest extends TestCase
{
    /**
     * @return void
     */
    public function test_if_no_distances_defined_returns_empty_array()
    {
        $repo = new Repository($this->db, 'nl');

        // has to return empty array on non existing accommodation
        $this->assertEmpty($repo->all(1999999999));
    }

    /**
     * @return void
     */
    public function test_if_accommodation_with_distances_returns_resultset_array()
    {
        $repo = new Repository($this->db, 'nl');

        // getting accommodation
        $accommodation = $this->getAccommodation(['accommodatie_id']);

        // has to return array of distance records for existing accommodation
        $this->assertNotEmpty($repo->all($accommodation['accommodatie_id']));
    }

    /**
     * @return void
     */
    public function test_if_one_distance_record_contains_the_required_fields()
    {
        $repo = new Repository($this->db, 'nl');

        // getting accommodation
        $accommodation = $this->getAccommodation(['accommodatie_id']);

        // getting a distance record
        $resultset = $repo->all($accommodation['accommodatie_id']);
        $record    = current($resultset);

        // checking fields
        $this->assertArrayHasKey('counter', $record);
        $this->assertArrayHasKey('name', $record);
        $this->assertArrayHasKey('distance', $record);
        $this->assertArrayHasKey('addition', $record);
    }
}