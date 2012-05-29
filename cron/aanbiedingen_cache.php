<?php

# /usr/local/bin/php --php-ini /home/sites/chalet.nl/php_cli.ini /home/sites/chalet.nl/html/cron/aanbiedingen_cache.php

#
# Script wordt elk uur om 15 minuten over het hele uur gerund
#

# Testversie?
if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$testsysteem=true;
}

set_time_limit(0);
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
	$unzip="/usr/bin/unzip";
	$tmpdir="/tmp/";
} else {
	$unixdir="/home/sites/chalet.nl/html/";
	$unzip="/usr/local/bin/unzip";
	$tmpdir="/home/sites/chalet.nl/html/tmp/";
}
$cron=true;
$geen_tracker_cookie=true;
include($unixdir."admin/vars.php");

echo date("r")."<pre>Chalet.nl Aanbiedingen Prefetch\n\n\n";
flush();

echo "<pre>";

while(list($key,$value)=each($vars["websiteinfo"]["basehref"])) {
	unset($url,$opsomming,$filename);
	if($key=="Z" or $key=="N") {
		# zomerhuisje.nl en zomerhuisje.eu - aanbiedingen per land ophalen

		# Alle landen doorlopen
		$db->query("SELECT land_id, naam FROM land WHERE zomertonen=1 ORDER BY naam;");
		while($db->next_record()) {
			$url=$value.$txta[$vars["websiteinfo"]["taal"][$key]]["menu_aanbiedingen"]."/".wt_convert2url($db->f("naam"))."/?nocache=1";
			echo "\n-----------------------------------\n".$url."\n\n";
			$opsomming=@file_get_contents($url);
			if(preg_match("/<!-- WTbegin -->(.*)<!-- WTend -->/s",$opsomming,$regs)) {
				$filename=$unixdir."cache/aanbiedingen_land_".$db->f("land_id")."_".$key.".html";
				file_put_contents($filename,$regs[1]);
				echo "Opgeslagen: ".$filename."\n";
				flush();
				sleep(1);
			}
		}
	} elseif($key=="I") {
		# italissima: alleen aanbiedingen Italië ophalen
		$url=$value.$txta[$vars["websiteinfo"]["taal"][$key]]["menu_aanbiedingen"]."/?nocache=1";
		$opsomming=@file_get_contents($url);
		if(preg_match("/<!-- WTbegin -->(.*)<!-- WTend -->/s",$opsomming,$regs)) {
			$filename=$unixdir."cache/aanbiedingen_land_5_".$key.".html";
			file_put_contents($filename,$regs[1]);
			echo "Opgeslagen: ".$filename."\n";
			flush();
			sleep(1);
		}
	} elseif($key<>"S" and $key<>"O") {
		# alle andere sites - per datum
		$url=$value.$txta[$vars["websiteinfo"]["taal"][$key]]["menu_aanbiedingen"].".php";
		echo "\n-----------------------------------\n".$url."\n\n";
		$opsomming=@file_get_contents($value."aanbiedingen.php?nocache=1");
		if(preg_match("/<!-- WTbegin -->(.*)<!-- WTend -->/s",$opsomming,$regs)) {
			$filename=$unixdir."cache/aanbiedingen_".$key.".html";
			file_put_contents($filename,$regs[1]);
			echo "Opgeslagen: ".$filename."\n";

			# Alle datums doorlopen
			if(preg_match_all("/\.php\?d=([0-9]+)/",$regs[1],$regs2)) {
				while(list($key2,$value2)=each($regs2[1])) {
					$aanbiedingen=@file_get_contents($url."?nocache=1&d=".$value2);
					if(preg_match("/<!-- WTbegin -->(.*)<!-- WTend -->/s",$aanbiedingen,$regs3)) {
						$filename=$unixdir."cache/aanbiedingen_".$key."_".$value2.".html";
						file_put_contents($filename,$regs3[1]);
						echo "Opgeslagen: ".$filename."\n";
						flush();
						sleep(1);
					}
				}
			}
		}
	}
}

?>