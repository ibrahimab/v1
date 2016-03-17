<?php
namespace Chalet\Frontend\Images;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
interface AccommodationRepositoryInterface
{
    /**
     * @param integer $id
     *
     * @return array
     */
    public function all($id);
}