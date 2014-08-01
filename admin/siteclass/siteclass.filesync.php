<?php


/**
* sync files from web01 to web02 (and vice versa)
*/

class filesync {

	function __construct() {

	}


	function add_to_filesync_table($file, $delete=false) {
		// save file to table `filesync`

		$db=new DB_sql;

		if(defined("wt_server_id")) {
			$source = wt_server_id;
		} else {
			$source = 0;
		}

		$db->query("INSERT INTO `filesync` SET `source`='".intval($source)."', `file`='".wt_as($file)."', `delete`='".intval($delete)."', `added`=NOW();");

	}


	function sync_files() {

		$db=new DB_sql;

		if(defined("wt_server_id")) {
			$source = wt_server_id;
		} else {
			$source = 0;
		}

		// new files
		$db->query("SELECT `filesync_id`, `file`, `delete` FROM `filesync` WHERE `source`='".intval($source)."' AND `sync_start` IS NULL ORDER BY `added`, `filesync_id`;");
		while($db->next_record()) {

			$this->handle_file($db->f("file"), $db->f("delete"), $db->f("filesync_id"));

		}

		// previously failed files (after 1 minute)
		$db->query("SELECT `filesync_id`, `file`, `delete` FROM `filesync` WHERE `source`='".intval($source)."' AND `sync_start` IS NOT NULL AND `sync_start`<(NOW() - INTERVAL 1 MINUTE) AND `sync_finish` IS NULL ORDER BY `added`, `filesync_id`;");
		while($db->next_record()) {

			// trigger_error("handle_file ".$db->f("file"),E_USER_NOTICE);

			$this->handle_file($db->f("file"), $db->f("delete"), $db->f("filesync_id"));

		}
	}


	function handle_file($file, $delete, $filesync_id) {

		global $vars, $unixdir;

		$sync_succeed = false;

		$db=new DB_sql;


		$db->query("UPDATE `filesync` SET `sync_start`=NOW() WHERE `filesync_id`='".intval($filesync_id)."';");

		if( $delete ) {
			$result = $this->delete_file($file);
			if($result===0) {
				$sync_succeed = true;
			}
		} else {
			$result = $this->transfer_file($file);
			if($result===0) {
				$sync_succeed = true;
			}
		}

		if($sync_succeed) {
			$db->query("UPDATE `filesync` SET `sync_finish`=NOW() WHERE `filesync_id`='".intval($filesync_id)."';");
		} else {
			$db->query("UPDATE `filesync` SET `error`='".wt_as($result)."' WHERE `filesync_id`='".intval($filesync_id)."';");
		}
	}

	function transfer_file($file) {


// http://wpkg.org/Rsync_exit_codes


		global $unixdir;

		$return = 999991;

		if(wt_server_id==1) {
			$server="web02.chalet.nl";
		} elseif(wt_server_id==2) {
			$server="web01.chalet.nl";
		}
		if($server) {
			$command = "rsync -avzh --numeric-ids -e 'ssh' --rsync-path='sudo rsync' ".$unixdir.$file." chalet01@".$server.":".$unixdir.$file;
			exec($command, $output, $return_var);
			// exec("rsync -avzh --numeric-ids -e ssh ".$unixdir.$file." chalet01@".$server.":".$unixdir.$file, $output, $return_var);
//			rsync -avzh --numeric-ids -e ssh /var/www/chalet.nl/html/pic/cms/hoofdfoto_accommodatie/319.jpg chalet01@web02.chalet.nl:/var/www/chalet.nl/html/pic/cms/hoofdfoto_accommodatie/319.jpg

			if(is_array($output)) {
				$output_string = implode($output);
			}

			// trigger_error($command." - return melding ".$unixdir.$file.": ".$return_var,E_USER_NOTICE);
			$return = $return_var;
		}

		return $return;
	}

	function delete_file($file) {

		global $unixdir;

		$return = 999992;

		if(defined("wt_server_id")) {
			if(wt_server_id==1) {
				$server="web02.chalet.nl";
			} elseif(wt_server_id==2) {
				$server="web01.chalet.nl";
			}
		}
		if($server) {

			if(!file_exists($unixdir.$file)) {

				$command = "ssh chalet01@".$server." 'sudo rm ".$unixdir.$file."'";
				exec($command, $output, $return_var);

				if(is_array($output)) {
					$output_string = implode($output);
				}
				// trigger_error($command." - return melding ".$unixdir.$file.": ".$return_var,E_USER_NOTICE);
				$return = $return_var;
			}

		}

		return $return;
	}
}


?>