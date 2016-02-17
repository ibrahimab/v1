<?php
namespace Chalet\Frontend\Accommodation\ExtraDistances;

use Chalet\Frontend\Accommodation\ExtraDistances\RepositoryInterface;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class ExtraDistances
{
    /**
     * @var array
     */
    private $distances;

    /**
     * initializing properties
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->distances  = [];
    }

    /**
     * @param integer $id
     *
     * @return array
     */
    public function all($id)
    {
        if (!isset($this->distances[$id])) {
            $this->distances[$id] = $this->repository->all($id);
        }

        return $this->distances[$id];
    }
}