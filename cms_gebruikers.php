<?php

$mustlogin=true;
include("admin/vars.php");

if(!$login->has_priv("1")) {
	header("Location: cms.php");
	exit;
}

$db->query("SELECT user_id, lasthosts FROM user WHERE userlevel>0;");
while($db->next_record()) {
	$a=split("\n",$db->f("lasthosts"));
	if(strlen($a[0])>5) {
		if(preg_match("/chromeframe/",$a[0])) {
			$browser[$db->f("user_id")]="Chrome via IE";
		} elseif(preg_match("/MSIE 7/",$a[0])) {
			$browser[$db->f("user_id")]="IE7";
		} elseif(preg_match("/MSIE 8/",$a[0])) {
			$browser[$db->f("user_id")]="IE8";
		} elseif(preg_match("/MSIE 9/",$a[0])) {
			$browser[$db->f("user_id")]="IE9";
		} elseif(preg_match("/iPad/",$a[0]) and preg_match("/CPU OS ([0-9]+)_/",$a[0],$regs)) {
			$browser[$db->f("user_id")]="iPad (iOS ".$regs[1].")";
		} elseif(preg_match("/iPhone/",$a[0]) and preg_match("/CPU iPhone OS ([0-9]+)_/",$a[0],$regs)) {
			$browser[$db->f("user_id")]="iPhone (iOS ".$regs[1].")";
		} elseif(preg_match("/Chrome\/([0-9]+)/",$a[0],$regs)) {
			if($login->userlevel>=10) {
				$browser[$db->f("user_id")]="Chrome ".$regs[1];
			} else {
				$browser[$db->f("user_id")]="Chrome";
			}
		} elseif(preg_match("/Firefox\/([0-9]+)/",$a[0],$regs)) {
			$browser[$db->f("user_id")]="Firefox ".$regs[1];
		} else {
			$browser[$db->f("user_id")]=$a[0];
		}

		if(preg_match("/\[([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})\]/",$a[0],$regs)) {
			if($regs[1]=="80.101.166.235") {
				$ipadres[$db->f("user_id")]="Kantoor (ADSL)";
			} elseif($regs[1]=="213.125.164.74") {
				$ipadres[$db->f("user_id")]="Kantoor (Ziggo)";
			} elseif($regs[1]=="82.93.130.238") {
				$ipadres[$db->f("user_id")]="Bert thuis";
			} elseif($regs[1]=="VERBERGEN_83.215.210.75") {
				$ipadres[$db->f("user_id")]="Bauernhaus";
			} elseif($regs[1]=="82.173.186.80") {
				$ipadres[$db->f("user_id")]="WebTastic";
			} else {
				$ipadres[$db->f("user_id")]="IP-adres ".$regs[1];
			}
		}
	}
}
#echo wt_dump($ipadres);

$vars["userlevel"]=array(1=>"actief",0=>"tijdelijk inactief",-1=>"definitief geblokkeerd");
if($login->userlevel==10) {
	$vars["userlevel"][10]="systeembeheerder";
}

#
# Database-declaratie
#
# Database db_field($counter,$type,$id,$field="",$options="")
$cms->db_field(25,"select","user_id","",array("selection"=>$browser));
$cms->db_field(25,"select","ipadres","user_id",array("selection"=>$ipadres));
$cms->db_field(25,"select","userlevel","",array("selection"=>$vars["userlevel"]));
$cms->db_field(25,"text","user");
$cms->db_field(25,"text","naam");
$cms->db_field(25,"text","voornaam");
$cms->db_field(25,"text","tussenvoegsel");
$cms->db_field(25,"text","achternaam");
$cms->db_field(25,"checkbox","priv","",array("selection"=>$vars["priv"]));
$cms->db_field(25,"yesno","cmslog_pagina");
$cms->db_field(25,"email","email");
$cms->db_field(25,"date","lastlogin");
$cms->db_field(25,"password","password");
if($login->userlevel==10 and $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$cms->db_field(25,"textarea","lasthosts");
}

