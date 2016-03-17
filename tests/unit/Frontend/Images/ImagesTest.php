<?php
namespace Chalet\Test\Unit\Frontend\Images;

use Chalet\Test\Unit\Frontend\Images\TestCase;
use Chalet\Frontend\Images\Images;
use Chalet\Frontend\Images\AccommodationRepository;
use Chalet\Frontend\Images\TypeRepository;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class ImagesTest extends TestCase
{
    /**
     * @var Images
     */
    private $images;

    /**
     * @return void
     */
    public function setup()
    {
        parent::setup();

        $this->images = new Images($this->typeRepository, $this->accommodationRepository);
        $this->images->setDefaultImage('accommodations', 'default.png');
    }

    /**
     * @return void
     */
    public function test_if_fetching_all_images_returns_array()
    {
        $records = $this->images->all(mt_rand(1, 10), mt_rand(1, 10));

        // has to return 20 records (type images + accommodation images)
        $this->assertNotEmpty($records);
        $this->assertCount(20, $records);
    }

    /**
     * @return void
     */
    public function test_if_fetching_from_type_without_images_returns_images_from_accommodation()
    {
        $records = $this->images->all(mt_rand(11, 20), mt_rand(1, 10));

        // has to return 10 records (accommodation images)
        $this->assertNotEmpty($records);
        $this->assertCount(10, $records);
    }

    /**
     * @return void
     */
    public function test_fetching_main_image()
    {
        // getting images
        $image  = $this->images->main(mt_rand(1, 10), mt_rand(1, 10));

        $this->assertNotEmpty($image);
        $this->assertArrayHasKey('type', $image);
        $this->assertEquals('big', $image['type']);
    }

    /**
     * @return void
     */
    public function test_if_fetching_main_from_type_without_images_returns_main_from_accommodation()
    {
        $image = $this->images->main(mt_rand(11, 20), mt_rand(1, 10));

        $this->assertNotEmpty($image);
        $this->assertArrayHasKey('type', $image);
        $this->assertEquals('big', $image['type']);
    }

    /**
     * @return void
     */
    public function test_if_type_and_accommodation_without_images_returns_empty_array()
    {
        $this->assertEmpty($this->images->all(mt_rand(11, 20), mt_rand(11, 20)));
    }
}