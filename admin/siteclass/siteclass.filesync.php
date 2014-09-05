<?php


/**
* sync files from web01 to web02 (and vice versa)
*/


/*


sync pic-dir:

	sudo rsync -avzh --numeric-ids -e 'ssh' --dry-run --exclude '_imgcache' --rsync-path='sudo rsync' /var/www/chalet.nl/html/pic/cms chalet01@web02.chalet.nl:/var/www/chalet.nl/html/pic/
	sudo rsync -avzh --numeric-ids -e 'ssh' --exclude '_imgcache' --rsync-path='sudo rsync' /var/www/chalet.nl/html/pic/cms chalet01@web02.chalet.nl:/var/www/chalet.nl/html/pic/



Het sync-script werkt als volgt:

    - Zodra een nieuw bestand (afbeelding of pdf) wordt geüpload/aangemaakt in het CMS komt de locatie van dit nieuwe bestand
      in de database-tabel "filesync" terecht, met daarbij de notering wat de bron-server is (web01 of web02)

    - Op zowel web01 als web02 draait een daemon (via het upstart-script /etc/init/filesync.conf) op basis
      van het php-script /var/www/chalet.nl/html/cron/filesync-daemon.php

    - Dat script kijkt elke 10 seconden of er vanaf de eigen bron een nieuwe entry is toegevoegd aan de filesync-table.
      Is dat het geval dan wordt dit bestand via ssh (via een rsync-commando) verzonden naar de andere server.

    - Bij een bestand dat door een gebruiker wordt gewist gebeurt hetzelfde: een nieuwe entry in de filesync-table
      (met als tag "delete"), waarna de daemon via ssh (via "rm ...") dit bestand wist.

    - In de database wordt genoteerd of de sync correct heeft plaatsgevonden.

    - Doet zicht een fout voor dan gaat er een melding naar mij en wordt de exit-code opgeslagen in de database. Via deze code
      kan ik nagaan wat er mis is gegaan. Hier een overzicht van de mogelijke exit-codes van rsync: http://wpkg.org/Rsync_exit_codes

    - Indien de bestemming-server offline is op het moment van syncen dan wordt bij de volgende run (10 seconden later) opnieuw
      geprobeerd het betreffende bestand te syncen, net zo lang totdat de sync geslaagd is. Hierbij wordt exact de volgorde aangehouden
      van het aanmaken/wissen van de bronbestanden.

    - Omdat bij PHP-daemons de kans bestaat dat langzaamaan het geheugen volloopt zal /var/www/chalet.nl/html/cron/filesync-daemon.php
      regelmatig beëindigd worden. Het upstart-script zal er via de respawn-parameter voor zorgen dat het script in dat geval opnieuw wordt gestart.


Naar mijn idee moet bovenstaande oplossing goed werken om de 2 servers in-sync te houden. De enige situatie waarin ik problemen voorzie is wanneer een bestand (bijna) gelijktijdig wordt gewijzigd op web01 en web02. Nou is die kans in het CMS sowieso klein, maar voor nog meer zekerheid is het fijn als de load-balancer zoveel mogelijk zorgt dat verkeer van 1 IP-adres (alle Chalet-medewerkers werken in principe vanaf hetzelfde adres) op 1 server terechtkomt. Naar ik begreep is een behoorlijk lange sessie-tijd ingesteld. Wat ik me nog afvroeg: wordt die sessie-tijd bij elke request opnieuw verlengd? Of gaat die tellen vanaf het eerste bezoek van één IP-adres, waarna deze (na verstrijken van de sessie-tijd) dan ineens kan verspringen naar de andere server? Dus: om 9:00 uur komt de eerste sessie binnen. De load-balancer zegt: stuur deze naar web01. Stel dat de sessie-tijd 1,5 uur is, wat wordt dan de nieuwe eindtijd als er om 9:02 een nieuwe request plaatsvindt? Blijft deze 10:30 uur of wordt deze verlengd naar 10:32 uur? (waardoor deze dus steeds doorschuift)



*/

class filesync {

	function __construct() {

	}


