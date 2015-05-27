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

	public function __construct($server)
	{
		$this->mongodb = new MongoClient($server);
	}

	public function getFiles($collectionName, $fileId)
	{
		$db     	= $this->mongodb->files;
		$collection = $db->{$collectionName};

		return $collection->find(['file_id' => intval($fileId)])
						  ->sort(['rank'    => 1]);
	}

	public function getFile($collectionName, $_id)
	{
		$db 	= $this->mongodb->files;
		$gridfs = $db->{$collectionName};

		return $gridfs->findOne(['_id' => new MongoId($_id)]);
	}

	public function maxRank($collectionName, $fileId)
	{
		$db 	    = $this->mongodb->files;
		$collection = $db->{$collectionName};
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
		$db 	    = $this->mongodb->files;
		$collection = $db->{$collectionName};

		return $collection->update(['_id' => new MongoId($_id)], ['$set' => ['file_id' => $fileId]]);
	}

	public function removeMetadata($collectionName, $_id)
	{
		$db 	    = $this->mongodb->files;
		$collection = $db->{$collectionName};

		return $collection->remove(['_id' => new MongoId($_id)]);
	}

	public function getBulkUpdater($collectionName)
	{
		$db 		= $this->mongodb->files;
		$collection = $db->{$collectionName};

		return new MongoUpdateBatch($collection);
	}

	public function getCollection($collectionName)
	{
		return $this->mongodb->files->{$collectionName};
	}
}