if($_GET["t"]==2) {
	$cms->db[25]["where"]="userlevel<=0 AND user<>'bertdianne'";
} else {
	if($login->userlevel<10) {
		$cms->db[25]["where"]="user<>'webtastic' AND userlevel>=1";
	} else {
		$cms->db[25]["where"]="userlevel>=1";
	}
}

#
# List
#
# Te tonen icons/links bij list
$cms->settings[25]["list"]["show_icon"]=true;
$cms->settings[25]["list"]["edit_icon"]=true;
$cms->settings[25]["list"]["delete_icon"]=false;
$cms->settings[25]["list"]["add_link"]=true;

# List list_field($counter,$id,$title="",$options="",$layout="")
$cms->list_sort[25]=array("voornaam");
#$cms->list_field(25,"user","Gebruikersnaam");
$cms->list_field(25,"voornaam","Voornaam");
$cms->list_field(25,"lastlogin","Laatste login",array("date_format"=>"DAG D MAAND JJJJ, U:ZZ"));
if($_GET["t"]==2) {
	$cms->list_field(25,"userlevel","Status");
}
$cms->list_field(25,"ipadres","Laatste locatie");
$cms->list_field(25,"user_id","Laatste browser");

# Show show_field($counter,$id,$title="",$options="",$layout=""))
$cms->show_name[25]="gebruikersgegevens";
$cms->show_mainfield[25]="voornaam";
$cms->show_field(25,"voornaam");
$cms->show_field(25,"email");

# Controle op delete-opdracht
if($_GET["delete"]==25 and $_GET["25k0"]) {

}

# Bij wissen record: DELETEn van andere tabellen
if($cms->set_delete_init(25)) {

}

#
# Edit
#

# Edit edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="")
#$cms->edit_field(25,1,"naam","Naam");
$cms->edit_field(25,1,"userlevel","Status","",array("allow_0"=>true));
if($_GET["25k0"] and $_GET["edit"]==25) {
	$db->query("SELECT user_id, wrongtime FROM user WHERE wrongcount>=4 AND wrongtime>0;");
	if($db->next_record()) {
		$cms->edit_field(25,0,"htmlrow","<div style=\"background-color:yellow;padding:10px;\">Gebruiker geblokkeerd tot: ".date("d-m-Y, H:i",$db->f("wrongtime")+$login->settings["loginblocktime"])."<br><br><input type=\"checkbox\" name=\"free_user\" value=\"1\" id=\"free_user_id\"><label for=\"free_user_id\">&nbsp;blokkade van deze gebruiker opheffen</label></div>");
	}
}
$cms->edit_field(25,1,"voornaam","Voornaam");
$cms->edit_field(25,0,"tussenvoegsel","Tussenvoegsel");
$cms->edit_field(25,1,"achternaam","Achternaam");
$cms->edit_field(25,1,"user","Gebruikersnaam");
$cms->edit_field(25,0,"password","Wachtwoord","",array("new_password"=>true,"strong_password"=>true,"salt"=>$vars["salt"]));
$cms->edit_field(25,1,"email","E-mailadres");
$cms->edit_field(25,0,"priv","Privileges","","",array("one_per_line"=>true));
$cms->edit_field(25,0,"cmslog_pagina","Loggen van elke opgevraagde CMS-pagina",array("selection"=>true));
if($login->userlevel==10 and $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	$cms->edit_field(25,0,"lasthosts","Lasthosts","","",array("style"=>"width:100%;height:400px;","newline"=>true));
}

# Controle op ingevoerde formuliergegevens
$cms->set_edit_form_init(25);
if($cms_form[25]->filled) {
	if(!ereg("^[0-9a-z]+$",$cms_form[25]->input["user"])) {
		$cms_form[25]->error("user","alleen kleine letters en cijfers toegestaan");
	}
}

# functie na opslaan form
function form_before_goto($form) {
	$db=new DB_sql;
	global $login,$vars;
	if($_POST["free_user"]) {
		$db->query("UPDATE user SET wrongcount=0, wrongtime=0 WHERE user_id='".addslashes($_GET["25k0"])."';");
	}
}

# End declaration
$cms->end_declaration();

# Vormgeving weergeven
$layout->display_all($cms->page_title);

?>