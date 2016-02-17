<?php
namespace Chalet\Test\Unit\ExtraDistances;

use Chalet\Frontend\Accommodation\ExtraDistances\ExtraDistances;
use Chalet\Frontend\Accommodation\ExtraDistances\Repository;
use DB_Sql;

class ExtraDistancesTest extends TestCase
{
    /**
     * Testing whether we get distances
     */
    public function test_if_distances_returns_resultset()
    {
        $repo           = new Repository($this->db, 'nl');
        $extraDistances = new ExtraDistances($repo);
        $accommodation  = $this->getAccommodation(['accommodatie_id']);

        $distances = $extraDistances->all($accommodation['accommodatie_id']);

        $this->assertNotEmpty($distances);
    }

    /**
     * This tests whether the repository is called once for every accommodation
     *
     * @return void
     */
    public function test_if_distances_are_cached()
    {
        $data = [

            'counter'  => 1,
            'name'     => 'fake',
            'distance' => rand(0, 10000),
            'addition' => 'addition',
        ];

        $mock = $this->getMockBuilder(Repository::class)
                     ->setConstructorArgs([$this->db, 'nl'])->getMock();

        $mock->expects($this->exactly(2))
             ->method('all')
             ->willReturn($data);

        $extraDistances = new ExtraDistances($mock);
        $accommodationA = $this->getAccommodation(['accommodatie_id']);
        $accommodationB = $this->getAccommodation(['accommodatie_id']);

        $extraDistances->all($accommodationA['accommodatie_id']);
        $extraDistances->all($accommodationA['accommodatie_id']);
        $extraDistances->all($accommodationB['accommodatie_id']);
        $extraDistances->all($accommodationB['accommodatie_id']);
    }
}