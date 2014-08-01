<?php

$geen_tracker_cookie=true;
$banner_size["728x90"]  = array(1, 10, 13, 16, 19, 22, 25, 28, 31);
$banner_size["120x600"] = array(5, 11, 14, 17, 20, 23, 26, 29, 32);
$banner_size["300x250"] = array(6, 12, 15, 18, 21, 24, 27, 30, 33);
$banner_size["300x600"] = array(34);
$banner_size["970x250"] = array(35);

$unixdir="../../../";
include("../../../admin/vars.php");

if(!$_GET["themadatum"]) $_GET["themadatum"]=1;
if(!$_GET["n"]) $_GET["n"]=1;



$netwerken[1]="TradeTracker";
$netwerken[2]="Sneeuwhoogte.nl";
$netwerken[3]="Snowplaza";

if($_GET["n"]==1) {
	# TradeTracker
	$extra_qs="network=tradetracker&";
} elseif($_GET["n"]==2) {
	# Cleafs
	$extra_qs="network=sneeuwhoogte&";
} elseif($_GET["n"]==3) {
	# Snowplaza
	$extra_qs="chad=KWX12&";
}

#if($_GET["wzt"]==1 and $_GET["themadatum"]==2) {
#	$db->query("SELECT UNIX_TIMESTAMP(begin) AS begin, UNIX_TIMESTAMP(eind) AS eind FROM seizoen WHERE type=1 AND EIND>NOW() ORDER BY begin LIMIT 0,1;");
#	if($db->next_record()) {
#
#	}
#}

if($_GET["wzt"]==2) {
	$site="zomerhuisje";
	$siteurl="https://www.zomerhuisje.nl/";
} elseif($_GET["wzt"]==3) {
	$site="italissima";
	$siteurl="https://www.italissima.nl/";

	$db->query("SELECT DISTINCT skigebied_id, skigebied FROM view_accommodatie WHERE websites LIKE '%I%' AND atonen=1 AND ttonen=1 ORDER BY skigebied;");
	while($db->next_record()) {
		$regio[$db->f("skigebied_id")]=$db->f("skigebied");
	}
} elseif($_GET["wzt"]==4) {
	$site="chalet";
	$siteurl="https://www.chalet.be/";
} elseif($_GET["wzt"]==6) {
	$site="italissima";
	$siteurl="https://www.italissima.be/";

	$db->query("SELECT DISTINCT skigebied_id, skigebied FROM view_accommodatie WHERE websites LIKE '%I%' AND atonen=1 AND ttonen=1 ORDER BY skigebied;");
	while($db->next_record()) {
		$regio[$db->f("skigebied_id")]=$db->f("skigebied");
	}
} elseif($_GET["wzt"]==9) {
	$site="chalet";
	$siteurl="https://www.chalet.eu/";
	$vars["taal"]="en";
} else {
	$site="chalet";
	$siteurl="https://www.chalet.nl/";
}

if($_GET["t"]==4) {
	$buttontoevoeging="_klein";
}

function zomer_land($width="130px") {
	echo "<select name=\"l\" style=\"width:".$width.";\" onchange=\"formsubmit('l');\">\n";
	if($_GET["themadatum"]==2) {
		echo "<option value=\"0\" selected>Kies land</option>\n";
	} else {
		echo "<option value=\"0\" selected>Zoek op land</option>\n";
	}
	echo "<option value=\"1\">Frankrijk</option>\n";
	echo "<option value=\"2\">Oostenrijk</option>\n";
	echo "<option value=\"3\">Zwitserland</option>\n";
	echo "<option value=\"5\">Itali&euml;</option>\n";
	echo "<option value=\"6\">Duitsland</option>\n";
	echo "</select>\n";
}

function italissima_regio($width="130px") {
	global $regio;
	echo "<select name=\"l\" style=\"width:".$width.";\" onchange=\"formsubmit('l');\">\n";
	if($_GET["themadatum"]==2) {
		echo "<option value=\"0\" selected>Regio</option>\n";
	} else {
		echo "<option value=\"0\" selected>Regio</option>\n";
	}
	reset($regio);
	while(list($key,$value)=each($regio)) {
		echo "<option value=\"".$key."\">".wt_he($value)."</option>\n";
	}
	echo "</select>\n";
}

function italissima_datum($width="130px") {
	if($_GET["themadatum"]==1) {
		echo "<select name=\"t\" style=\"width:".$width.";\" onchange=\"formsubmit('t');\">\n";
		echo "<option value=\"0\" selected>Zoek op thema</option>\n";
		echo "<option value=\"1\">Wellness</option>\n";
		echo "<option value=\"2\">Bij golfbaan</option>\n";
		echo "<option value=\"3\">Bij het water</option>\n";
		echo "<option value=\"4\">Met zwembad</option>\n";
		echo "<option value=\"5\">Op boerderij</option>\n";
		echo "<option value=\"6\">Groepen</option>\n";
		echo "<option value=\"8\">Met kids</option>\n";
		echo "<option value=\"9\">In de bergen</option>\n";
		echo "</select>\n";
	} else {
		echo "<select name=\"t\" id=\"vertrekdatum\" style=\"width:".$width.";\" onchange=\"formsubmit('t');\">\n";
		echo "<option value=\"0\" selected>Datum</option>\n";
		echo "</select>\n";
	}
}

