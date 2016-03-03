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
        $main = $this->getMainImage($typeId, $accommodationId);

        if (null === $main) {
            $main = $this->default;
        }

        return $main;
    }

    /**
     * @param integer $id
     * @param integer $accommodationId
     *
     * @return array|null
     */
    public function getMainImage($typeId, $accommodationId)
    {
        if (!isset($this->main[$typeId])) {

            $images = $this->typeImages($typeId);
            $main   = null;

            foreach ($images as $image) {

                if (isset($image['type']) && $image['type'] === 'big') {

                    $main = $image;
                    break;
                }
            }

            if (null === $main) {

                $images = $this->accommodationImages($accommodationId);

                foreach ($images as $image) {

                    if (isset($image['type']) && $image['type'] === 'big') {

                        $main = $image;
                        break;
                    }
                }
            }

            $this->main[$typeId] = $main;
        }

        return $this->main[$typeId];
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