<?php

$mustlogin=true;

if($_GET["t"]==3) {
	$vars["types_in_vars"]=true;
	$vars["acc_in_vars"]=true;
}

include("admin/vars.php");

if($_POST["winterkoppeling"]) {
	
	if(is_array($_POST["geenkoppeling"])) {
		while(list($key,$value)=each($_POST["geenkoppeling"])) {
			if($value) {
				$db->query("UPDATE type SET geenzomerwinterkoppeling=1 WHERE type_id='".addslashes($key)."';");
			}
		}
	}
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}

if($_GET["t"]==5) {
	#
	# Overzicht vouchers
	#
	
	# frm = formname (mag ook wat anders zijn)
	$form=new form2("frm"); 
	$form->settings["fullname"]="Periode";
	$form->settings["layout"]["css"]=true;
	$form->settings["type"]="get";
	
	$form->settings["message"]["submitbutton"]["nl"]="OK";
	
	# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
	$form->settings["layout"]["goto_aname"]=true;
	  
	#_field: (obl),id,title,db,prevalue,options,layout

#$vars["zaterdag_over_6_weken"]=mktime(0,0,0,date("m"),date("d")+(6-date("w"))+(6*7),date("Y"));

	# begin = zaterdag een week geleden
	$vars["temp_begin"]=mktime(0,0,0,date("m"),date("d")+(6-date("w"))-14,date("Y"));
	$vars["temp_eind"]=mktime(0,0,0,date("m"),date("d")+(6-date("w"))+28,date("Y"));
	
	$form->field_htmlrow("","<i>Aankomstdatum</i>");
	$form->field_date(1,"van","Van","",array("time"=>$vars["temp_begin"]),array("startyear"=>2010,"endyear"=>date("Y")+1),array("calendar"=>true));
	$form->field_date(1,"tot","Tot en met","",array("time"=>$vars["temp_eind"]),array("startyear"=>2010,"endyear"=>date("Y")+1),array("calendar"=>true));
	
	$form->check_input();
	
	if($form->input["van"]["unixtime"]>$form->input["tot"]["unixtime"]) $form->error("tot","moet later zijn dan de eerste");
	
	$form->end_declaration();
} elseif($_GET["t"]==10 and $_GET["dl"] and $_GET["confirmed"]==1) {
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2") {
		echo "<pre>";
	} else {
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=nieuwsbriefleden_".($_GET["dl"]==2 ? "reisagenten_" : "").date("Y_m_d").".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
	}
	$exportmoment=time();
	if($_GET["dl"]==1) {
		#
		# Gewone nieuwsbrief
		#
		echo wt_csvconvert("e-mailadres").";".wt_csvconvert("voornaam",";").";".wt_csvconvert("tussenvoegsel",";").";".wt_csvconvert("achternaam",";")."\n";
		$db->query("SELECT nieuwsbrieflid_id, email, voornaam, tussenvoegsel, achternaam FROM nieuwsbrieflid WHERE export IS NULL ORDER BY nieuwsbrieflid_id;");
		while($db->next_record()) {
			echo wt_csvconvert($db->f("email")).";".wt_csvconvert($db->f("voornaam"),";").";".wt_csvconvert($db->f("tussenvoegsel"),";").";".wt_csvconvert($db->f("achternaam"),";")."\n";
			$db2->query("UPDATE nieuwsbrieflid SET export=FROM_UNIXTIME(".$exportmoment.") WHERE nieuwsbrieflid_id='".addslashes($db->f("nieuwsbrieflid_id"))."';");
		}
	} elseif($_GET["dl"]==2) {
		#
		# Reisagenten-nieuwsbrief
		#
		echo wt_csvconvert("e-mailadres").";".wt_csvconvert("voornaam",";").";".wt_csvconvert("tussenvoegsel",";").";".wt_csvconvert("achternaam",";").";".wt_csvconvert("reisagent",";").";".wt_csvconvert("gewoon",";")."\n";
		$db->query("SELECT user_id, email, voornaam, tussenvoegsel, achternaam, mailingmanager_agentennieuwsbrief, mailingmanager_gewonenieuwsbrief FROM reisbureau_user WHERE nieuwsbriefexport IS NULL AND userlevel=1 AND email<>'' AND (mailingmanager_agentennieuwsbrief=1 OR mailingmanager_gewonenieuwsbrief=1) ORDER BY user_id;");
		while($db->next_record()) {
			echo wt_csvconvert($db->f("email")).";".wt_csvconvert($db->f("voornaam"),";").";".wt_csvconvert($db->f("tussenvoegsel"),";").";".wt_csvconvert($db->f("achternaam"),";").";";
			if($db->f("mailingmanager_agentennieuwsbrief")==1) {
				echo wt_csvconvert("ja",";").";";
			} else {
				echo ";";
			}
			if($db->f("mailingmanager_gewonenieuwsbrief")==1) {
				echo wt_csvconvert("ja",";").";";
			} else {
				echo ";";
			}
			echo "\n";
			$db2->query("UPDATE reisbureau_user SET nieuwsbriefexport=FROM_UNIXTIME(".$exportmoment.") WHERE user_id='".addslashes($db->f("user_id"))."';");
		}
	}
	exit;
} elseif($_GET["t"]==10) {

	# frm = formname (mag ook wat anders zijn)
	$form=new form2("frm"); 
	$form->settings["fullname"]="diverseinstellingen";
	$form->settings["layout"]["css"]=false;
	$form->settings["db"]["table"]="diverse_instellingen";
	$form->settings["db"]["where"]="diverse_instellingen_id=1";
	$form->settings["message"]["submitbutton"]["nl"]="OPSLAAN";
	#$form->settings["target"]="_blank";
	 
	# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
	$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen	
	
	#_field: (obl),id,title,db,prevalue,options,layout
	
	$form->field_htmlrow("","<b>Links naar nieuwsbrieven</b><br><br>per regel: <i>nieuwsbrief-nummer [spatie] Tekst</i><br><br><i>http://chaletnl.m16.mailplus.nl/archief/mailing-31311490.html</i> wordt: <i>31311490 28 november 2011 – De sneeuw komt eraan!</i>");
	#$form->field_text(1,"test","test",array("field"=>"test")); # (opslaan in databaseveld "test")
	$form->field_textarea(1,"winternieuwsbrieven","Winternieuwsbrieven",array("field"=>"winternieuwsbrieven"));
	$form->field_textarea(1,"zomernieuwsbrieven","Zomernieuwsbrieven",array("field"=>"zomernieuwsbrieven"));
	
	$form->check_input();
	
	if($form->filled) {

	}
	
	if($form->okay) {
		$form->save_db();
		$_SESSION["wt_popupmsg"]="gegevens zijn correct opgeslagen";
		header("Location: ".$_SERVER["REQUEST_URI"]."#top");
		exit;
	}
	$form->end_declaration();
}

$layout->display_all($cms->page_title);

?>