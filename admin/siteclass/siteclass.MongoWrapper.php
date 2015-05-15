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
		$collection = $db->{$collectionName . '.files'};

		return $collection->find(['metadata.file_id' => intval($fileId)]);
	}

	public function getFile($collectionName, $fileId, $rank)
	{
		$db 	= $this->mongodb->files;
		$gridfs = $db->getGridFS($collectionName);

		return $gridfs->findOne(['metadata.file_id' => intval($fileId), 'metadata.rank' => intval($rank)]);
	}

	public function countFiles($collectionName, $fileId)
	{
		$db 	= $this->mongodb->files;
		$gridfs = $db->getGridFS($collectionName);

		return $gridfs->count(['metadata.file_id' => intval($fileId)]);
	}

	public function maxRank($collectionName, $fileId)
	{
		$db 	 = $this->mongodb->files;
		$gridfs  = $db->getGridFS($collectionName);
		$results = $gridfs->aggregate([

			[
				'$match' => [
					'metadata.file_id' => intval($fileId)
				]
			],
			[
				'$group' => [
					'_id'  => 'maxRank',
					'rank' => [
						'$max' => '$metadata.rank',
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

	public function removeFile($collectionName, $gridFSId)
	{
		$db 	= $this->mongodb->files;
		$gridfs = $db->getGridFS($collectionName);

		return $gridfs->remove(['_id' => new MongoId($gridFSId)]);
	}
}