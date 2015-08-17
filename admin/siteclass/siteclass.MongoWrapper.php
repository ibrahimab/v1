<?php
/**
 * MongoDB wrapper around the mongodb extension to work inside the Chalet.nl CMS
 *
 * @author Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet CMS
 */
class MongoWrapper
{
    protected $mongodb;
    protected $db;
    protected $error;

    public function __construct($server, $database, $replicaSet = false)
    {
        try {
            
            $defaultOptions    = ['connect' => true];
            $replicaSetOptions = ['connect' => true, 'replicaSet' => $replicaSet];
            $this->mongodb     = new MongoClient($server, (false === $replicaSet ? $defaultOptions : $replicaSetOptions));
            $this->db          = $this->mongodb->{$database};
            
        } catch (\Exception $e) {
            $this->error = true;
        }
    }

    public function getFiles($collectionName, $fileId)
    {
        if (true === $this->error) {
            return [];
        }
        
        $collection = $this->db->{$collectionName};

        return $collection->find(['file_id' => intval($fileId)])
                          ->sort(['rank'    => 1]);
    }

    public function getFirstFilesByKind($collectionName, $fileIds, $kinds)
    {
        if (true === $this->error) {
            return [];
        }
        
        $collection = $this->db->{$collectionName};

        return $collection->find(['file_id' => ['$in' => $fileIds],
                                  'kind'    => ['$in' => $kinds],
                                  'rank'    => 1]);
    }

    public function getAllFilesByKind($collectionName, $fileIds, $kinds)
    {
        if (true === $this->error) {
            return [];
        }
        
        $collection = $this->db->{$collectionName};

        return $collection->find(['file_id' => ['$in' => $fileIds],
                                  'kind'    => ['$in' => $kinds]])
                          ->sort(['rank' => 1]);
    }

    public function getAllFiles($collectionName, $fileIds)
    {
        if (true === $this->error) {
            return [];
        }
        
        $collection = $this->db->{$collectionName};

        return $collection->find(['file_id' => ['$in' => $fileIds]])
                          ->sort(['rank' => 1]);
    }

    public function getAllMainFiles($collectionName, $fileIds)
    {
        if (true === $this->error) {
            return [];
        }
        
        $collection = $this->db->{$collectionName};

        return $collection->find(['file_id' => ['$in' => $fileIds],
                                  'type'    => 'big'])
                          ->sort(['rank'    => 1]);
    }

    public function getFile($collectionName, $_id)
    {
        if (true === $this->error) {
            return [];
        }
        
        $collection = $this->db->{$collectionName};

        return $collection->findOne(['_id' => new MongoId($_id)]);
    }

    public function maxRank($collectionName, $fileId)
    {
        if (true === $this->error) {
            return 0;
        }
        
        $collection = $this->db->{$collectionName};
        $results    = $collection->aggregate([

            [
                '$match' => [
                    'file_id' => intval($fileId)
                ]
            ],
            [
                '$group' => [
                    '_id'  => 'maxRank',
                    'rank' => [
                        '$max' => '$rank',
                    ]
                ]
            ]
        ]);

        $maxRank = 0;
        if (isset($results['result']) && isset($results['result'][0])) {
            $maxRank = $results['result'][0]['rank'];
        }

        return $maxRank;
    }

    public function updateFileId($collectionName, $_id, $fileId)
    {
        $collection = $this->db->{$collectionName};

        return $collection->update(['_id' => new MongoId($_id)], ['$set' => ['file_id' => $fileId]]);
    }

    public function removeMetadata($collectionName, $_id)
    {
        $collection = $this->db->{$collectionName};

        return $collection->remove(['_id' => new MongoId($_id)]);
    }

    public function getBulkUpdater($collectionName)
    {
        $collection = $this->db->{$collectionName};

        return new MongoUpdateBatch($collection);
    }

    public function getCollection($collectionName)
    {
        return $this->db->{$collectionName};
    }
}
