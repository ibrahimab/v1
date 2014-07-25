<?php


/**
* sync files
*/
class filesync {

	function __construct() {
		$this->source = 1;
	}


	function add_to_filesync_table($file, $delete=false) {
		// save file to table `filesync`

		$source = 1;

		$db=new DB_sql;

		$db->query("INSERT INTO `filesync` SET `source`='".intval($source)."', `file`='".wt_as($file)."', `delete`='".intval($delete)."', `added`=NOW();");

	}


	function sync_files() {

		$db=new DB_sql;

		// new files
		$db->query("SELECT `filesync_id`, `file`, `delete` FROM `filesync` WHERE `source`='".intval($source)."' AND `sync_start` IS NULL ORDER BY `added`, `filesync_id`;");
		while($db->next_record()) {

			$this->ftp($db->f("file"), $db->f("delete"), $db->f("filesync_id"));

		}

		// previously failed files (after 1 minute)
		$db->query("SELECT `filesync_id`, `file`, `delete` FROM `filesync` WHERE `source`='".intval($source)."' AND `sync_start` IS NOT NULL AND `sync_start`<(NOW() - INTERVAL 2 MINUTE) AND `sync_start` IS NULL ORDER BY `added`, `filesync_id`;");
		while($db->next_record()) {

			$this->ftp($db->f("file"), $db->f("delete"), $db->f("filesync_id"));

		}

		if($this->conn_id) {
			ftp_close($this->conn_id);
		}

	}


	function ftp($file, $delete, $filesync_id) {

		global $vars, $unixdir;

		$sync_succeed = false;

		$db=new DB_sql;

		if(!$this->conn_id) {
			$this->ftp_connect();
		}

		if($this->conn_id) {

			$db->query("UPDATE `filesync` SET `sync_start`=NOW() WHERE `filesync_id`='".intval($filesync_id)."';");

			if( $delete ) {
				if (ftp_delete($this->conn_id, $unixdir.$file)) {
					$sync_succeed = true;
				}
			} else {
				if (ftp_put($this->conn_id, $unixdir.$file, $unixdir.$file,  FTP_BINARY)) {
					$sync_succeed = true;
				}
			}

			if($sync_succeed) {
				$db->query("UPDATE `filesync` SET `sync_finish`=NOW() WHERE `filesync_id`='".intval($filesync_id)."';");
			}
		}
	}

	function ftp_connect() {

		if($this->source==1) {
			$ftp_server="web02.chalet.nl";
		} elseif($this->source==2) {
			$ftp_server="web01.chalet.nl";
		}
		if($ftp_server) {
			$ftp_user_name="chalet01";
			$ftp_user_pass="lKwkejJ9e";

			// set up basic connection
			$this->conn_id = ftp_connect($ftp_server);

			// login with username and password
			$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
		}
	}
}


?>