<?php

#
# Twitter-cache aanmaken
#

#
# Wordt elke 5 minuten aangemaakt via een cronjob.
#
# zelf runnen: /usr/bin/php --php-ini /var/www/chalet.nl/php_cli.ini /var/www/chalet.nl/html/cron/twitter.php
#
#

set_time_limit(0);
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
	$tmpdir="/tmp/";
} else {
	$unixdir="/var/www/chalet.nl/html/";
	$tmpdir="/var/www/chalet.nl/html/tmp/";
}

$cron=true;
$geen_tracker_cookie=true;

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);
include($unixdir."admin/vars.php");
$username=array();
#$username[0]='Zomerhuisje';
$username[1]='Italissima';
$username[2]='ChaletNL';
$username[3]='SuperSkiNL';
$format='xml';
foreach($username as $userAccount) {
#	if($userAccount=='SuperSkiNL') {
#		$get_user_account='ChaletNL';
#	} else {
		$get_user_account=$userAccount;
#	}
	$tweet=simplexml_load_file("http://api.twitter.com/1/statuses/user_timeline/{$get_user_account}.{$format}");
	if($tweet->status[0]->text!="") {
		$bericht="";
		$berichtNext="";
		$berichtNextNext="";
		$bericht1=explode(" ",iconv("UTF-8","cp1252",$tweet->status[0]->text));
		$bericht2=explode(" ",iconv("UTF-8","cp1252",$tweet->status[1]->text));
		$bericht3=explode(" ",iconv("UTF-8","cp1252",$tweet->status[2]->text));
		for($a=0; $a<count($bericht1);$a++) {
			if($userAccount=='Zomerhuisje') {
				$imgSrc="https://si0.twimg.com/profile_images/1388402637/ZOMERHUISJE_NL_vierkant.jpg";
				$backColor="#cfbcd8";
				$naam="Zomerhuisje.nl";
				$kopColor="#5F227B";
			} elseif($userAccount=='ChaletNL') {
				$imgSrc="http://www.chalet.nl/pic/logo_chalet.gif";
				$backColor="#eaf0fc";
				$naam="Chalet.nl";
				$kopColor="#d40139";
			} elseif($userAccount=='Italissima') {
				$backColor="#e0d1cc";
				$kopColor="#D40139";
				$naam="Italissima";
			} elseif($userAccount=='SuperSkiNL') {
				$naam="SuperSki";
				$backColor="";
				$kopColor="#003366";
			}
			if(substr($bericht1[$a],0,4)=="http") {
				$bericht.="<BR><a style=\"text-decoration:underline;\" href=";
				$bericht.=wt_he($bericht1[$a]);
				$bericht.=" target=\"_blank\">";
				$bericht.=wt_he($bericht1[$a]);
				$bericht.="</a>";
			} else {
				$bericht.=" ".wt_he($bericht1[$a]);
			}
		}
		for($a=0; $a<count($bericht2);$a++) {
			if(substr($bericht2[$a],0,4)=="http"){
				$berichtNext.="<BR><a style=\"text-decoration:underline;\" href=";
				$berichtNext.=wt_he($bericht2[$a]);
				$berichtNext.=" target=\"_blank\">";
				$berichtNext.=wt_he($bericht2[$a]);
				$berichtNext.="</a>";
			} else {
				$berichtNext.=" ".wt_he($bericht2[$a]);
			}
		}
		for($a=0; $a<count($bericht3);$a++) {
			if(substr($bericht3[$a],0,4)=="http"){
				$berichtNextNext.="<BR><a style=\"text-decoration:underline;\" href=";
				$berichtNextNext.=wt_he($bericht3[$a]);
				$berichtNextNext.=" target=\"_blank\">";
				$berichtNextNext.=wt_he($bericht3[$a]);
				$berichtNextNext.="</a>";
			} else {
				$berichtNextNext.=" ".wt_he($bericht3[$a]);
			}
		}
		if($userAccount=='Italissima') {
			// horizontaal tweets tonen
			$content="<table cellspacing=\"0\" style=\"background-color:#e0d1cc;padding-left:25px;padding-top:5px; padding-bottom:5px; padding-right:25px; width:580px;\">
					<tr><td style=\"color:#661700; font-size:1.2em;padding-bottom:10px;\" colspan=\"2\"><a style=\"text-decoration:none;\" href=\"https://twitter.com/Italissima\" target=\"_blank\">Italissima op Twitter</a></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".$bericht."<hr></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".$berichtNext."<hr></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".$berichtNextNext."</td></tr>
					</table>";
 		} elseif($userAccount=='SuperSkiNL') {
			$content="<table cellspacing=\"0\" style=\"padding-left:15px;padding-top:5px; padding-bottom:5px; padding-right:15px; width:100%;\">
					<tr><td style=\"color:#661700; font-size:1.2em;padding-bottom:10px;\" colspan=\"2\"><a style=\"text-decoration:none;\" href=\"https://twitter.com/SuperSkiNL\" target=\"_blank\">SuperSki op Twitter</a></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".$bericht."<hr></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".$berichtNext."<hr></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".$berichtNextNext."</td></tr>
					</table>";
		} else {
			// verticaal tweets tonen
			$content.="<div style=\"background-color:#cfbcd8; width:170px;\"><table id=\"hoofdpagina_twitter_blok\" cellspacing=\"2\" style=\"".($backColor ? "background-color:".$backColor.";" : "")."padding:5px;\">";
			$content.="<td style=\"color:".$kopColor.";font-size:14px;\"><div style=\"cursor:pointer;\" onclick=\"document.location.href='https://twitter.com/$userAccount';\">".$naam." op Twitter</div></td></tr><tr><td></td><td></td></tr>";
			$content.="<tr><td valign=\"top\" style=\"font-size:11px;\" colspan=\"2\">".$bericht."<br><br></td></tr>";
			$content.="<tr><td valign=\"top\" style=\"font-size:11px;\" colspan=\"2\">".$berichtNext."<br><br></td></tr>";
			$content.="<tr><td valign=\"top\" style=\"font-size:11px;\" colspan=\"2\">".$berichtNextNext."<br><br></td></tr>";
			$content.="</table></div>";
		}
		$toWrite=$unixdir."cache/twitter".$userAccount.".html";
		$handle=fopen($toWrite,'w') or die('Cannot open file:  '.$toWrite);
		$data=$content;
		fwrite($handle, $data);
		$content="";
		echo "success\n";
	}
}

?>