<?php

#
# errorLog - wordt elke 15 minuten gerund en doorzoekt /var/log/httpd-error.log op nieuwe foutmeldingen van Apache
#


set_time_limit(0);
if($_SERVER["HTTP_HOST"]) {
	$unixdir="../";
} elseif($_SERVER["SCRIPT_NAME"]=="/home/webtastic/html/chalet/cron/errorLogReporter.php") {
	$unixdir="/home/webtastic/html/chalet/";
} else {
	if($_SERVER["PWD"]=="/var/www/chalet.nl") {
		$unixdir="/var/www/chalet.nl/html/";
	} else {
		$unixdir="/home/sites/chalet.nl/html/";
	}
}
$cron=true;
$geen_tracker_cookie=true;
$boeking_bepaalt_taal=true;
include($unixdir."admin/vars.php");

function transformTodate($stringMaand,$stringDag,$stringTime){
	switch($stringMaand){
		case "Jan":
			$stringMaand=1;
			break;
			case "Feb":
			$stringMaand=2;
			break;
			case "Mar":
			$stringMaand=3;
			break;
			case "Apr":
			$stringMaand=4;
			break;
			case "May":
			$stringMaand=5;
			break;
			case "Jun":
			$stringMaand=6;
			break;
			case "Jul":
			$stringMaand=7;
			break;
			case "Aug":
			$stringMaand=8;
			break;
			case "Sep":
			$stringMaand=9;
			break;
			case "Oct":
			$stringMaand=10;
			break;
			case "Nov":
			$stringMaand=11;
			break;
			case "Dec":
			$stringMaand=12;
			break;
			case "jan":
			$stringMaand=1;
			break;
			case "feb":
			$stringMaand=2;
			break;
			case "mar":
			$stringMaand=3;
			break;
			case "apr":
			$stringMaand=4;
			break;
			case "may":
			$stringMaand=5;
			break;
			case "jun":
			$stringMaand=6;
			break;
			case "jul":
			$stringMaand=7;
			break;
			case "aug":
			$stringMaand=8;
			break;
			case "sep":
			$stringMaand=9;
			break;
			case "oct":
			$stringMaand=10;
			break;
			case "nov":
			$stringMaand=11;
			break;
			case "dec":
			$stringMaand=12;
			break;
	}
	$jaar=date("Y");
	$date=strtotime($jaar."/".$stringMaand."/".$stringDag." ".$stringTime);
	return $date;
}
$negate=array();
$negate[0]=" [notice";
$negate[1]=" File does not exist:";
$negate[2]=" [notice";



$intervals=array();
$intervals[0]=0;
$intervals[1]=1;
$intervals[2]=2;
$intervals[3]=3;
$intervals[4]=4;
$intervals[5]=5;
$intervals[6]=6;
$intervals[7]=7;
$intervals[8]=8;
$intervals[9]=9;
$intervals[10]=10;
$intervals[11]=11;
$intervals[12]=12;
$intervals[13]=13;
$intervals[14]=14;
$intervals[15]=15;
$intervals[16]=-15;
$intervals[17]=-14;
$intervals[18]=-13;
$intervals[19]=-12;
$intervals[20]=-11;
$intervals[21]=-10;
$intervals[22]=-9;
$intervals[23]=-8;
$intervals[24]=-7;
$intervals[25]=-6;
$intervals[26]=-5;
$intervals[27]=-4;
$intervals[28]=-3;
$intervals[29]=-2;
$intervals[30]=-1;
$handle=@fopen("/var/log/httpd-error.log", "r");
if($handle) {
	$logs="";
	while(($buffer=fgets($handle,4096))!==false) {
		if(substr($buffer,0,1)!="["){
			$date=substr($buffer,0,15);
			$params=explode(" ",$buffer);
			$dag=$params[1];
			$maand=$params[0];
			$tijd=$params[2];
			$datum=transformTodate($maand,$dag,$tijd);
			$van=date("i",$datum);
			$tot=date("i");
			$verschil=$van-$tot;
			$data=strtotime(date("Y/m/d"));
			if(date("d/m/Y",$datum)==date("d/m/Y",$data) and date("H",$datum)==date("H")) {
				if(in_array($verschil,$intervals)) {
					$logs.=$buffer;
					$logs.="\n";
				}
			}
		} else {
			$params=explode("]",$buffer);
			$paramsVerder=explode(" ",$params[0]);
			$dag=$paramsVerder[2];
			$maand=$paramsVerder[1];
			$tijd=$paramsVerder[3];
			$datum=transformTodate($maand,$dag,$tijd);
			$van=date("i",$datum);
			$tot=date("i");
			$verschil=$van-$tot;
			$data=strtotime(date("Y/m/d"));
			if((!in_array($params[1],$negate)) and (!in_array(substr($params[3],0,21),$negate))) {
				if(date("d/m/Y",$datum)==date("d/m/Y",$data) and date("H",$datum)==date("H")) {
					if(in_array($verschil,$intervals)){
						$logs.=$buffer;
						$logs.="\n";
					}
				}
			}
		}
	}
	if (!feof($handle)) {
		echo "Error: unexpected fgets() fail\n";
	}
	if($logs!=""){
		$mail=new wt_mail;
		$mail->fromname="Chalet.nl";
		$mail->from="info@chalet.nl";
		$mail->toname="Chalet.nl Mailbackup";
		$mail->to="chaletmailbackup+errorlog@gmail.com";
#		$mail->bcc="jeroen@webtastic.nl";
		$mail->subject.="httpd errorlog report";
		$mail->plaintext=$logs; # deze leeg laten bij een opmaak-mailtje
		$mail->send();
		echo "mail met log berichten succesvol verzonden";
	} else {
		echo "Er zijn geen nieuwe meldingen binnen gekomen.";
	}
	fclose($handle);
}

?>