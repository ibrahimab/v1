<?php
/**
 * The purpose of this model is to lock and unlock based on a database table.
 * This can be useful when you do not want two processes to edit the same data at one moment in time.
 * A shutdown function is registered to make sure the lock is released when the PHP process crashes.
 *
 */
class Model_Locking extends Model {
	/* @var string requested locking code */
	protected $_lockCode = null;
	/* @var string identifier for current process*/
	protected $_processCode = null;
	/* @var Mysql */
	protected $_dbAdapter = null;
	/* @var string */
	protected $_lockTable = null;
	/* @var string */
	protected $_lockTableQ = null;
	/* @var string */
	protected $_lockInfo = '';
	/* @var Helper_Data */
	protected $_helper;

	/* @var integer */
	protected $_lockTimeoutSec = 50;     // Seconds. Can be set in config.xml
	/* @var float */
	protected $_tryWaitSec = 0.5;        // Seconds, can be < 1. Can be set in config.xml
	/* @var integer after the stealLock time a lock entry can be taken by another (used to fix locks that are not freed) */
	protected $_stealLockSec = 5000;     // Seconds. Can be set in config.xml
	/* @var integer */
	protected $_cleanupTimeSec = 500000; // Seconds. Can be set in config.xml
	/* @var bool */
	protected $_locked = false;
	/* @var bool */
	protected $_initOK = false;
	/* @var int */
	private $_order_id;

	/**
	 * Constructor, prepares everything for locking, but does not do the lock itself.
	 *
	 * @param $lock_code A string to identify what you want to lock, for example 'order_1223123'
	 * @param $order_number Order number used for the logging part
	 *
	 * @return Smile_Docdata_Model_Rowlock|false
	 */
	public function __construct($lock_code, $order_number = null) {

		parent::__construct();

		$this->_order_id = $order_number;
		$this->_helper = $_helper = App::get('helper/data');
		if (empty($lock_code)) {
			$_helper->dbLog(get_class() . ': Empty lock code given', App::WARN, $order_number);
			return false;
		}

		$this->_lockCode = $lock_code;
		$this->_processCode = uniqid(get_class(), true);

		// prepare database access and info to use during locking
		#$this->_dbAdapter  = $oCoreResource->getConnection('core_write');
		$this->_lockTable  = 'docdata_lock';
		$this->_lockTableQ = $this->_lockTable;
		$this->_lockInfo   = 'lock_code="'.$this->_lockCode.'" process_code="'.$this->_processCode.'"';

		//check if the table to update is present
		$this->query("SHOW TABLE STATUS LIKE '". $this->_lockTable . "';");
		if (!$this->num_rows()) {
			$this->_helper->dbLog(get_class() . ': Lock table "'.$this->_lockTable.'" does not exist. Locking is not possible. ' . $this->_lockInfo, App::ERR, $order_number);
			return false;
		}

		//retrieve config values if present, otherwise use defaults
		$config_helper = App::get('helper/config');

		$config_val = intval($config_helper->getItem('locking/lock_timeout_sec'));
		$this->_lockTimeoutSec = ( !empty($config_val) ) ? $config_val : $this->_lockTimeoutSec;

		$config_val = floatval($config_helper->getItem('locking/try_wait_sec'));
		$this->_tryWaitSec = ( !empty($config_val) ) ? $config_val : $this->_tryWaitSec;

		$config_val = intval($config_helper->getItem('locking/steal_loc_sec'));
		$this->_stealLockSec = ( !empty($config_val) ) ? $config_val : $this->_stealLockSec;

		$config_val = intval($config_helper->getItem('locking/cleanup_time_sec'));
		$this->_cleanupTimeSec = ( !empty($config_val) ) ? $config_val : $this->_cleanupTimeSec;

		//register shutdown so it is called during shutdown
		register_shutdown_function( array($this,'shutdown') );

		//register that initializing went well
		$this->_initOK = true;

		return $this;
	}

	/**
	 * Do the actual lock.
	 * This function is blocking until either the lock is acquired or 10 seconds have passed and no lock is acquired.
	 *
	 * @return bool True if successful
	 */
	public function lock() {
		if (!$this->initCheck()) {
			return false;
		}

		//determine how many tries there are left
		$tries = ceil($this->_lockTimeoutSec / $this->_tryWaitSec);

		// If lock does not yet exist we can insert it.
		$result = $this->_insertLock();

		// If insert was not successful we will try to update a number of times
		while ($tries and !$result) {
			$tries--;
			//attempt to take a lock that is freed or that has been inactive for a configured period
			$result = $this->_updateLock();
			if (!$result) {
				usleep(ceil($this->_tryWaitSec * 1000 * 1000));
			}
		}

		if ($result) {
			$this->_locked = true;
			$this->_helper->dbLog(get_class() . ': Lock acquired. ' . $this->_lockInfo, App::DEBUG, $this->_order_id);
		} else {
			$this->_helper->dbLog(get_class() . ': Lock acquire failed. ' . $this->_lockInfo, App::WARN, $this->_order_id);
		}

		return $result;
	}