	function add_to_filesync_table($file, $delete=false) {
		// save file to table `filesync`

		$file = preg_replace("@/var/www/chalet.nl/html/@", "", $file);

		$db=new DB_sql;

		if(defined("wt_server_id")) {
			$source = wt_server_id;
		} else {
			$source = 0;
		}

		$db->query("INSERT INTO `filesync` SET `source`='".intval($source)."', `file`='".wt_as($file)."', `delete`='".intval($delete)."', `added`=NOW();");

	}


	function sync_files() {

		// trigger_error("filesync-error-test",E_USER_NOTICE);

		$db=new DB_sql;

		if(defined("wt_server_id")) {
			$source = wt_server_id;
		} else {
			$source = 0;
		}

		unset($inquery_filesync_id);

		// new files
		// $db->query("SELECT `filesync_id`, `file`, `delete` FROM `filesync` WHERE `source`='".intval($source)."' AND `sync_start` IS NULL ORDER BY `added`, `filesync_id`;");
		$db->query("SELECT `filesync_id`, `file`, `delete`, UNIX_TIMESTAMP(`sync_start`) AS `sync_start` FROM `filesync` WHERE `source`='".intval($source)."' AND `sync_finish` IS NULL ORDER BY `added`, `filesync_id`;");
		while($db->next_record()) {

			$inquery_filesync_id .= ",".$db->f("filesync_id");

			$this->handle_file($db->f("file"), $db->f("delete"), $db->f("filesync_id"));

			if(!$db->f("sync_start")) {
				$this->newfile[$db->f("filesync_id")] = true;
			}

		}

		// check if all files have been synced
		if($inquery_filesync_id) {
			$db->query("SELECT `filesync_id`, `file`, `delete` FROM `filesync` WHERE `source`='".intval($source)."' AND `sync_finish` IS NULL AND `filesync_id` IN (".substr($inquery_filesync_id, 1).") ORDER BY `added`, `filesync_id`;");
			while($db->next_record()) {
				if($this->newfile[$db->f("filesync_id")]) {
					trigger_error("filesync-error ".$db->f("file"). " (id ".$db->f("filesync_id").")",E_USER_NOTICE);
				}
			}
		}



		// // previously failed files (after 1 minute)
		// $db->query("SELECT `filesync_id`, `file`, `delete` FROM `filesync` WHERE `source`='".intval($source)."' AND `sync_start` IS NOT NULL AND `sync_start`<(NOW() - INTERVAL 1 MINUTE) AND `sync_finish` IS NULL ORDER BY `added`, `filesync_id`;");
		// while($db->next_record()) {

		// 	// trigger_error("handle_file ".$db->f("file"),E_USER_NOTICE);

		// 	$this->handle_file($db->f("file"), $db->f("delete"), $db->f("filesync_id"));

		// }
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
			if($this->newfile[$filesync_id]) {
				if( $delete ) {
					trigger_error("sync-fout (delete) ".$file." - exit-code: ".$result,E_USER_NOTICE);
				} else {
					trigger_error("sync-fout ".$file." - exit-code: ".$result,E_USER_NOTICE);
				}
			}
			$db->query("UPDATE `filesync` SET `error`='".wt_as($result)."' WHERE `filesync_id`='".intval($filesync_id)."';");
		}
	}



	function transfer_file($file) {

		global $unixdir;

		$return = 999991;

		if(wt_server_id==1) {
			$server="web02.chalet.nl";
		} elseif(wt_server_id==2) {
			$server="web01.chalet.nl";
		}
		if($server) {
			$command = "sudo rsync -avzh --numeric-ids -e 'ssh' --rsync-path='sudo rsync' ".$unixdir.$file." chalet01@".$server.":".$unixdir.$file;
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

			if(file_exists($unixdir.$file)) {
				$return = 0;
			} else {
				$command = "ssh chalet01@".$server." 'sudo rm ".$unixdir.$file."'";
				exec($command, $output, $return_var);

				if(is_array($output)) {
					$output_string = implode($output);
				}
				$return = $return_var;
			}
		}

		return $return;
	}
}


?>