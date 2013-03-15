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

if($_SERVER["DOCUMENT_ROOT"]!="/home/webtastic/html") {
	ini_set('display_errors', 'Off');
	ini_set('display_startup_errors', 'Off');
	error_reporting(0);
}
include($unixdir."admin/vars.php");
$username=array();
#$username[0]='Zomerhuisje';
$username[1]='Italissima';
$username[2]='ChaletNL';
$username[3]='SuperSkiNL';
foreach($username as $userAccount) {

	echo $userAccount."\n";

	$url="https://api.twitter.com/1.1/statuses/user_timeline.json";

	$oauth_consumer_key="QIK0DHtEfkvdzyzGodfvQ";
	$oauth_consumer_secret="lmosQrPKEp8ohjYtdB8ABZyVstGuF2uJNo5Gp133s";
	$oauth_access_token="132939299-3sC8Zjr68teoEI5GAuos2991MZ4pcrmyKbdqqffc";
	$oauth_access_token_secret="XPPUCnfBJKKdS4lqf9k16NhWSUicq6uotjkCuDtPRQM";

	$json_content=wt_get_tiwtter_url($url,array("screen_name"=>$userAccount,"include_entities"=>"true","exclude_replies"=>"true","include_rts"=>"false"),$oauth_consumer_key,$oauth_consumer_secret,$oauth_access_token,$oauth_access_token_secret);

	$json=json_decode($json_content["output"],true);


#	echo "Content:".wt_dump($xml_content);
#	exit;


	if(is_array($json)) {
		unset($bericht);
		if($userAccount=='Zomerhuisje') {
			$imgSrc="https://si0.twimg.com/profile_images/1388402637/ZOMERHUISJE_NL_vierkant.jpg";
			$backColor="#cfbcd8";
			$naam="Zomerhuisje.nl";
			$kopColor="#5F227B";
			$main_url="http://www.zomerhuisje.nl/";
		} elseif($userAccount=='ChaletNL') {
			$imgSrc="http://www.chalet.nl/pic/logo_chalet.gif";
			$backColor="#eaf0fc";
			$naam="Chalet.nl";
			$kopColor="#d40139";
			$main_url="http://www.chalet.nl/";
		} elseif($userAccount=='Italissima') {
			$backColor="#e0d1cc";
			$kopColor="#D40139";
			$naam="Italissima";
			$main_url="http://www.italissima.nl/";
		} elseif($userAccount=='SuperSkiNL') {
			$naam="SuperSki";
			$backColor="";
			$kopColor="#003366";
			$main_url="http://www.superski.nl/";
		}

		$teller=0;
		$teller_html=0;

		while(list($key,$status)=each($json)) {
			unset($html);
			$tweet_inhoud=iconv("UTF-8","cp1252",$status["text"]);

#echo $status->entities;

#echo wt_dump($status["entities"]["urls"]);

			# t.co-url's omzetten naar mooie url's
			unset($display_url,$expanded_url);
			if(is_array($status["entities"]["urls"])) {
				foreach($status["entities"]["urls"] as $urls) {
					$display_url[iconv("UTF-8","cp1252",$urls["url"])]=iconv("UTF-8","cp1252",$urls["display_url"]);
					$expanded_url[iconv("UTF-8","cp1252",$urls["url"])]=iconv("UTF-8","cp1252",$urls["display_url"]);
				}
			}

			# pic.twitter-t.co-url's omzetten naar mooie url's
			if(is_array($status["entities"]["media"])) {
				foreach($status["entities"]["media"] as $urls) {
					$display_url[iconv("UTF-8","cp1252",$urls["url"])]=iconv("UTF-8","cp1252",$urls["display_url"]);
					$expanded_url[iconv("UTF-8","cp1252",$urls["url"])]=iconv("UTF-8","cp1252",$urls["display_url"]);
				}
			}

#echo wt_dump($display_url);

			if(!preg_match("/@/",$tweet_inhoud)) {
				$bericht_array=explode(" ",$tweet_inhoud);

				for($a=0; $a<count($bericht_array);$a++) {
					if(substr($bericht_array[$a],0,4)=="http"){
						$html.=" <a style=\"text-decoration:underline;\" href=";
						if($expanded_url[$bericht_array[$a]]) {
							$html.=wt_he($expanded_url[$bericht_array[$a]]);
							if(substr($expanded_url[$bericht_array[$a]],0,strlen($main_url))==$main_url) {
								# interne URL
							} else {
								# externe URL
								$html.=" target=\"_blank\" rel=\"nofollow\"";
							}
						} else {
							$html.=wt_he($bericht_array[$a]);
							$html.=" target=\"_blank\" rel=\"nofollow\"";
						}
						$html.=">";
						if($display_url[$bericht_array[$a]]) {
							$html.=preg_replace("/\//","/&shy;",wt_he($display_url[$bericht_array[$a]]));
						} else {
							$html.=wt_he($bericht_array[$a]);
						}
						$html.="</a>";
					} else {
						$html.=" ".wt_he($bericht_array[$a]);
					}
				}
				$teller_html++;
				$bericht[$teller_html]=$html;

				if($teller_html>=3) break;
			}
			$teller++;
		}

		if($userAccount=='Italissima') {
			// horizontaal tweets tonen
			$content="<table cellspacing=\"0\" style=\"background-color:#e0d1cc;padding-left:25px;padding-top:5px; padding-bottom:5px; padding-right:25px; width:580px;\">
					<tr><td style=\"color:#661700; font-size:1.2em;padding-bottom:10px;\" colspan=\"2\"><a style=\"text-decoration:none;\" href=\"https://twitter.com/Italissima\" target=\"_blank\">Italissima op Twitter</a></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".$bericht[1]."<hr></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".$bericht[2]."<hr></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".$bericht[3]."</td></tr>
					</table>";
 		} elseif($userAccount=='SuperSkiNL') {
			$content="<table cellspacing=\"0\" style=\"padding-left:15px;padding-top:5px; padding-bottom:5px; padding-right:15px; width:100%;\">
					<tr><td style=\"color:#661700; font-size:1.2em;padding-bottom:10px;\" colspan=\"2\"><a style=\"text-decoration:none;\" href=\"https://twitter.com/SuperSkiNL\" target=\"_blank\">SuperSki op Twitter</a></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".$bericht[1]."<hr></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".$bericht[2]."<hr></td></tr>
					<tr><td valign=\"top\" colspan=\"2\" style=\"font-size:11px;\">".$bericht[3]."</td></tr>
					</table>";
		} else {
			// verticaal tweets tonen
			$content.="<div style=\"background-color:#cfbcd8; width:170px;\"><table id=\"hoofdpagina_twitter_blok\" cellspacing=\"2\" style=\"".($backColor ? "background-color:".$backColor.";" : "")."padding:5px;\">";
			$content.="<td style=\"color:".$kopColor.";font-size:14px;\"><div style=\"cursor:pointer;\"><a style=\"text-decoration:none;\" href=\"https://twitter.com/".$userAccount."\" target=\"_blank\">".$naam." op Twitter</a></div></td></tr><tr><td></td><td></td></tr>";
			$content.="<tr><td valign=\"top\" style=\"font-size:11px;\" colspan=\"2\">".$bericht[1]."<br><br></td></tr>";
			$content.="<tr><td valign=\"top\" style=\"font-size:11px;\" colspan=\"2\">".$bericht[2]."<br><br></td></tr>";
			$content.="<tr><td valign=\"top\" style=\"font-size:11px;\" colspan=\"2\">".$bericht[3]."<br><br></td></tr>";
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