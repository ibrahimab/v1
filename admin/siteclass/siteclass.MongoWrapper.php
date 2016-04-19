<?php
/**
 * MongoDB wrapper around the mongodb extension to work inside the Chalet.nl CMS
 *
 * @author Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet CMS
 */
class MongoWrapper
{
	/**
	 * @var MongoClient
	 */
	protected $mongodb;

	/**
	 * @var MongoDB
	 */
	protected $db;

	/**
	 * @var bool
	 */
	protected $error;

	/**
	 * @param string $server
	 * @param string $database
	 * @param bool   $replicaSet
	 */
	public function __construct($server, $database, $replicaSet = false)
	{
		try {

			$defaultOptions    = ['connect' => true];
			$replicaSetOptions = ['connect' => true, 'replicaSet' => $replicaSet];
			$this->mongodb     = new MongoClient($server, (false === $replicaSet ? $defaultOptions : $replicaSetOptions));
			$this->db          = $this->mongodb->{$database};
			$this->exception   = null;

		} catch (\Exception $e) {
			$this->exception = $e;
		}
	}

	/**
	 * @return MongoClient
	 */
	public function getClient()
	{
		return $this->mongodb;
	}

	/**
	 * @return MongoDB
	 */
	public function getDatabase()
	{
		return $this->db;
	}

	/**
	 * @return bool
	 */
	public function connected()
	{
		return (false === $this->hasError());
	}

	/**
	 * @param string  $collectionName
	 * @param integer $fileId
	 *
	 * @return MongoCursor
	 */
	public function getFiles($collectionName, $fileId)
	{
		if (true === $this->hasError()) {
			return [];
		}

		$collection = $this->db->{$collectionName};

		return $collection->find(['file_id' => intval($fileId)])
		                  ->sort(['rank'    => 1]);
	}

	/**
	 * @param string $collectionName
	 * @param array  $fileIds
	 * @param array  $kinds
	 *
	 * @return MongoCursor
	 */
	public function getFirstFilesByKind($collectionName, $fileIds, $kinds)
	{
		if (true === $this->hasError()) {
			return [];
		}

		$collection = $this->db->{$collectionName};

		return $collection->find(['file_id' => ['$in' => $fileIds],
		                          'kind'    => ['$in' => $kinds],
		                          'rank'    => 1]);
	}

	/**
	 * @param string $collectionName
	 * @param array  $fileIds
	 * @param array  $kinds
	 *
	 * @return MongoCursor
	 */
	public function getAllFilesByKind($collectionName, $fileIds, $kinds)
	{
		if (true === $this->hasError()) {
			return [];
		}

		$collection = $this->db->{$collectionName};

		return $collection->find(['file_id' => ['$in' => $fileIds],
		                          'kind'    => ['$in' => $kinds]])
		                  ->sort(['rank' => 1]);
	}

	/**
	 * @param string $collectionName
	 * @param array  $fileIds
	 *
	 * @return MongoCursor
	 */
	public function getAllFiles($collectionName, $fileIds)
	{
		if (true === $this->hasError()) {
			return [];
		}

		$collection = $this->db->{$collectionName};

		return $collection->find(['file_id' => ['$in' => $fileIds]])
		                  ->sort(['rank' => 1]);
	}

	/**
	 * @param string $collectionName
	 * @param array  $fileIds
	 *
	 * @return MongoCursor
	 */
	public function getAllMainFiles($collectionName, $fileIds)
	{
		if (true === $this->hasError()) {
			return [];
		}

		$collection = $this->db->{$collectionName};

		return $collection->find(['file_id' => ['$in' => $fileIds],
		                          'type'    => 'big'])
		                  ->sort(['rank'    => 1]);
	}

	/**
	 * @param string $collectionName
	 * @param string $_id
	 *
	 * @return array
	 */
	public function getFile($collectionName, $_id)
	{
		if (true === $this->hasError()) {
			return [];
		}

		$collection = $this->db->{$collectionName};

		return $collection->findOne(['_id' => new MongoId($_id)]);
	}

	/**
	 * @param string  $collectionName
	 * @param integer $fileId
	 *
	 * @return integer
	 */
	public function maxRank($collectionName, $fileId)
	{
		if (true === $this->hasError()) {
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

	/**
	 * @param string  $collectionName
	 * @param string  $_id
	 * @param integer $fileId
	 *
	 * @return bool|array
	 */
	public function updateFileId($collectionName, $_id, $fileId)
	{
		$collection = $this->db->{$collectionName};

		return $collection->update(['_id' => new MongoId($_id)], ['$set' => ['file_id' => $fileId]]);
	}

	/**
	 * @param string $collectionName
	 * @param string $_id
	 *
	 * @return bool|array
	 */
	public function removeMetadata($collectionName, $_id)
	{
		$collection = $this->db->{$collectionName};

		return $collection->remove(['_id' => new MongoId($_id)]);
	}

	/**
	 * @param string $collectionName
	 *
	 * @return MongoUpdateBatch
	 */
	public function getBulkUpdater($collectionName)
	{
		$collection = $this->db->{$collectionName};

		return new MongoUpdateBatch($collection);
	}

	/**
	 * @param string $collectionName
	 *
	 * @return MongoDB
	 */
	public function getCollection($collectionName)
	{
		return $this->db->{$collectionName};
	}

	/**
	 * @param integer $id
	 * @param string  $directory
	 * @param string  $filename
	 * @param integer $rank
	 * @param integer $width
	 * @param integer $height
	 *
	 * @return MongoId
	 */
	public function saveAccommodationImage($id, $directory, $filename, $rank, $width, $height)
	{
		return $this->saveImage('accommodations', $id, $directory, $filename, $rank, $width, $height);
	}

	/**
	 * @param integer $id
	 * @param string  $directory
	 * @param string  $filename
	 * @param integer $rank
	 * @param integer $width
	 * @param integer $height
	 *
	 * @return MongoId
	 */
	public function saveTypeImage($id, $directory, $filename, $rank, $width, $height)
	{
		return $this->saveImage('types', $id, $directory, $filename, $rank, $width, $height);
	}

	/**
	 * @param string  $collection
	 * @param integer $id
	 * @param string  $directory
	 * @param string  $filename
	 * @param integer $rank
	 * @param integer $width
	 * @param integer $height
	 *
	 * @return MongoId
	 */
	public function saveImage($collection, $id, $directory, $filename, $rank, $width, $height)
	{
		$collection = $this->getCollection($collection);
		$data       = [

			'file_id'   => $id,
			'kind'      => $directory,
			'directory' => $directory,
			'filename'  => $filename,
			'label'     => '',
			'type'      => 'normal',
			'under'     => false,
			'always'    => false,
			'rank'      => $rank,
			'width'     => $width,
			'height'    => $height,
		];

		$collection->insert($data);

		return $data['_id'];
	}

	/**
	 * @return bool
	 */
	public function hasError()
	{
		return (null !== $this->exception);
	}

	/**
	 * @return string|null
	 */
	public function getException()
	{
		return (true === $this->hasError() ? $this->exception : null);
	}
}
