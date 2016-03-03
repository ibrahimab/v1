<?php
namespace Chalet\Frontend\Images;

use MongoWrapper;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class TypeRepository implements TypeRepositoryInterface
{
    const COLLECTION_TYPES = 'types';

    /**
     * @var MongoWrapper
     */
    private $mongo;

    /**
     * @param MongoWrapper $mongo
     */
    public function __construct(MongoWrapper $mongo)
    {
        $this->mongo = $mongo;
    }

    /**
     * @param integer $id
     *
     * @return array
     */
    public function all($id)
    {
        $collection = $this->mongo->getCollection(self::COLLECTION_TYPES);
        $images     = $collection->find(['file_id' => intval($id)])
                                 ->sort(['rank'    => 1]);

        return iterator_to_array($images);
    }
}