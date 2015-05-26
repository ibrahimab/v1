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

	public function storeFile($collectionName, $tmpFile, $metaData)
	{
		$db		= $this->mongodb->files;
		$gridfs = $db->getGridFS($collectionName);

		return $gridfs->storeFile($tmpFile, [

			'filename' => $metaData['filename'],
			'metadata' => $metaData,
		]);
	}

	public function getFiles($collectionName, $fileId)
	{
		$db     	= $this->mongodb->files;
		$collection = $db->{$collectionName};

		return $collection->find(['file_id' => intval($fileId)])
						  ->sort(['rank'    => 1]);
	}

	public function getFile($collectionName, $fileId, $rank)
	{
		$db 	= $this->mongodb->files;
		$gridfs = $db->getGridFS($collectionName);

		return $gridfs->findOne(['file_id' => intval($fileId), 'metadata.rank' => intval($rank)]);
	}

	public function countFiles($collectionName, $fileId)
	{
		$db 	= $this->mongodb->files;
		$gridfs = $db->getGridFS($collectionName);

		return $gridfs->count(['file_id' => intval($fileId)]);
	}

	public function maxRank($collectionName, $fileId)
	{
		$db 	 = $this->mongodb->files;
		$gridfs  = $db->getGridFS($collectionName);
		$results = $gridfs->aggregate([

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

	public function updateFileId($collectionName, $gridFSId, $fileId)
	{
		$db 	= $this->mongodb->files;
		$gridfs = $db->getGridFS($collectionName);

		return $gridfs->update(['_id' => new MongoId($gridFSId)], ['$set' => ['metadata.file_id' => $fileId]]);
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
}