	/**
	 * Try to get the lock by insert. Succeeds if the lock record does not exist yet.
	 *
	 * @return bool True if lock acquired.
	 */
	private function _insertLock() {
		if (!$this->initCheck()) {
			return false;
		}

		$nowTime = time();
		$this->_helper->dbLog(get_class() . ': Trying to get lock by insert. ' . $this->_lockInfo, App::DEBUG, $this->_order_id);
		//try to insert new row into table
		$insertSQL =  'INSERT IGNORE INTO ' . $this->_lockTableQ . ' ';
		$insertSQL .= "SET `lock_code`= '" . $this->_lockCode ."', `process_code`= '" . $this->_processCode . "', `lock_time`= '" . $nowTime ."' ";
		$this->query($insertSQL);
		$result = ( 1 == $this->affected_rows() );
		return $result;
	}

	/**
	 * Try to get the lock by update. Succeeds if the lock record existed but was free or older than StealLockSec.
	 *
	 * @return bool True if lock acquired.
	 */
	private function _updateLock() {
		if (!$this->initCheck()){
			return false;
		}

		$nowTime = time();
		//determine steal lock time, if entry is old enough the row can be taken over by current process
		$iStealLockTime = $nowTime - $this->_stealLockSec;
		$this->_helper->dbLog(get_class() . ': Trying to get lock by update. ' . $this->_lockInfo, App::DEBUG, $this->_order_id);
		$lockSQL = "UPDATE " . $this->_lockTableQ . " SET `process_code`= '" . $this->_processCode . "', lock_time= '". $nowTime ."' ";
		$lockSQL .= "WHERE `lock_code`= '" . $this->_lockCode ."' ";
		$lockSQL .= "AND ( `process_code` IS NULL OR `lock_time` < '". $iStealLockTime ."' ) ";
		$lockSQL .= 'LIMIT 1';
		$this->query($lockSQL);
		$result = ( 1 == $this->affected_rows() );
		return $result;
	}

	/**
	 * Unlock the lock
	 *
	 * @return bool True if successful.
	 */
	public function unlock() {
		if (!$this->initCheck()) {
			return false;
		}

		$this->_helper->dbLog(get_class() . ': Trying to free lock. ' . $this->_lockInfo, App::DEBUG, $this->_order_id);
		//release lock by clearing the process code field
		$unlockSQL =  'UPDATE ' . $this->_lockTableQ . ' SET `process_code`=NULL ';
		$unlockSQL .= "WHERE `lock_code` = '". $this->_lockCode ."' ";
		$unlockSQL .= "AND `process_code` = '". $this->_processCode ."' ";
		$unlockSQL .= 'LIMIT 1';
		$this->query($unlockSQL);
		$result = ( 1 == $this->affected_rows() );
		if ($result) {
			$this->_locked = false;
			$this->_helper->dbLog(get_class() . ': Lock freed. ' . $this->_lockInfo, App::DEBUG, $this->_order_id);
		} else {
			$this->_helper->dbLog(get_class() . ': Freeing lock failed. ' . $this->_lockInfo, App::WARN, $this->_order_id);
		}

		// Randomly on on 100 hits we clean up the lock table
		// When there are more orders the number of cleanups scales automatically.
		// Prevents flooding the lock table without depending on cronjobs and without a constant cleanup load
		if ( 77 == rand(1,100) ) {
			$this->_maintenance();
		}

		return $result;
	}

	/**
	 * Get the Lock code set when creating the instance of this class.
	 *
	 * @return string|null
	 */
	public function getLockCode() {
		if (!$this->initCheck()) {
			return false;
		}

		return $this->_lockCode;
	}

	/**
	 * Cleanup old lock records.
	 * We only clean up unlocked records, because if there are "hanging" locks
	 * we want to be able to see it afterwards.
	 *
	 * @return bool|int Number of removed rows
	 */
	private function _maintenance() {
		if (!$this->initCheck()) {
			return false;
		}

		$cleanupTime = time() - $this->_cleanupTimeSec;
		$this->_helper->dbLog(get_class() . ': Performing maintenance, removing locks older than '.$cleanupTime, App::DEBUG, $this->_order_id);
		//clean entries that are freed (have no processcode) or are inactive for configured time
		$cleanupSQL =  'DELETE FROM ' . $this->_lockTableQ . ' ';
		$cleanupSQL .= "WHERE `lock_time` < '" . $cleanupTime ."' ";
		$cleanupSQL .= 'AND process_code IS NULL ';
		$this->query( $cleanupSQL );
		$result = $this->affected_rows();
		$this->_helper->dbLog(get_class() . ': Maintenance performed, ' . $result . ' rows removed.', App::DEBUG, $this->_order_id);
		return $result;
	}

	public function shutdown() {
		if (!$this->initCheck()) {
			return false;
		}

		if ($this->_locked) {
			$this->_helper->dbLog(get_class() . ': Still locked on shutdown, so unlocking. ' . $this->_lockInfo, App::WARN, $this->_order_id);
			$this->unlock();
		}
	}

	/**
	 * Function to check if the Lock object was able to initialize successful
	 * during the construction.
	 *
	 * @return bool
	 */
	public function initCheck() {
		if (!$this->_initOK) {
			$this->_helper->dbLog(get_class() . ': Object is not initialed correctly, no locking possible. ' . $this->_lockInfo, App::ERR, $this->_order_id);
		}
		return $this->_initOK;
	}

}