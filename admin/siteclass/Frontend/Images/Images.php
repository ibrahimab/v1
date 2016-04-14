<?php
namespace Chalet\Frontend\Images;

use Chalet\Frontend\Images\AccommodationRepositoryInterface;
use Chalet\Frontend\Images\TypeRepositoryInterface;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class Images
{
    /**
     * @var integer
     */
    const KIND_TYPE = 1;

    /**
     * @var integer
     */
    const KIND_ACCOMMODATION = 2;

    /**
     * @var AccommodationRepositoryInterface
     */
    private $accommodationRepository;

    /**
     * @var TypeRepositoryInterface
     */
    private $typeRepository;

    /**
     * @var array
     */
    private $images;

    /**
     * @var array
     */
    private $main;

    /**
     * @var array
     */
    private $mainKinds;

    /**
     * @var array
     */
    private $smallAbove;

    /**
     * @var array
     */
    private $smallAboveKinds;

    /**
     * @var array
     */
    private $smallBelow;

    /**
     * @var array
     */
    private $smallBelowKinds;

    /**
     * @var string
     */
    private $default;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(TypeRepositoryInterface $typeRepository, AccommodationRepositoryInterface $accommodationRepository)
    {
        $this->typeRepository          = $typeRepository;
        $this->accommodationRepository = $accommodationRepository;
        $this->images                  = [];
        $this->main                    = [];
        $this->mainKinds               = [];
        $this->smallAbove              = [];
        $this->smallAboveKinds         = [];
        $this->smallBelow              = [];
        $this->smallBelowKinds         = [];
        $this->default                 = [];
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function setDefaultImage($directory, $filename)
    {
        $this->default = [

            'type'      => 'normal',
            'directory' => $directory,
            'filename'  => $filename,
        ];
    }

    /**
     * @param integer $id
     * @param integer $accommodationId
     *
     * @return array|null
     */
    public function main($typeId, $accommodationId)
    {
        if (!isset($this->main[$typeId])) {
            $this->defineMainImages($typeId, $accommodationId);
        }

        if (!isset($this->main[$typeId]) || null === $this->main[$typeId]) {
            $this->main[$typeId] = $this->default;
        }

        return $this->main[$typeId];
    }

    /**
     * @param integer $id
     * @param integer $accommodationId
     *
     * @return array|null
     */
    public function smallAbove($typeId, $accommodationId)
    {
        if (!isset($this->smallAbove[$typeId])) {
            $this->defineMainImages($typeId, $accommodationId);
        }

        if (!isset($this->smallAbove[$typeId]) || null === $this->smallAbove[$typeId]) {
            $this->smallAbove[$typeId] = $this->default;
        }

        return $this->smallAbove[$typeId];
    }

    /**
     * @param integer $id
     * @param integer $accommodationId
     *
     * @return array|null
     */
    public function smallBelow($typeId, $accommodationId)
    {
        if (!isset($this->smallBelow[$typeId])) {
            $this->defineMainImages($typeId, $accommodationId);
        }

        if (!isset($this->smallBelow[$typeId]) || null === $this->smallBelow[$typeId]) {
            $this->smallBelow[$typeId] = $this->default;
        }

        return $this->smallBelow[$typeId];
    }

    /**
     * @return array|null
     */
    private function defineMainImages($typeId, $accommodationId)
    {
        $images     = $this->typeImages($typeId);
        $main       = null;
        $smallAbove = null;
        $smallBelow = null;

        foreach ($images as $image) {

            if (isset($image['type'])) {

                if ($image['type'] === 'big') {

                    $main = $image;
                    $this->mainKinds[$typeId] = self::KIND_TYPE;
                }

                if ($image['type'] === 'small_above') {

                    $smallAbove = $image;
                    $this->smallAboveKinds[$typeId] = self::KIND_TYPE;
                }

                if ($image['type'] === 'small_below') {

                    $smallBelow = $image;
                    $this->smallBelowKinds[$typeId] = self::KIND_TYPE;
                }
            }
        }

        if (null === $main || null === $smallAbove || null === $smallBelow) {

            $images = $this->accommodationImages($accommodationId);

            foreach ($images as $image) {

                if (isset($image['type'])) {

                    if (null === $main && $image['type'] === 'big') {

                        $main = $image;
                        $this->mainKinds[$typeId] = self::KIND_ACCOMMODATION;
                    }

                    if (null === $smallAbove && $image['type'] === 'small_above') {

                        $smallAbove = $image;
                        $this->smallAboveKinds[$typeId] = self::KIND_ACCOMMODATION;
                    }

                    if (null === $smallBelow && $image['type'] === 'small_below') {

                        $smallBelow = $image;
                        $this->smallBelowKinds[$typeId] = self::KIND_ACCOMMODATION;
                    }
                }
            }
        }

        $this->main[$typeId] = $main;
        $this->smallAbove[$typeId] = $smallAbove;
        $this->smallBelow[$typeId] = $smallBelow;
    }

    /**
     * @param integer $typeId
     *
     * @return integer|boolean
     */
    public function getMainKind($typeId)
    {
        return (isset($this->mainKinds[$typeId]) ? $this->mainKinds[$typeId] : false);
    }

    /**
     * @param integer $typeId
     *
     * @return integer|boolean
     */
    public function getSmallAboveKind($typeId)
    {
        return (isset($this->smallAboveKinds[$typeId]) ? $this->smallAboveKinds[$typeId] : false);
    }

    /**
     * @param integer $typeId
     *
     * @return integer|boolean
     */
    public function getSmallBelowKind($typeId)
    {
        return (isset($this->smallBelowKinds[$typeId]) ? $this->smallBelowKinds[$typeId] : false);
    }

    /**
     * @param integer $typeId
     *
     * @return array
     */
    public function typeImages($typeId)
    {
        if (!isset($this->typeImages[$typeId])) {
            $this->typeImages[$typeId] = $this->typeRepository->all($typeId);
        }

        return $this->typeImages[$typeId];
    }

    /**
     * @param integer $accommodationId
     *
     * @return array
     */
    public function accommodationImages($accommodationId)
    {
        if (!isset($this->accommodationImages[$accommodationId])) {
            $this->accommodationImages[$accommodationId] = $this->accommodationRepository->all($accommodationId);
        }

        return $this->accommodationImages[$accommodationId];
    }

    /**
     * @param integer $id
     * @param integer $accommodationId
     *
     * @return array
     */
    public function all($typeId, $accommodationId)
    {
        $types          = $this->typeImages($typeId);
        $accommodations = $this->accommodationImages($accommodationId);
        $images         = array_values($types);
        $under          = [];

        foreach ($accommodations as $accommodation) {

            if (isset($accommodation['under']) && true === $accommodation['under']) {
                $under[] = $accommodation;
            } else {
                $images[] = $accommodation;
            }
        }

        $images = array_merge($images, $under);

        return $images;
    }
}