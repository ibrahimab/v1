<?php
/**
 * This is a polyfill for the batch update class present in the mongodb driver for php
 * This is in place because NetGround has the ubuntu repo driver version 1.4.5 and batches
 * are supported >= 1.5.0
 *
 * This class creates a batch of updates to be sent to the server in one trip instead of
 * multiple. This class won't be loaded if the ubuntu repo update upgrades the driver to
 * the version it supports.
 *
 * @author Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet CMS
 */
if (!class_exists('MongoUpdateBatch')) {

	/**
	 * MongoUpdateBatch class
	 *
	 * Group your updates together using this class. Pass in a collection instance
	 * and add updates to the batch using self::add and execute them using self::execute
	 *
	 * @author Ibrahim Abdullah <ibrahim@chalet.nl>
	 * @package Chalet CMS
	 */
	class MongoUpdateBatch
	{
		/**
		 * The collection to perform the batch on
		 *
		 * @var MongoCollection
		 */
		private $collection;

		/**
		 * Internal storage for the operations to be performed
		 *
		 * @var array
		 */
		private $operations;

		/**
		 * Flag whether to perform the batch in order (stopping the batch on error)
		 *
		 * @var boolean
		 */
		private $ordered;

		/**
		 * Constructor
		 *
		 * @param MongoCollection $collection
		 * @param boolean $ordered
		 */
		public function __construct(MongoCollection $collection, $ordered = false)
		{
			$this->collection = $collection;
			$this->operations = [];
			$this->ordered    = $ordered;
		}

		/**
		 * Add operation to the batch. The operation must be constructed in
		 * the following way for it to be processed without errors:
		 *
		 * [
		 *  'q' 			   => <document that describes the query>,
		 *  'u' 			   => <update operation>,
		 *  'multi'(optional)  => <boolean whether to update multiple documents>,
		 *  'upsert'(optional) => <boolean whether to insert document if 'q' does not match any documents>
		 * ]
		 *
		 * @param array $operation
		 */
		public function add($operation)
		{
			$this->operations[] = $operation;
		}

		/**
		 * Getter for the mongodb collection
		 *
		 * @return MongoCollection
		 */
		public function collection()
		{
			return $this->collection;
		}

		/**
		 * Getter for the operations
		 *
		 * @return array
		 */
		public function operations()
		{
			return $this->operations;
		}

		/**
		 * Getter for the ordered batch flag
		 *
		 * @return boolean
		 */
		public function ordered()
		{
			return $this->ordered;
		}

		/**
		 * Executing the update batch, returning database response (document)
		 *
		 * @return array
		 */
		public function execute()
		{
			$command = [

				'update'  	   => $this->collection()->getName(),
				'updates' 	   => $this->operations(),
				'ordered' 	   => $this->ordered(),
				'writeConcern' => [
					'w' => 1,
				],
			];

			return $this->collection()->db->command($command);
		}
	}
}