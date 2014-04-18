<?php

#
#
# Dit script wordt op elke minuut gerund op de server srv01.chalet.nl met het account chalet01.
#
# /usr/bin/php --php-ini /var/www/chalet.nl/php_cli.ini /var/www/chalet.nl/html/cron/elkeminuut.php test
#


set_time_limit(0);
if($argv[1]=="test" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$temptest=true;
} else {

}
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
	echo "<pre>";
} elseif($_SERVER["SCRIPT_NAME"]=="/home/webtastic/html/chalet/cron/elkeminuut.php") {
	$unixdir="/home/webtastic/html/chalet/";
} else {
	$unixdir="/var/www/chalet.nl/html/";
#	mail("chaletmailbackup+systemlog@gmail.com","Chalet-cron elkuur","Cron is gestart om ".date("r"));
}
$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");

#
# Controle op onjuiste wederverkoop-tarieven (elke 15 minuten)
#
if($temptest or (date("i")==15 or date("i")==30 or date("i")==45 or date("i")==00)) {
	$db->query("SELECT DISTINCT ta.type_id, ta.seizoen_id FROM tarief ta, type t, accommodatie a WHERE t.tonen=1 AND a.tonen=1 AND t.accommodatie_id=a.accommodatie_id AND ta.type_id=t.type_id AND ta.c_bruto>0 AND ta.c_verkoop_site>0 AND ta.wederverkoop_verkoopprijs>0 AND (ta.wederverkoop_verkoopprijs<(ta.c_verkoop_site-10));");
	if($db->num_rows()) {
		while($db->next_record()) {
			$typeid_berekenen_inquery.=",".$db->f("type_id");
		}
		if($typeid_berekenen_inquery) {
			$typeid_berekenen_inquery=substr($typeid_berekenen_inquery,1);
			include($unixdir."cron/tarieven_berekenen.php");
		}
		wt_mail("jeroen@webtastic.nl","Onjuiste wederverkoop-tarieven Chalet.nl","Er zijn onjuiste wederverkoop-tarieven aangetroffen (via cron/elkeminuut.php). Er wordt geprobeerd de fout automatisch op te lossen.\n\n");
		$db->query("SELECT DISTINCT ta.type_id, ta.seizoen_id FROM tarief ta, type t, accommodatie a WHERE t.tonen=1 AND a.tonen=1 AND t.accommodatie_id=a.accommodatie_id AND ta.type_id=t.type_id AND ta.c_bruto>0 AND ta.c_verkoop_site>0 AND ta.wederverkoop_verkoopprijs>0 AND (ta.wederverkoop_verkoopprijs<(ta.c_verkoop_site-10));");
		if($db->num_rows()) {
			wt_mail("jeroen@webtastic.nl","Onjuiste wederverkoop-tarieven Chalet.nl","Er zijn onjuiste wederverkoop-tarieven aangetroffen (via cron/elkeminuut.php) op Chalet.nl.\n\nOpen de volgende pagina en wacht tot alles is verwerkt:\n\nhttps://www.chalet.nl/cms_tarieven_autosubmit.php?check=1&t=99&confirmed=1\n\n");
		}
	}
}

?>