function italissima_personen($width="130px") {
	echo "<select name=\"p\" style=\"width:".$width.";\" onchange=\"formsubmit('p');\">\n";
	echo "<option value=\"0\" selected>Personen</option>\n";
	echo "<option value=\"1\">1 persoon</option>\n";
	for($i=2;$i<=40;$i++) {
		echo "<option value=\"".$i."\">".$i." personen</option>\n";
	}
	echo "</select>\n";
}

function zomer_thema($width="130px") {
	if($_GET["themadatum"]==1) {
		echo "<select name=\"t\" style=\"width:".$width.";\" onchange=\"formsubmit('t');\">\n";
		echo "<option value=\"0\" selected>Zoek op thema</option>\n";
		echo "<option value=\"1\">Wellness</option>\n";
		echo "<option value=\"2\">Bij golfbaan</option>\n";
		echo "<option value=\"3\">Bij het water</option>\n";
		echo "<option value=\"4\">Met zwembad</option>\n";
		echo "<option value=\"5\">Op boerderij</option>\n";
		echo "<option value=\"6\">Groepen</option>\n";
		echo "<option value=\"7\">Vakantieparken</option>\n";
		echo "<option value=\"8\">Met kids</option>\n";
		echo "</select>\n";
	} else {
		echo "<select name=\"t\" id=\"vertrekdatum\" style=\"width:".$width.";\" onchange=\"formsubmit('t');\">\n";
		echo "<option value=\"0\" selected>Kies datum</option>\n";
		echo "</select>\n";
	}
}

function winter_land($width="155px") {
	echo "<select name=\"l\" style=\"width:".$width.";\" onchange=\"formsubmit('l');\">\n";
	if($_GET["themadatum"]==2) {
		echo "<option value=\"0\" selected>".html("kies-land", "htmlbanner")."</option>\n";
	} else {
		echo "<option value=\"0\" selected>".html("zoek-op-land", "htmlbanner")."</option>\n";
	}
	echo "<option value=\"1\">".html("frankrijk", "htmlbanner")."</option>\n";
	echo "<option value=\"2\">".html("oostenrijk", "htmlbanner")."</option>\n";
	echo "<option value=\"3\">".html("zwitserland", "htmlbanner")."</option>\n";
	echo "<option value=\"5\">".html("italie", "htmlbanner")."</option>\n";
	echo "</select>\n";
}

function winter_thema($width="155px") {
	if($_GET["themadatum"]==1) {
		echo "<select name=\"t\" style=\"width:".$width.";\" onchange=\"formsubmit('t');\">\n";
		echo "<option value=\"0\" selected>Zoek op thema</option>\n";
		echo "<option value=\"1\">Open haard</option>\n";
		echo "<option value=\"2\">Bij apr&egrave;s-ski</option>\n";
		echo "<option value=\"3\">Catering</option>\n";
		echo "<option value=\"4\">Kids</option>\n";
		echo "<option value=\"5\">Groepen</option>\n";
		echo "<option value=\"6\">Priv&eacute; sauna</option>\n";
		echo "<option value=\"7\">Ski-stations</option>\n";
		echo "</select>\n";
	} else {
		echo "<select name=\"t\" id=\"vertrekdatum\" style=\"width:".$width.";\" onchange=\"formsubmit('t');\">\n";
		echo "<option value=\"0\" selected>".html("kies-datum", "htmlbanner")."</option>\n";
		echo "</select>\n";
	}
}

function winter_personen($width="155px") {

	echo "<select name=\"p\" style=\"width:".$width.";\" onchange=\"formsubmit('p');\">\n";
	if($_GET["themadatum"]==2) {
		echo "<option value=\"0\" selected>".html("kies-aantal-personen", "htmlbanner")."</option>\n";
	} else {
		echo "<option value=\"0\" selected>".html("zoek-op-aantal-personen", "htmlbanner")."</option>\n";
	}
	echo "<option value=\"1\">1 ".html("persoon", "htmlbanner")."</option>\n";
	for($i=2;$i<=20;$i++) {
		echo "<option value=\"".$i."\">".$i." ".html("personen", "htmlbanner")."</option>\n";
	}
	echo "</select>\n";
}


echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<head>";
echo "<title></title>\n";
echo "<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js\" ></script>\n";
if($_GET["themadatum"]==2) {
	echo "<script type=\"text/javascript\" src=\"".$siteurl."pic/banners/htmlbanners/datums.php\" ></script>\n";
}
if($_GET["n"]==2) {
	#echo "<script type=\"text/javascript\" src=\"http://www.cleafs.nl/js/htmlbanner.js\" ></script>\n";
}

