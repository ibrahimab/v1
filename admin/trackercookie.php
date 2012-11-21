<?php


#
# trackercookie
#

# TIJDELIJK Cookies wissen
if($_COOKIE["flc"]) {
	if(is_array($_COOKIE)) {
		while(list($key,$value)=each($_COOKIE)) {
			if(@ereg("^TT2_",$key)) {
				$cookietime=time()-86400;
				setcookie($key,"",$cookietime,"/");
			}
		}
	}
}

#unset($_COOKIE,$mustlogin);

# Controle op Cleafs-referer
if($_GET["network"]=="cleafs") {
	$_GET["chad"]=30;
	@setcookie("cleafs",time(),(time()+4320000),"/");
	$vars["bezoek_altijd_opslaan"]=true;
}
# Controle op tradetracker-referer
if(!$_GET["chad"] and $_GET["network"]=="tradetracker") {
	$_GET["chad"]=20;
	@setcookie("tradetracker",time(),(time()+4320000),"/");
	$vars["bezoek_altijd_opslaan"]=true;
}

#
# Bezoekers-statistieken opslaan
#
if((!$_COOKIE["tch"] or $vars["bezoek_altijd_opslaan"]) and !$mustlogin) {
	#
	# tch = "temp chalet" (=sessie-cookie)
	# sch = "solid chalet" (=gewoon cookie)
	# chad = chalet ad (code voor advertentie)
	# wt_mm = Mailingmanager

	if($_GET["wt_mm"]) $_GET["chad"]=11;

	if(!$_GET["chad"] and (ereg("^http://googleads\.",$_SERVER["HTTP_REFERER"]) or $_GET["gclid"])) {
		# Google Ads
		$_GET["chad"]=1;
	}

	# Controle op $_GET["chad"] (zie $vars["ads_controle"])
	if(ereg("^([A-Z]{3})([0-9]+)$",$_GET["chad"],$regs)) {
		if($vars["ads_controle"][$regs[2]]==$regs[1]) {
			$_GET["chad"]=$regs[2];
		} else {
			$_GET["chad"]="";
		}
	}

	if(ereg("mail\.live\.com",$_SERVER["HTTP_REFERER"]) or ereg("mail\.google\.com",$_SERVER["HTTP_REFERER"])) {
		$vanwebmail=true;
	}

	if(ereg("www\.chalet\.nl/cms_",$_SERVER["HTTP_REFERER"])) {
		$vancms=true;
	}

	if($_SERVER["DOCUMENT_ROOT"]<>"/home/webtastic/html" and $_SERVER["HTTP_REFERER"]) {
		if(strpos("_".$_SERVER["HTTP_REFERER"],$_SERVER["HTTP_HOST"])) {
			$intern=true;
		}
	}
	if(eregi("IlseBot",$_SERVER["HTTP_USER_AGENT"]) or eregi("Googlebot",$_SERVER["HTTP_USER_AGENT"]) or eregi("Yahoo.*Slurp",$_SERVER["HTTP_USER_AGENT"]) or eregi("msnbot",$_SERVER["HTTP_USER_AGENT"])) {
		$intern=true;
	}
	if(!$intern) {
		if($_COOKIE["sch"] and strlen($_COOKIE["sch"])==32) {
			$code=$_COOKIE["sch"];
			$db->query("SELECT bezoeker_id FROM bezoeker WHERE bezoeker_id='".addslashes($code)."';");
			if($db->num_rows()) {
				$vars["trackercookie_terugkerende_bezoeker"]=true;
			} else {
				$db->query("INSERT INTO bezoeker SET bezoeker_id='".addslashes($code)."', site='".addslashes($_SERVER["HTTP_HOST"])."', gewijzigd=NOW();");
			}
			setcookie("sch",$code,time()+(86400*365*10),"/");
		} else {

#			$db->query("SELECT bezoeker_id FROM bezoek WHERE ip='".addslashes($_SERVER["REMOTE_ADDR"])."' AND browser='".addslashes($_SERVER["HTTP_USER_AGENT"])."' AND UNIX_TIMESTAMP(datumtijd)>'".(time()-600)."';");
#			if($db->next_record()) {
#				$intern=true;
#				$code=$db->f("bezoeker_id");
#			} else {
				$code=wt_create_id("bezoeker","bezoeker_id",32);
				if($_COOKIE["last_acc"]) {
					$tmp_lastacc=$_COOKIE["last_acc"];
					$cookietime=time()-86400;
					setcookie("last_acc","",$cookietime,"/");
				} else {
					$tmp_lastacc="";
				}
				$db->query("INSERT INTO bezoeker SET bezoeker_id='".addslashes($code)."', site='".addslashes($_SERVER["HTTP_HOST"])."'".($tmp_lastacc ? ", last_acc='".addslashes($tmp_lastacc)."'" : "").", gewijzigd=NOW();");
				setcookie("sch",$code,time()+(86400*365*10),"/");
				$_COOKIE["sch"]=$code;
#			}
		}
	}
	if(!$intern and !$vanwebmail and !$vancms) {
		if($_GET["chad"]) $ad=$_GET["chad"];
		$db->query("INSERT INTO bezoek SET bezoeker_id='".addslashes($code)."', datumtijd=NOW(), referer='".addslashes($_SERVER["HTTP_REFERER"])."', ad='".addslashes($ad)."', enterpage='".addslashes($_SERVER["REQUEST_URI"])."', browser='".addslashes($_SERVER["HTTP_USER_AGENT"])."', ip='".addslashes($_SERVER["REMOTE_ADDR"])."';");
	}
	if(!$code) $code="unk";
	setcookie("tch",$code,0,"/");
	$_COOKIE["tch"]=$code;
}

if(!$_COOKIE["abt"]) {
	#
	# A/B-testing-cookie bepalen: $_COOKIE["abt"] (waarde van 0-9)
	#

	# Waarde van 0-9 bepalen t.b.v. A/B-testing
	$code_abt=substr(preg_replace("/[a-z]/","",$_COOKIE["tch"]),0,1);

	if(!$code_abt) $code_abt=rand(0,9);
	setcookie("abt",$code_abt,time()+(86400*365*10),"/");
	$_COOKIE["abt"]=$code_abt;
}

# Pagina reloaden (maar dan zonder chad=)
if($_GET["chad"] and ereg("^[A-Z0-9]+$",$_GET["chad"]) and ereg("chad=[A-Z0-9]+",$_SERVER["REQUEST_URI"])) {
	$url=$_SERVER["REQUEST_URI"];
	$url=ereg_replace("\?chad=[A-Z0-9]+&","?",$url);
	$url=ereg_replace("\?chad=[A-Z0-9]+","",$url);
	$url=ereg_replace("&chad=[A-Z0-9]+","",$url);
	header("Location: ".$url);
	exit;
}

# Pagina reloaden (maar dan zonder network=X)
if($_GET["network"]=="cleafs") {
	$url=$_SERVER["REQUEST_URI"];
	$url=ereg_replace("\?network=cleafs&","?",$url);
	$url=ereg_replace("\?network=cleafs","",$url);
	$url=ereg_replace("&network=cleafs","",$url);
	header("Location: ".$url);
	exit;
}
if($_GET["network"]=="tradetracker") {
	$url=$_SERVER["REQUEST_URI"];
	$url=ereg_replace("\?network=tradetracker&","?",$url);
	$url=ereg_replace("\?network=tradetracker","",$url);
	$url=ereg_replace("&network=tradetracker","",$url);
	header("Location: ".$url);
	exit;
}

?>