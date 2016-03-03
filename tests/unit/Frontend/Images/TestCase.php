<?php
namespace Chalet\Test\Unit\Frontend\Images;

use Chalet\Test\Unit\TestCase as UnitTestCase;
use Chalet\Frontend\Images\TypeRepository;
use Chalet\Frontend\Images\AccommodationRepository;
use MongoWrapper;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class TestCase extends UnitTestCase
{
    /**
     * @var MongoWrapper
     */
    protected $mongo;

    /**
     * @var AccommodationRepository
     */
    protected $accommodationRepository;

    /**
     * @var TypeRepository
     */
    protected $typeRepository;

    /**
     * setting up repositories and test data
     *
     * @return void
     */
    public function setup()
    {
        parent::setup();

        $this->mongo                   = new MongoWrapper('127.0.0.1', 'test_files');
        $this->accommodationRepository = new AccommodationRepository($this->mongo);
        $this->typeRepository          = new TypeRepository($this->mongo);

        $this->prepare();
    }

    /**
     * @return void
     */
    public function prepare()
    {
        $this->prepareAccommodationImages();
        $this->prepareTypeImages();
    }

    /**
     * @return void
     */
    public function prepareAccommodationImages()
    {
        // removing test images
        $name       = 'accommodations';
        $collection = $this->mongo->getCollection($name);
        $collection->drop();

        // creating images
        foreach (range(1, 20) as $id) {

            if ($id <= 10) {

                foreach (range(1, 10) as $rank) {
                    $this->mongo->saveAccommodationImage($id, $name, $id . '-' . $rank . '.png', $rank, 100, 100);
                }

                $collection->update(['file_id' => $id, 'rank' => 1], ['$set' => ['type' => 'big']]);
            }
        }
    }

    /**
     * @return void
     */
    public function prepareTypeImages()
    {
        // removing test images
        $name       = 'types';
        $collection = $this->mongo->getCollection($name);
        $collection->drop();

        // creating images
        foreach (range(1, 20) as $id) {

            // randomize when to save an image to types
            // this is for testing whether the accommodation images come through
            if ($id <= 10) {

                foreach (range(1, 10) as $rank) {
                    $this->mongo->saveTypeImage($id, $name, $id . '-' . $rank . '.png', $rank, 100, 100);
                }

                $collection->update(['file_id' => $id, 'rank' => 1], ['$set' => ['type' => 'big']]);
            }
        }
    }
}