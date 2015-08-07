<?php

//
// script to delete old database-importfiles on backup-server (www2.chalet.nl).
// This is necessary to ensure enough free space on this server.
//
// script is included from: /home/chaletnl/backup-www.php
//
// look for files in dir: /home/chaletnl/mysql/
//

// delete files older than 3 days
$date = new DateTime("3 days ago");

$dir = '/home/chaletnl/mysql/';
$backupfiles = scandir($dir);

foreach ($backupfiles as $key => $value) {
	if(preg_match("@06\.sql@", $value) or preg_match("@12\.sql@", $value) or preg_match("@18\.sql@", $value)) {
		if (filemtime($dir.$value)>0 and filemtime($dir.$value)<$date->getTimestamp()) {
			echo "Delete ".$value."\n";
			unlink($dir.$value);
		}
	}
}