echo "<script type=\"text/javascript\">\n";


if($_GET["themadatum"]==1) {
	echo "var themadatum=1;\n";
} else {
	echo "var themadatum=2;\n";
}

$utm="utm_source=".$netwerken[$_GET["n"]]."&utm_medium=banner&utm_campaign=htmlbanner";

if($_GET["n"]==2) {
	# Andere utm Sneeuwhoogte.nl
	$utm="chad=33&utm_source=sneeuwhoogte.nl&utm_medium=sneeuwhoogte.nl&utm_campaign=sneeuwhoogte.nl-htmlbanner";
} elseif($_GET["n"]==3) {
	# Andere utm Snowplaza
	$utm="utm_source=Snowplaza&utm_medium=Snowplaza_htmlbanners&utm_campaign=Snowplaza_htmlbanners";
	if($_GET["t"]==1) {
		$utm.="_728x90";
	} elseif($_GET["t"]==2) {
		$utm.="_468x60";
	} elseif($_GET["t"]==3) {
		$utm.="_250x250";
	} elseif($_GET["t"]==4) {
		$utm.="_234x60";
	} elseif($_GET["t"]==5) {
		$utm.="_120x600";
	} elseif($_GET["t"]==6) {
		$utm.="_300x250";
	} elseif($_GET["t"]==34) {
		$utm.="_300x600";
	} elseif($_GET["t"]==35) {
		$utm.="_970x250";
	}
}elseif($_GET["n"]==4){
    $utm8 = "utm_source=wintersporters.nl&utm_medium=wintersporters.nl&utm_campaign=wintersporters.nl-zillertal-banner";
    $utm9 = "utm_source=wintersporters.nl&utm_medium=wintersporters.nl&utm_campaign=wintersporters.nl-lesmenuires-banner";
    $utm = ($_GET["t"] == 8) ? $utm8 : $utm9;
}elseif($_GET["n"]==5){

    $region_url["zellamsell"]   = "utm_source=zoover.nl&utm_medium=zoover.nl&utm_campaign=zoover.nl-zellamsee-banner";
    $region_url["Kaprun"]       = "utm_source=zoover.nl&utm_medium=zoover.nl&utm_campaign=zoover.nl-kaprun-banner";
    $region_url["lesmenui"]     = "utm_source=zoover.nl&utm_medium=zoover.nl&utm_campaign=zoover.nl-lesmenuires-banner";
    $region_url["ozenoisans"]   = "utm_source=zoover.nl&utm_medium=zoover.nl&utm_campaign=zoover.nl-ozenoisans-banner";
    $region_url["alpedhuez"]    = "utm_source=zoover.nl&utm_medium=zoover.nl&utm_campaign=zoover.nl-alpedhuez-banner";
    $region_url["vallandry"]    = "utm_source=zoover.nl&utm_medium=zoover.nl&utm_campaign=zoover.nl-vallandry-banner";
    $region_url["valthorens"]   = "utm_source=zoover.nl&utm_medium=zoover.nl&utm_campaign=zoover.nl-valthorens-banner";
    $region_url["chatel"]       = "utm_source=zoover.nl&utm_medium=zoover.nl&utm_campaign=zoover.nl-chatel-banner";

    $region_by_utm["zellamsell"]= array("10", "11", "12");
    $region_by_utm["Kaprun"]    = array("13", "14", "15");
    $region_by_utm["lesmenui"]  = array("16", "17", "18");
    $region_by_utm["ozenoisans"]= array("19", "20", "21");
    $region_by_utm["alpedhuez"] = array("22", "23", "24");
    $region_by_utm["vallandry"] = array("25", "26", "27");
    $region_by_utm["valthorens"]= array("28", "29", "30");
    $region_by_utm["chatel"]    = array("31", "32", "33");

    foreach($region_by_utm as $regionName => $region){
        if(in_array($_GET["t"], $region)){
            $utm = $region_url[$regionName];
        }
    }

}

?>

function init() {
	if(themadatum==2) {
		init2();
	}
}

