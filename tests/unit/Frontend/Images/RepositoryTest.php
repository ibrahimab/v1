<?php
namespace Chalet\Test\Unit\Images\Type;

use Chalet\Test\Unit\Frontend\Images\TestCase;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class RepositoryTest extends TestCase
{
    /**
     * @return void
     */
    public function test_if_accommodation_repository_returns_empty_array_with_nonexisting_type()
    {
        $images = $this->accommodationRepository->all(mt_rand(90, 100), mt_rand(100, 200));

        $this->assertCount(0, $images);
    }

    /**
     * @return void
     */
    public function test_if_accommodation_repository_returns_resultset_array()
    {
        // getting images
        $images = $this->accommodationRepository->all(mt_rand(1, 10), mt_rand(1, 10));

        // has to return one resultset of images
        $this->assertNotEmpty($images);
        $this->assertCount(10, $images);
    }

    /**
     * @return void
     */
    public function test_if_accommodation_image_record_contains_required_fields()
    {
        // getting images
        $images = $this->accommodationRepository->all(mt_rand(1, 10));

        // getting one record
        $image = current($images);

        // checking fields
        $this->assertInternalType('array', $image);
        $this->assertArrayHasKey('file_id', $image);
        $this->assertArrayHasKey('rank', $image);
        $this->assertArrayHasKey('label', $image);
        $this->assertArrayHasKey('type', $image);
        $this->assertArrayHasKey('kind', $image);
        $this->assertArrayHasKey('filename', $image);
        $this->assertArrayHasKey('directory', $image);
        $this->assertArrayHasKey('under', $image);
        $this->assertArrayHasKey('always', $image);
    }

    /**
     * @return void
     */
    public function test_if_type_repository_returns_empty_array_with_nonexisting_type()
    {
        $images = $this->typeRepository->all(mt_rand(90, 100), mt_rand(100, 200));

        $this->assertCount(0, $images);
    }

    /**
     * @return void
     */
    public function test_if_type_repository_returns_resultset_array()
    {
        // getting images
        $images = $this->typeRepository->all(mt_rand(1, 10), mt_rand(1, 10));

        // has to return one resultset of images
        $this->assertNotEmpty($images);
        $this->assertCount(10, $images);
    }

    /**
     * @return void
     */
    public function test_if_type_image_record_contains_required_fields()
    {
        // getting images
        $images = $this->typeRepository->all(mt_rand(1, 10));

        // getting one record
        $image = current($images);

        // checking fields
        $this->assertInternalType('array', $image);
        $this->assertArrayHasKey('file_id', $image);
        $this->assertArrayHasKey('rank', $image);
        $this->assertArrayHasKey('label', $image);
        $this->assertArrayHasKey('type', $image);
        $this->assertArrayHasKey('kind', $image);
        $this->assertArrayHasKey('filename', $image);
        $this->assertArrayHasKey('directory', $image);
        $this->assertArrayHasKey('under', $image);
        $this->assertArrayHasKey('always', $image);
    }
}