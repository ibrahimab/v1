<?php

#
#
#

$mustlogin=true;

include("admin/vars.php");

if($_POST["filled"]) {
	# Gegevens opslaan in database
	while(list($key,$value)=each($vars["sjabloon_velden"])) {
		if(is_array($_POST[$value])) {
			reset($_POST[$value]);
			while(list($key2,$value2)=each($_POST[$value])) {
				if($value=="aflopen_allotment") {
					if($value2<>"") {
						$savequery[$key2].=", ".$value."='".addslashes($value2)."'";
					} else {
						$savequery[$key2].=", ".$value."=NULL";
					}
				} else {
					$savequery[$key2].=", ".$value."='".addslashes($value2)."'";
				}
			}
		}
	}

	# Eerst gegevens wissen
	$db->query("DELETE FROM calculatiesjabloon_week WHERE leverancier_id='".addslashes($_GET["lid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");

	# Dan opslaan
	while(list($key,$value)=each($savequery)) {
		$db->query("INSERT INTO calculatiesjabloon_week SET leverancier_id='".addslashes($_GET["lid"])."', seizoen_id='".addslashes($_GET["sid"])."', week='".$key."'".$value.";");
#		echo $db->lastquery."<br>";
	}

	# Vroegboekkorting-datums opslaan in tabel calculatiesjabloon

	reset($vars["tarief_datum_velden"]);
	while(list($key,$value)=each($vars["tarief_datum_velden"])) {
		if(ereg("^([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})$",$_POST[$value],$regs)) {
			$vroegboekdatum_query.=", ".$value."=FROM_UNIXTIME(".mktime(0,0,0,$regs[2],$regs[1],$regs[3]).")";
		} else {
			$vroegboekdatum_query.=", ".$value."=''";
		}
	}
	if($vroegboekdatum_query) {
		$db->query("SELECT leverancier_id FROM calculatiesjabloon WHERE leverancier_id='".addslashes($_GET["lid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
		if($db->next_record()) {
			$db->query("UPDATE calculatiesjabloon SET ".substr($vroegboekdatum_query,2)." WHERE leverancier_id='".addslashes($_GET["lid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
		} else {
			$db->query("INSERT INTO calculatiesjabloon SET leverancier_id='".addslashes($_GET["lid"])."', seizoen_id='".addslashes($_GET["sid"])."'".$vroegboekdatum_query.";");
		}
	}
#	echo "<html>\n<head>\n<body onload=\"self.close();\">&nbsp;<br></body></html>";
	header("Location: ".$_GET["from"]);
	exit;
} else {
	# Gegevens ophalen uit database
	$db->query("SELECT week, korting_percentage, toeslag, korting_euro, vroegboekkorting_percentage, vroegboekkorting_euro, opslag_accommodatie, opslag_skipas, korting_arrangement_bed_percentage, toeslag_arrangement_euro, korting_arrangement_euro, toeslag_bed_euro, korting_bed_euro, vroegboekkorting_arrangement_percentage, vroegboekkorting_arrangement_euro, vroegboekkorting_bed_percentage, vroegboekkorting_bed_euro, opslag, c_korting_percentage, c_toeslag, c_korting_euro, c_vroegboekkorting_percentage, c_vroegboekkorting_euro, c_opslag_accommodatie, wederverkoop_opslag_euro, wederverkoop_opslag_percentage, wederverkoop_commissie_agent, aflopen_allotment FROM calculatiesjabloon_week WHERE leverancier_id='".addslashes($_GET["lid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	while($db->next_record()) {
		reset($vars["sjabloon_velden"]);
		while(list($key,$value)=each($vars["sjabloon_velden"])) {
			if($value=="aflopen_allotment") {
				if($db->f($value)<>"") $sjabloon["weken"][$db->f("week")][$value]=$db->f($value);
			} else {
				if($db->f($value)>0) $sjabloon["weken"][$db->f("week")][$value]=$db->f($value);
			}
		}
	}
	reset($vars["tarief_datum_velden"]);
	unset($datum_velden);
	while(list($key,$value)=each($vars["tarief_datum_velden"])) {
		$datum_velden.=", UNIX_TIMESTAMP(".$value.") AS ".$value;
	}
	$db->query("SELECT ".substr($datum_velden,2)." FROM calculatiesjabloon WHERE leverancier_id='".addslashes($_GET["lid"])."' AND seizoen_id='".addslashes($_GET["sid"])."';");
	if($db->next_record()) {
		$bestaand_sjabloon=true;
		reset($vars["tarief_datum_velden"]);
		while(list($key,$value)=each($vars["tarief_datum_velden"])) {
			if($db->f($value)) {
				$sjabloon[$value]=date("d-m-Y",$db->f($value));

				# Indien datum verstreken: vroegboekkorting_percentage wissen
				if($db->f($value)<mktime(0,0,0,date("m"),date("d"),date("Y"))) {
					$doorloop_array=$sjabloon["weken"];
					reset($doorloop_array);
					while(list($key2,$value2)=each($doorloop_array)) {
						$sjabloon["weken"][$key2][substr($value,0,-6)]="";
					}
				}
			}
		}
	}
}

# Seizoengegevens laden
$db->query("SELECT naam, UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind FROM seizoen WHERE seizoen_id='".addslashes($_GET["sid"])."';");
if($db->next_record()) {
	$seizoen["naam"]=$db->f("naam");
	$seizoen["begin"]=$db->f("begin");
	$seizoen["eind"]=$db->f("eind");
}

# Leveranciergegevens laden
$db->query("SELECT l.naam, l.aflopen_allotment FROM leverancier l WHERE l.leverancier_id='".addslashes($_GET["lid"])."';");
if($db->next_record()) {
	$leverancier["naam"]=$db->f("naam");
	$leverancier["aflopen_allotment"]=$db->f("aflopen_allotment");
}

include("content/cms_calculatiesjablonen.html");

?>