function formsubmit(type) {
	var ie = (function() {
		var undef,
		v = 3,
		div = document.createElement('div'),
		all = div.getElementsByTagName('i');
		while (
		div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
		all[0]
		);
		return v > 4 ? v : undef;
	}());
	var url='';
	var flash=false;
	var aantalpersonen=0;
	var gebruik_aantalpersonen=false;

	if(typeof document.frm.p!='undefined') {
		aantalpersonen=document.frm.p.value;
		gebruik_aantalpersonen=true;
	}

	if(themadatum==2) {
		<?php if($_GET["wzt"]==2) { ?>
			url='https://www.zomerhuisje.nl/zoek-en-boek.php?filled=1&fzt=&fsg='+document.frm.l.value+'-0&fap='+aantalpersonen+'&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==3) { ?>
			url='https://www.italissima.nl/zoek-en-boek.php?filled=1&fzt=&fsg=5-'+document.frm.l.value+'&fap='+aantalpersonen+'&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==4) { ?>
			url='https://www.chalet.be/zoek-en-boek.php?filled=1&fzt=&fsg='+document.frm.l.value+'-0&fap='+aantalpersonen+'&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==6) { ?>
			url='https://www.italissima.be/zoek-en-boek.php?filled=1&fzt=&fsg=5-'+document.frm.l.value+'&fap='+aantalpersonen+'&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==9) { ?>
			url='https://www.chalet.eu/search-and-book.php?filled=1&fzt=&fsg='+document.frm.l.value+'-0&fap='+aantalpersonen+'&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php } else { ?>
			url='https://www.chalet.nl/zoek-en-boek.php?filled=1&fzt=&fsg='+document.frm.l.value+'-0&fap='+aantalpersonen+'&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php } ?>
	} else {
		if(document.frm.l.value>0) {
		<?php if($_GET["wzt"]==2) { ?>
			if(document.frm.l.value==1) url='https://www.zomerhuisje.nl/land/Frankrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==2) url='https://www.zomerhuisje.nl/land/Oostenrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==3) url='https://www.zomerhuisje.nl/land/Zwitserland/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==5) url='https://www.zomerhuisje.nl/land/Italie/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==6) url='https://www.zomerhuisje.nl/land/Duitsland/?<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==3) {
			while(list($key,$value)=each($regio)) {
				echo "if(document.frm.l.value==".$key.") url='https://www.italissima.nl/vakantiehuizen/regio/".wt_convert2url_seo($value)."/?".$extra_qs.$utm."';\n";
			}
		?>
		<?php } elseif($_GET["wzt"]==4) { ?>
			if(document.frm.l.value==1) url='https://www.chalet.be/land/Frankrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==2) url='https://www.chalet.be/land/Oostenrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==3) url='https://www.chalet.be/land/Zwitserland/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==5) url='https://www.chalet.be/land/Italie/?<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==9) { ?>
			if(document.frm.l.value==1) url='https://www.chalet.eu/country/France/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==2) url='https://www.chalet.eu/country/Austria/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==3) url='https://www.chalet.eu/country/Switzerland/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==5) url='https://www.chalet.eu/country/Italy/?<?php echo $extra_qs.$utm; ?>';
		<?php } else { ?>
			if(document.frm.l.value==1) url='https://www.chalet.nl/land/Frankrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==2) url='https://www.chalet.nl/land/Oostenrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==3) url='https://www.chalet.nl/land/Zwitserland/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==5) url='https://www.chalet.nl/land/Italie/?<?php echo $extra_qs.$utm; ?>';
		<?php } ?>
		} else if(document.frm.t.value>0) {
		<?php if($_GET["wzt"]==2) { ?>
			if(document.frm.t.value==1) url='https://www.zomerhuisje.nl/thema/vleugje-wellness/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==2) url='https://www.zomerhuisje.nl/thema/golf/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==3) url='https://www.zomerhuisje.nl/thema/water/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==4) url='https://www.zomerhuisje.nl/thema/zwembad/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==5) url='https://www.zomerhuisje.nl/thema/tegastopdeboerderij/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==6) url='https://www.zomerhuisje.nl/thema/met-zn-allen/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==7) url='https://www.zomerhuisje.nl/thema/vakantieparken/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==8) url='https://www.zomerhuisje.nl/thema/kids/?<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==4) { ?>
			if(document.frm.t.value==1) url='https://www.chalet.be/thema/open-haard/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==2) url='https://www.chalet.be/thema/apres-ski/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==3) url='https://www.chalet.be/thema/catering/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==4) url='https://www.chalet.be/thema/kids/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==5) url='https://www.chalet.be/thema/groepen/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==6) url='https://www.chalet.be/thema/sauna/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==7) url='https://www.chalet.be/thema/super-ski-stations/?<?php echo $extra_qs.$utm; ?>';
		<?php } else { ?>
			if(document.frm.t.value==1) url='https://www.chalet.nl/thema/open-haard/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==2) url='https://www.chalet.nl/thema/apres-ski/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==3) url='https://www.chalet.nl/thema/catering/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==4) url='https://www.chalet.nl/thema/kids/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==5) url='https://www.chalet.nl/thema/groepen/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==6) url='https://www.chalet.nl/thema/sauna/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==7) url='https://www.chalet.nl/thema/super-ski-stations/?<?php echo $extra_qs.$utm; ?>';
		<?php } ?>
		} else {
		<?php if($_GET["wzt"]==2) { ?>
			url='https://www.zomerhuisje.nl/?<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==3) { ?>
			url='https://www.italissima.nl/?<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==4) { ?>
			url='https://www.chalet.be/?<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==9) { ?>
			url='https://www.chalet.eu/?<?php echo $extra_qs.$utm; ?>';
		<?php }else { ?>
			url='https://www.chalet.nl/?<?php echo $extra_qs.$utm; ?>';
		<?php } ?>
		}
	}
	if(ie==='undefined') {
		window.open(url,"_blank");
		document.frm.l.value=0;
		document.frm.t.value=0;
		if(gebruik_aantalpersonen) {
			document.frm.p.value=0;
		}
	} else {
		if(type=='s') {
			window.open(url);
			document.frm.l.value=0;
			document.frm.t.value=0;
			document.frm.l.disabled=false;
			document.frm.t.disabled=false;
			if(gebruik_aantalpersonen) {
				document.frm.p.value=0;
				document.frm.p.disabled=false;
			}

			if(document.getElementById('submit').src.indexOf("_flash.gif")) {
				document.getElementById('submit').src=document.getElementById('submit').src.replace("_flash.gif",".gif");
			}
		} else {
			if(themadatum==1) {
				if(type=='l') {
					if(document.frm.l.value==0) {
						document.frm.t.disabled=false;
					} else {
						document.frm.t.disabled=true;
						flash=true;
					}
				} else if(type=='t') {
					if(document.frm.t.value==0) {
						document.frm.l.disabled=false;
					} else {
						document.frm.l.disabled=true;
						flash=true;
					}
				}
			} else if(themadatum==2) {
				if(gebruik_aantalpersonen) {
					if(document.frm.l.value>0&&document.frm.t.value>0&&document.frm.p.value>0) {
						flash=true;
					} else {
						flash=false;
					}
				} else {
					if(document.frm.l.value>0&&document.frm.t.value>0) {
						flash=true;
					} else {
						flash=false;
					}
				}
			}
			if(flash) {
				if(document.getElementById('submit').src.indexOf("_flash.gif")>0) {

				} else {
					document.getElementById('submit').src=document.getElementById('submit').src.replace(".gif","_flash.gif");
				}

			} else {
				if(document.getElementById('submit').src.indexOf("_flash.gif")) {
					document.getElementById('submit').src=document.getElementById('submit').src.replace("_flash.gif",".gif");
				} else {

				}
			}
		}
	}
}
<?php
echo "</script>\n";
echo "<style>\n"; ?>

.submit2013 {
	-moz-box-shadow: 3px 3px 3px #888;
	-webkit-box-shadow: 3px 3px 3px #888;
	box-shadow: 3px 3px 3px #888;
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=5, Direction=135, Color='#888888')";
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=5, Direction=135, Color='#888888');
}

.submit2013:hover {
	-ms-filter:\"progid:DXImageTransform.Microsoft.Alpha(Opacity=60)\";
	filter: alpha(opacity=60);
	opacity: .6;
}

<?php
echo "</style>";

echo "</head>\n";
echo "<body style=\"margin:0;\" onload=\"init();\">\n";
if($_GET["t"]==1 and $_GET["wzt"]==2) {
	# zomerhuisje 728x90
	echo "<div style=\"position:fixed;width:728px;height:90px;background-image:url('https://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_htmlbanner_728x90.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.zomerhuisje.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:22px;left:165px;\">\n";
	zomer_land();
	echo "</div>";

	echo "<div style=\"position:absolute;top:50px;left:165px;\">\n";
	zomer_thema();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:35px;left:315px;\"><input type=\"image\" src=\"https://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";

} elseif($_GET["t"]==2 and $_GET["wzt"]==2) {
	# zomerhuisje 468x60
	echo "<div style=\"position:fixed;width:468px;height:60px;background-image:url('https://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_htmlbanner_468x60.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.zomerhuisje.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:7px;left:87px;\">\n";
	zomer_land("113px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:32px;left:87px;\">\n";
	zomer_thema("113px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:19px;left:203px;\"><input type=\"image\" src=\"https://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==3 and $_GET["wzt"]==2) {
	# zomerhuisje 250x250
	echo "<div style=\"position:fixed;width:250px;height:250px;background-image:url('https://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_htmlbanner_250x250.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.zomerhuisje.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:118px;left:12px;\">\n";
	zomer_land();
	echo "</div>";

	echo "<div style=\"position:absolute;top:143px;left:12px;\">\n";
	zomer_thema();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:130px;left:153px;\"><input type=\"image\" src=\"https://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==4 and $_GET["wzt"]==2) {
	# zomerhuisje 234x60
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:234px;height:60px;background-image:url('https://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_htmlbanner_234x60.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.zomerhuisje.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:5px;left:92px;\">\n";
	zomer_land("100px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:33px;left:92px;\">\n";
	zomer_thema("100px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:7px;left:199px;\"><input type=\"image\" src=\"https://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_zoekenboek_klein.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==5 and $_GET["wzt"]==2) {
	# zomerhuisje 120x600
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:120px;height:600px;background-image:url('https://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_htmlbanner_120x600.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.zomerhuisje.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:110px;left:3px;\">\n";
	zomer_land("110px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:139px;left:3px;\">\n";
	zomer_thema("110px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:170px;left:16px;\"><input type=\"image\" src=\"https://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==6 and $_GET["wzt"]==2) {
	# zomerhuisje 300x250
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:300px;height:250px;background-image:url('https://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_htmlbanner_300x250.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.zomerhuisje.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:118px;left:29px;\">\n";
	zomer_land();
	echo "</div>";

	echo "<div style=\"position:absolute;top:141px;left:29px;\">\n";
	zomer_thema();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:131px;left:180px;\"><input type=\"image\" src=\"https://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==1 and $_GET["wzt"]==3) {
	# italissima 728x90
	echo "<div style=\"position:fixed;width:728px;height:90px;background-image:url('https://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_728x90.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:22px;left:320px;\">\n";
	italissima_regio();
	echo "</div>";

	echo "<div style=\"position:absolute;top:50px;left:320px;\">\n";
	italissima_datum();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:35px;left:459px;\"><input type=\"image\" src=\"https://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==2 and $_GET["wzt"]==3) {
	# italissima 468x60
	echo "<div style=\"position:fixed;width:468px;height:60px;background-image:url('https://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_468x60.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:7px;left:230px;\">\n";
	italissima_regio();
	echo "</div>";

	echo "<div style=\"position:absolute;top:32px;left:230px;\">\n";
	italissima_datum();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:19px;left:372px;\"><input type=\"image\" src=\"https://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==3 and $_GET["wzt"]==3) {
	# italissima 250x250
	echo "<div style=\"position:fixed;width:250px;height:250px;background-image:url('https://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_250x250.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:133px;left:12px;\">\n";
	italissima_regio();
	echo "</div>";

	echo "<div style=\"position:absolute;top:169px;left:12px;\">\n";
	italissima_datum();
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:205px;left:12px;\">\n";
	italissima_personen();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:169px;left:153px;\"><input type=\"image\" src=\"https://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==4 and $_GET["wzt"]==3) {
	# italissima 234x60
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:234px;height:60px;background-image:url('https://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_234x60.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:5px;left:92px;\">\n";
	italissima_regio("100px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:33px;left:92px;\">\n";
	italissima_datum("100px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:7px;left:199px;\"><input type=\"image\" src=\"https://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek_klein.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==5 and $_GET["wzt"]==3) {
	# italissima 120x600
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:120px;height:600px;background-image:url('https://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_120x600.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:275px;left:3px;\">\n";
	italissima_regio("110px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:310px;left:3px;\">\n";
	italissima_datum("110px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:345px;left:3px;\">\n";
	italissima_personen("110px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:390px;left:16px;\"><input type=\"image\" src=\"https://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";

} elseif(in_array($_GET["t"], $banner_size["728x90"]) and ($_GET["wzt"]==1 or $_GET["wzt"]==4 or $_GET["wzt"]==8) and $_GET["themadatum"]==2) {
	# chalet 728x90 land-datum-aantal personen
	echo "<div style=\"position:fixed;width:728px;height:90px;background-image:url('".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==4 ? "be" : "")."_htmlbanner_2013_728x90.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"".$siteurl."\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:7px;left:475px;\">\n";
	winter_land();
	echo "</div>";

	echo "<div style=\"position:absolute;top:33px;left:475px;\">\n";
	winter_thema();
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:60px;left:475px;\">\n";
	winter_personen();
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:23px;left:640px;\"><input type=\"image\" src=\"".$siteurl."pic/banners/htmlbanners/chalet_zoekenboek_2013.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" class=\"submit2013\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";

} elseif(in_array($_GET["t"], $banner_size["300x600"]) and ($_GET["wzt"]==1 or $_GET["wzt"]==4 or $_GET["wzt"]==9) and $_GET["themadatum"]==2) {
		# chalet 300x600 land-datum-aantal personen
		echo "<div style=\"position:fixed;width:300px;height:600px;background-image:url('".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==4 ? "be" : "").($_GET["wzt"]==9 ? "eu" : "")."_htmlbanner_300x600.jpg?c=1');\">\n";
		echo "<form name=\"frm\" method=\"get\" action=\"".$siteurl."\" target=\"_blank\">\n";
		echo "<div style=\"position:absolute;top:504px;left:11px;\">\n";
		winter_land("158px");
		echo "</div>";

		echo "<div style=\"position:absolute;top:534px;left:11px;\">\n";
		winter_thema("158px");
		echo "</div>\n";

		echo "<div style=\"position:absolute;top:564px;left:11px;\">\n";
		winter_personen("158px");
		echo "</div>\n";

		echo "<div style=\"position:absolute;top:504px;left:180px;\"><input type=\"image\" src=\"".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==9 ? "en" : "")."_zoekenboek_113x81.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" class=\"submit2013\" /></div>\n";
		echo "</form>\n";
		echo "</div>\n";

} elseif(in_array($_GET["t"], $banner_size["970x250"]) and ($_GET["wzt"]==1 or $_GET["wzt"]==4 or $_GET["wzt"]==9) and $_GET["themadatum"]==2) {
		# chalet 970x250 land-datum-aantal personen
		echo "<div style=\"position:fixed;width:970px;height:250px;background-image:url('".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==4 ? "be" : "").($_GET["wzt"]==9 ? "eu" : "")."_htmlbanner_970x250.jpg?c=1');\">\n";
		echo "<form name=\"frm\" method=\"get\" action=\"".$siteurl."\" target=\"_blank\">\n";
		echo "<div style=\"position:absolute;top:38px;left:782px;\">\n";
		winter_land("158px");
		echo "</div>";

		echo "<div style=\"position:absolute;top:85px;left:782px;\">\n";
		winter_thema("158px");
		echo "</div>\n";

		echo "<div style=\"position:absolute;top:130px;left:782px;\">\n";
		winter_personen("158px");
		echo "</div>\n";

		echo "<div style=\"position:absolute;top:178px;left:782px;\"><input type=\"image\" src=\"".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==9 ? "en" : "")."_zoekenboek_158x39.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" class=\"submit2013\" /></div>\n";
		echo "</form>\n";
		echo "</div>\n";
} elseif($_GET["t"]==1 and ($_GET["wzt"]==1 or $_GET["wzt"]==4)) {
	# chalet 728x90 land-thema
	echo "<div style=\"position:fixed;width:728px;height:90px;background-image:url('".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==4 ? "be" : "")."_htmlbanner_2013_728x90.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"".$siteurl."\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:22px;left:475px;\">\n";
	winter_land();
	echo "</div>";

	echo "<div style=\"position:absolute;top:50px;left:475px;\">\n";
	winter_thema();
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:26px;left:640px;\"><input type=\"image\" src=\"".$siteurl."pic/banners/htmlbanners/chalet_zoekenboek_2013.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" class=\"submit2013\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";

} elseif($_GET["t"]==2 and ($_GET["wzt"]==1 or $_GET["wzt"]==4) and $_GET["themadatum"]==2) {
	# chalet 468x60 land-datum-aantal personen
	echo "<div style=\"position:fixed;width:468px;height:60px;background-image:url('".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==4 ? "be" : "")."_htmlbanner_2013_468x60.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"".$siteurl."\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:7px;left:280px;\">\n";
	winter_land("110px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:32px;left:280px;\">\n";
	winter_thema("110px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:14px;left:398px;\"><input type=\"image\" src=\"".$siteurl."pic/banners/htmlbanners/chalet_zoekenboek_klein_2013.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" class=\"submit2013\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==2 and ($_GET["wzt"]==1 or $_GET["wzt"]==4)) {
	# chalet 468x60 land-thema
	echo "<div style=\"position:fixed;width:468px;height:60px;background-image:url('".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==4 ? "be" : "")."_htmlbanner_468x60.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"".$siteurl."\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:7px;left:95px;\">\n";
	winter_land();
	echo "</div>";

	echo "<div style=\"position:absolute;top:32px;left:95px;\">\n";
	winter_thema();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:19px;left:263px;\"><input type=\"image\" src=\"".$siteurl."pic/banners/htmlbanners/chalet_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==3 and ($_GET["wzt"]==1 or $_GET["wzt"]==4)) {
	# chalet 250x250
	echo "<div style=\"position:fixed;width:250px;height:250px;background-image:url('".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==4 ? "be" : "")."_htmlbanner_2013_250x250.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"".$siteurl."\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:199px;left:9px;\">\n";
	winter_land("130px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:224px;left:9px;\">\n";
	winter_thema("130px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:200px;left:150px;\"><input type=\"image\" src=\"".$siteurl."pic/banners/htmlbanners/chalet_zoekenboek_2013.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" class=\"submit2013\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==4 and ($_GET["wzt"]==1 or $_GET["wzt"]==4)) {
	# chalet 234x60
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:234px;height:60px;background-image:url('".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==4 ? "be" : "")."_htmlbanner_234x60.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"".$siteurl."\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:5px;left:80px;\">\n";
	winter_land("100px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:33px;left:80px;\">\n";
	winter_thema("100px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:7px;left:193px;\"><input type=\"image\" src=\"".$siteurl."pic/banners/htmlbanners/chalet_zoekenboek_klein.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";

} elseif(in_array($_GET["t"], $banner_size["120x600"]) and ($_GET["wzt"]==1 or $_GET["wzt"]==4 or $_GET["wzt"]==8) and $_GET["themadatum"]==2) {
	# chalet 120x600
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:120px;height:600px;background-image:url('".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==4 ? "be" : "")."_htmlbanner_2013_120x600.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"".$siteurl."\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:355px;left:3px;\">\n";
	winter_land("110px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:393px;left:3px;\">\n";
	winter_thema("110px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:429px;left:3px;\">\n";
	winter_personen("110px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:490px;left:16px;\"><input type=\"image\" src=\"".$siteurl."pic/banners/htmlbanners/chalet_zoekenboek_2013.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" class=\"submit2013\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";

} elseif($_GET["t"]==5 and ($_GET["wzt"]==1 or $_GET["wzt"]==4)) {
	# chalet 120x600
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:120px;height:600px;background-image:url('".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==4 ? "be" : "")."_htmlbanner_2013_120x600.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"".$siteurl."\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:385px;left:3px;\">\n";
	winter_land("110px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:429px;left:3px;\">\n";
	winter_thema("110px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:490px;left:16px;\"><input type=\"image\" src=\"".$siteurl."pic/banners/htmlbanners/chalet_zoekenboek_2013.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" class=\"submit2013\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif(in_array($_GET["t"], $banner_size["300x250"]) and ($_GET["wzt"]==1 or $_GET["wzt"]==4 or $_GET["wzt"]==8)) {
	# chalet 300x250
	echo "<div style=\"position:fixed;width:300px;height:250px;background-image:url('".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==4 ? "be" : "")."_htmlbanner_2013_300x250.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"".$siteurl."\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:199px;left:10px;\">\n";
	winter_land("120px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:223px;left:10px;\">\n";
	winter_thema("120px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:200px;left:153px;\"><input type=\"image\" src=\"".$siteurl."pic/banners/htmlbanners/chalet_zoekenboek_2013.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" class=\"submit2013\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==7 and ($_GET["wzt"]==1 or $_GET["wzt"]==4) and $_GET["themadatum"]==2) {
	# chalet 120x450
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:120px;height:450px;background-image:url('".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==4 ? "be" : "")."_htmlbanner_2013_120x450.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"".$siteurl."\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:285px;left:3px;\">\n";
	winter_land("110px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:315px;left:3px;\">\n";
	winter_thema("110px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:345px;left:3px;\">\n";
	winter_personen("110px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:390px;left:16px;\"><input type=\"image\" src=\"".$siteurl."pic/banners/htmlbanners/chalet_zoekenboek_2013.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" class=\"submit2013\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif(($_GET["t"]==8 or $_GET["t"]==9) and ($_GET["wzt"]==1 or $_GET["wzt"]==7) and $_GET["themadatum"]==2) {
	# chalet 336x280
	echo "<style>\nselect {\nfont-size:10pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:336px;height:280px;background-image:url('".$siteurl."pic/banners/htmlbanners/chalet".($_GET["wzt"]==4 ? "be" : "")."_htmlbanner_336x280.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"".$siteurl."\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:190px;left:10px;\">\n";
	winter_land("150px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:220px;left:10px;\">\n";
	winter_thema("150px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:250px;left:10px;\">\n";
	winter_personen("150px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:223px;left:185px;\"><input type=\"image\" src=\"".$siteurl."pic/banners/htmlbanners/chalet_zoekenboek_336x280.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" class=\"submit2013\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==1 and $_GET["wzt"]==6) {
	# italissima.be 728x90
	echo "<div style=\"position:fixed;width:728px;height:90px;background-image:url('https://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_728x90.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:22px;left:320px;\">\n";
	italissima_regio();
	echo "</div>";

	echo "<div style=\"position:absolute;top:50px;left:320px;\">\n";
	italissima_datum();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:35px;left:459px;\"><input type=\"image\" src=\"https://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==2 and $_GET["wzt"]==6) {
	# italissima.be 468x60
	echo "<div style=\"position:fixed;width:468px;height:60px;background-image:url('https://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_468x60.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:7px;left:230px;\">\n";
	italissima_regio();
	echo "</div>";

	echo "<div style=\"position:absolute;top:32px;left:230px;\">\n";
	italissima_datum();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:19px;left:372px;\"><input type=\"image\" src=\"https://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==3 and $_GET["wzt"]==6) {
	# italissima.be 250x250
	echo "<div style=\"position:fixed;width:250px;height:250px;background-image:url('https://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_250x250.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:133px;left:12px;\">\n";
	italissima_regio();
	echo "</div>";

	echo "<div style=\"position:absolute;top:169px;left:12px;\">\n";
	italissima_datum();
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:205px;left:12px;\">\n";
	italissima_personen();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:169px;left:153px;\"><input type=\"image\" src=\"https://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==4 and $_GET["wzt"]==6) {
	# italissima.be 234x60
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:234px;height:60px;background-image:url('https://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_234x60.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:5px;left:92px;\">\n";
	italissima_regio("100px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:33px;left:92px;\">\n";
	italissima_datum("100px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:7px;left:199px;\"><input type=\"image\" src=\"https://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek_klein.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==5 and $_GET["wzt"]==6) {
	# italissima.be 120x600
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:120px;height:600px;background-image:url('https://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_120x600.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"https://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:275px;left:3px;\">\n";
	italissima_regio("110px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:310px;left:3px;\">\n";
	italissima_datum("110px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:345px;left:3px;\">\n";
	italissima_personen("110px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:390px;left:16px;\"><input type=\"image\" src=\"https://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";

}
echo "</body>\n";
echo "</html>\n";

?>