<?php

$geen_tracker_cookie=true;

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
	$siteurl="http://www.italissima.nl/";

	$db->query("SELECT DISTINCT skigebied_id, skigebied FROM view_accommodatie WHERE websites LIKE '%I%' AND atonen=1 AND ttonen=1 ORDER BY skigebied;");
	while($db->next_record()) {
		$regio[$db->f("skigebied_id")]=$db->f("skigebied");
	}
	if($_GET["t"]==3 or $_GET["t"]==5) {
		$aantalpersonen=true;
	}
} elseif($_GET["wzt"]==4) {
		$site="chalet";
		$siteurl="http://www.chalet.be/";
} elseif($_GET["wzt"]==5) {
		$site="superski";
		$siteurl="http://www.superski.nl/";
} elseif($_GET["wzt"]==6) {
	$site="italissima";
	$siteurl="http://www.italissima.be/";

	$db->query("SELECT DISTINCT skigebied_id, skigebied FROM view_accommodatie WHERE websites LIKE '%I%' AND atonen=1 AND ttonen=1 ORDER BY skigebied;");
	while($db->next_record()) {
		$regio[$db->f("skigebied_id")]=$db->f("skigebied");
	}
	if($_GET["t"]==3 or $_GET["t"]==5) {
		$aantalpersonen=true;
	}
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
		echo "<option value=\"0\" selected>Kies land</option>\n";
	} else {
		echo "<option value=\"0\" selected>Zoek op land</option>\n";
	}
	echo "<option value=\"1\">Frankrijk</option>\n";
	echo "<option value=\"2\">Oostenrijk</option>\n";
	echo "<option value=\"3\">Zwitserland</option>\n";
	echo "<option value=\"5\">Itali&euml;</option>\n";
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
		echo "<option value=\"0\" selected>Kies datum</option>\n";
		echo "</select>\n";
	}
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
	if(themadatum==2) {
		<?php if($_GET["wzt"]==2) { ?>
			url='http://www.zomerhuisje.nl/zoek-en-boek.php?filled=1&fzt=&fsg='+document.frm.l.value+'-0&fap=0&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==3 and $aantalpersonen) { ?>
			url='http://www.italissima.nl/zoek-en-boek.php?filled=1&fzt=&fsg=5-'+document.frm.l.value+'&fap='+document.frm.p.value+'&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==3) { ?>
			url='http://www.italissima.nl/zoek-en-boek.php?filled=1&fzt=&fsg=5-'+document.frm.l.value+'&fap=0&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==4) { ?>
			url='http://www.chalet.be/zoek-en-boek.php?filled=1&fzt=&fsg='+document.frm.l.value+'-0&fap=0&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==5) { ?>
			url='http://www.superski.nl/zoek-en-boek.php?filled=1&fzt=&fsg='+document.frm.l.value+'-0&fap=0&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==6 and $aantalpersonen) { ?>
			url='http://www.italissima.be/zoek-en-boek.php?filled=1&fzt=&fsg=5-'+document.frm.l.value+'&fap='+document.frm.p.value+'&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==6) { ?>
			url='http://www.italissima.be/zoek-en-boek.php?filled=1&fzt=&fsg=5-'+document.frm.l.value+'&fap=0&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php }else { ?>
			url='http://www.chalet.nl/zoek-en-boek.php?filled=1&fzt=&fsg='+document.frm.l.value+'-0&fap=0&fas=0&fad='+document.frm.t.value+'&<?php echo $extra_qs.$utm; ?>';
		<?php } ?>
	} else {
		if(document.frm.l.value>0) {
		<?php if($_GET["wzt"]==2) { ?>
			if(document.frm.l.value==1) url='http://www.zomerhuisje.nl/land/Frankrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==2) url='http://www.zomerhuisje.nl/land/Oostenrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==3) url='http://www.zomerhuisje.nl/land/Zwitserland/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==5) url='http://www.zomerhuisje.nl/land/Italie/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==6) url='http://www.zomerhuisje.nl/land/Duitsland/?<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==3) {
			while(list($key,$value)=each($regio)) {
				echo "if(document.frm.l.value==".$key.") url='http://www.italissima.nl/vakantiehuizen/regio/".wt_convert2url_seo($value)."/?".$extra_qs.$utm."';\n";
			}
		?>
		<?php } elseif($_GET["wzt"]==4) { ?>
			if(document.frm.l.value==1) url='http://www.chalet.be/land/Frankrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==2) url='http://www.chalet.be/land/Oostenrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==3) url='http://www.chalet.be/land/Zwitserland/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==5) url='http://www.chalet.be/land/Italie/?<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==5) { ?>
			if(document.frm.l.value==1) url='http://www.superski.nl/land/Frankrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==2) url='http://www.superski.nl/land/Oostenrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==3) url='http://www.superski.nl/land/Zwitserland/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==5) url='http://www.superski.nl/land/Italie/?<?php echo $extra_qs.$utm; ?>';
		<?php } else { ?>
			if(document.frm.l.value==1) url='http://www.chalet.nl/land/Frankrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==2) url='http://www.chalet.nl/land/Oostenrijk/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==3) url='http://www.chalet.nl/land/Zwitserland/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.l.value==5) url='http://www.chalet.nl/land/Italie/?<?php echo $extra_qs.$utm; ?>';
		<?php } ?>
		} else if(document.frm.t.value>0) {
		<?php if($_GET["wzt"]==2) { ?>
			if(document.frm.t.value==1) url='http://www.zomerhuisje.nl/thema/vleugje-wellness/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==2) url='http://www.zomerhuisje.nl/thema/golf/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==3) url='http://www.zomerhuisje.nl/thema/water/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==4) url='http://www.zomerhuisje.nl/thema/zwembad/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==5) url='http://www.zomerhuisje.nl/thema/tegastopdeboerderij/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==6) url='http://www.zomerhuisje.nl/thema/met-zn-allen/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==7) url='http://www.zomerhuisje.nl/thema/vakantieparken/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==8) url='http://www.zomerhuisje.nl/thema/kids/?<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==4) { ?>
			if(document.frm.t.value==1) url='http://www.chalet.be/thema/open-haard/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==2) url='http://www.chalet.be/thema/apres-ski/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==3) url='http://www.chalet.be/thema/catering/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==4) url='http://www.chalet.be/thema/kids/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==5) url='http://www.chalet.be/thema/groepen/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==6) url='http://www.chalet.be/thema/sauna/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==7) url='http://www.chalet.be/thema/super-ski-stations/?<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==5) { ?>
			if(document.frm.t.value==1) url='http://www.superski.nl/thema/open-haard/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==2) url='http://www.superski.nl/thema/apres-ski/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==3) url='http://www.superski.nl/thema/catering/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==4) url='http://www.superski.nl/thema/kids/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==5) url='http://www.superski.nl/thema/groepen/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==6) url='http://www.superski.nl/thema/sauna/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==7) url='http://www.superski.nl/thema/super-ski-stations/?<?php echo $extra_qs.$utm; ?>';
		<?php } else { ?>
			if(document.frm.t.value==1) url='http://www.chalet.nl/thema/open-haard/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==2) url='http://www.chalet.nl/thema/apres-ski/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==3) url='http://www.chalet.nl/thema/catering/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==4) url='http://www.chalet.nl/thema/kids/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==5) url='http://www.chalet.nl/thema/groepen/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==6) url='http://www.chalet.nl/thema/sauna/?<?php echo $extra_qs.$utm; ?>';
			if(document.frm.t.value==7) url='http://www.chalet.nl/thema/super-ski-stations/?<?php echo $extra_qs.$utm; ?>';
		<?php } ?>
		} else {
		<?php if($_GET["wzt"]==2) { ?>
			url='http://www.zomerhuisje.nl/?<?php echo $extra_qs.$utm; ?>';
		<?php } elseif($_GET["wzt"]==3) { ?>
			url='http://www.italissima.nl/?<?php echo $extra_qs.$utm; ?>';
				<?php } elseif($_GET["wzt"]==4) { ?>
			url='http://www.chalet.be/?<?php echo $extra_qs.$utm; ?>';
		<?php }else { ?>
			url='http://www.chalet.nl/?<?php echo $extra_qs.$utm; ?>';
		<?php } ?>
		}
	}
	if(ie==='undefined') {
		window.open(url,"_blank");
		document.frm.l.value=0;
		document.frm.t.value=0;
		<?php if($aantalpersonen) { ?>
		document.frm.p.value=0;
		<?php } ?>
	} else {
		if(type=='s') {
			window.open(url);
			document.frm.l.value=0;
			document.frm.t.value=0;
			document.frm.l.disabled=false;
			document.frm.t.disabled=false;
			<?php if($aantalpersonen) { ?>
			document.frm.p.value=0;
			document.frm.p.disabled=false;
			<?php } ?>
			if(document.getElementById('submit').src=="http://www.<?php echo $site; ?>.nl/pic/banners/htmlbanners/<?php echo $site; ?>_zoekenboek_<?php echo $buttontoevoeging; ?>flash.gif") {
				document.getElementById('submit').src="http://www.<?php echo $site; ?>.nl/pic/banners/htmlbanners/<?php echo $site; ?>_zoekenboek<?php echo $buttontoevoeging; ?>.gif";
			}
			else if(document.getElementById('submit').src=="http://www.<?php echo $site; ?>.be/pic/banners/htmlbanners/<?php echo $site; ?>_zoekenboek_<?php echo $buttontoevoeging; ?>flash.gif"){
				document.getElementById('submit').src="http://www.<?php echo $site; ?>.be/pic/banners/htmlbanners/<?php echo $site; ?>_zoekenboek<?php echo $buttontoevoeging; ?>.gif";
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
			<?php if($aantalpersonen) { ?>
				if(document.frm.l.value>0&&document.frm.t.value>0&&document.frm.p.value>0) {
			<?php } else { ?>
				if(document.frm.l.value>0&&document.frm.t.value>0) {
			<?php } ?>
					flash=true;
				} else {
					flash=false;
				}
			}
			if(flash) {
				if(document.getElementById('submit').src=="http://www.<?php echo $site; ?>.nl/pic/banners/htmlbanners/<?php echo $site; ?>_zoekenboek<?php echo $buttontoevoeging; ?>.gif") {
					document.getElementById('submit').src="http://www.<?php echo $site; ?>.nl/pic/banners/htmlbanners/<?php echo $site; ?>_zoekenboek<?php echo $buttontoevoeging; ?>_flash.gif";
				}
				else if(document.getElementById('submit').src=="http://www.<?php echo $site; ?>.be/pic/banners/htmlbanners/<?php echo $site; ?>_zoekenboek<?php echo $buttontoevoeging; ?>.gif") {
					document.getElementById('submit').src="http://www.<?php echo $site; ?>.be/pic/banners/htmlbanners/<?php echo $site; ?>_zoekenboek<?php echo $buttontoevoeging; ?>_flash.gif";
				}
			} else {
				if(document.getElementById('submit').src=="http://www.<?php echo $site; ?>.nl/pic/banners/htmlbanners/<?php echo $site; ?>_zoekenboek<?php echo $buttontoevoeging; ?>_flash.gif") {
					document.getElementById('submit').src="http://www.<?php echo $site; ?>.nl/pic/banners/htmlbanners/<?php echo $site; ?>_zoekenboek<?php echo $buttontoevoeging; ?>.gif";
				}
				else if(document.getElementById('submit').src=="http://www.<?php echo $site; ?>.be/pic/banners/htmlbanners/<?php echo $site; ?>_zoekenboek<?php echo $buttontoevoeging; ?>_flash.gif") {
					document.getElementById('submit').src="http://www.<?php echo $site; ?>.be/pic/banners/htmlbanners/<?php echo $site; ?>_zoekenboek<?php echo $buttontoevoeging; ?>.gif";
				}
			}
		}
	}
}
<?php
echo "</script>\n";
echo "</head>\n";
echo "<body style=\"margin:0;\" onload=\"init();\">\n";
if($_GET["t"]==1 and $_GET["wzt"]==2) {
	# zomerhuisje 728x90
	echo "<div style=\"position:fixed;width:728px;height:90px;background-image:url('http://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_htmlbanner_728x90.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.zomerhuisje.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:22px;left:165px;\">\n";
	zomer_land();
	echo "</div>";

	echo "<div style=\"position:absolute;top:50px;left:165px;\">\n";
	zomer_thema();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:35px;left:315px;\"><input type=\"image\" src=\"http://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";

} elseif($_GET["t"]==2 and $_GET["wzt"]==2) {
	# zomerhuisje 468x60
	echo "<div style=\"position:fixed;width:468px;height:60px;background-image:url('http://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_htmlbanner_468x60.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.zomerhuisje.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:7px;left:87px;\">\n";
	zomer_land("113px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:32px;left:87px;\">\n";
	zomer_thema("113px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:19px;left:203px;\"><input type=\"image\" src=\"http://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==3 and $_GET["wzt"]==2) {
	# zomerhuisje 250x250
	echo "<div style=\"position:fixed;width:250px;height:250px;background-image:url('http://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_htmlbanner_250x250.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.zomerhuisje.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:118px;left:12px;\">\n";
	zomer_land();
	echo "</div>";

	echo "<div style=\"position:absolute;top:143px;left:12px;\">\n";
	zomer_thema();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:130px;left:153px;\"><input type=\"image\" src=\"http://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==4 and $_GET["wzt"]==2) {
	# zomerhuisje 234x60
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:234px;height:60px;background-image:url('http://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_htmlbanner_234x60.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.zomerhuisje.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:5px;left:92px;\">\n";
	zomer_land("100px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:33px;left:92px;\">\n";
	zomer_thema("100px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:7px;left:199px;\"><input type=\"image\" src=\"http://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_zoekenboek_klein.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==5 and $_GET["wzt"]==2) {
	# zomerhuisje 120x600
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:120px;height:600px;background-image:url('http://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_htmlbanner_120x600.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.zomerhuisje.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:110px;left:3px;\">\n";
	zomer_land("110px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:139px;left:3px;\">\n";
	zomer_thema("110px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:170px;left:16px;\"><input type=\"image\" src=\"http://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==6 and $_GET["wzt"]==2) {
	# zomerhuisje 300x250
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:300px;height:250px;background-image:url('http://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_htmlbanner_300x250.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.zomerhuisje.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:118px;left:29px;\">\n";
	zomer_land();
	echo "</div>";

	echo "<div style=\"position:absolute;top:141px;left:29px;\">\n";
	zomer_thema();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:131px;left:180px;\"><input type=\"image\" src=\"http://www.zomerhuisje.nl/pic/banners/htmlbanners/zomerhuisje_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==1 and $_GET["wzt"]==3) {
	# italissima 728x90
	echo "<div style=\"position:fixed;width:728px;height:90px;background-image:url('http://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_728x90.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:22px;left:320px;\">\n";
	italissima_regio();
	echo "</div>";

	echo "<div style=\"position:absolute;top:50px;left:320px;\">\n";
	italissima_datum();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:35px;left:459px;\"><input type=\"image\" src=\"http://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==2 and $_GET["wzt"]==3) {
	# italissima 468x60
	echo "<div style=\"position:fixed;width:468px;height:60px;background-image:url('http://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_468x60.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:7px;left:230px;\">\n";
	italissima_regio();
	echo "</div>";

	echo "<div style=\"position:absolute;top:32px;left:230px;\">\n";
	italissima_datum();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:19px;left:372px;\"><input type=\"image\" src=\"http://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==3 and $_GET["wzt"]==3) {
	# italissima 250x250
	echo "<div style=\"position:fixed;width:250px;height:250px;background-image:url('http://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_250x250.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:133px;left:12px;\">\n";
	italissima_regio();
	echo "</div>";

	echo "<div style=\"position:absolute;top:169px;left:12px;\">\n";
	italissima_datum();
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:205px;left:12px;\">\n";
	italissima_personen();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:169px;left:153px;\"><input type=\"image\" src=\"http://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==4 and $_GET["wzt"]==3) {
	# italissima 234x60
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:234px;height:60px;background-image:url('http://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_234x60.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:5px;left:92px;\">\n";
	italissima_regio("100px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:33px;left:92px;\">\n";
	italissima_datum("100px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:7px;left:199px;\"><input type=\"image\" src=\"http://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek_klein.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==5 and $_GET["wzt"]==3) {
	# italissima 120x600
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:120px;height:600px;background-image:url('http://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_120x600.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:275px;left:3px;\">\n";
	italissima_regio("110px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:310px;left:3px;\">\n";
	italissima_datum("110px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:345px;left:3px;\">\n";
	italissima_personen("110px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:390px;left:16px;\"><input type=\"image\" src=\"http://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";

} elseif($_GET["t"]==1 and $_GET["wzt"]==1) {
	# chalet 728x90
	echo "<div style=\"position:fixed;width:728px;height:90px;background-image:url('http://www.chalet.nl/pic/banners/htmlbanners/chalet_htmlbanner_728x90.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.chalet.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:22px;left:295px;\">\n";
	winter_land();
	echo "</div>";

	echo "<div style=\"position:absolute;top:50px;left:295px;\">\n";
	winter_thema();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:35px;left:460px;\"><input type=\"image\" src=\"http://www.chalet.nl/pic/banners/htmlbanners/chalet_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";

} elseif($_GET["t"]==2 and $_GET["wzt"]==1) {
	# chalet 468x60
	echo "<div style=\"position:fixed;width:468px;height:60px;background-image:url('http://www.chalet.nl/pic/banners/htmlbanners/chalet_htmlbanner_468x60.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.chalet.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:7px;left:95px;\">\n";
	winter_land();
	echo "</div>";

	echo "<div style=\"position:absolute;top:32px;left:95px;\">\n";
	winter_thema();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:19px;left:263px;\"><input type=\"image\" src=\"http://www.chalet.nl/pic/banners/htmlbanners/chalet_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==3 and $_GET["wzt"]==1) {
	# chalet 250x250
	echo "<div style=\"position:fixed;width:250px;height:250px;background-image:url('http://www.chalet.nl/pic/banners/htmlbanners/chalet_htmlbanner_250x250.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.chalet.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:113px;left:20px;\">\n";
	winter_land("120px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:144px;left:20px;\">\n";
	winter_thema("120px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:128px;left:150px;\"><input type=\"image\" src=\"http://www.chalet.nl/pic/banners/htmlbanners/chalet_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==4 and $_GET["wzt"]==1) {
	# chalet 234x60
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:234px;height:60px;background-image:url('http://www.chalet.nl/pic/banners/htmlbanners/chalet_htmlbanner_234x60.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.chalet.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:5px;left:80px;\">\n";
	winter_land("100px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:33px;left:80px;\">\n";
	winter_thema("100px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:7px;left:193px;\"><input type=\"image\" src=\"http://www.chalet.nl/pic/banners/htmlbanners/chalet_zoekenboek_klein.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==5 and $_GET["wzt"]==1) {
	# chalet 120x600
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:120px;height:600px;background-image:url('http://www.chalet.nl/pic/banners/htmlbanners/chalet_htmlbanner_120x600.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.chalet.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:235px;left:3px;\">\n";
	winter_land("110px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:279px;left:3px;\">\n";
	winter_thema("110px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:340px;left:16px;\"><input type=\"image\" src=\"http://www.chalet.nl/pic/banners/htmlbanners/chalet_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==6 and $_GET["wzt"]==1) {
	# chalet 300x250
	echo "<div style=\"position:fixed;width:300px;height:250px;background-image:url('http://www.chalet.nl/pic/banners/htmlbanners/chalet_htmlbanner_300x250.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.chalet.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:113px;left:40px;\">\n";
	winter_land("120px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:144px;left:40px;\">\n";
	winter_thema("120px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:128px;left:170px;\"><input type=\"image\" src=\"http://www.chalet.nl/pic/banners/htmlbanners/chalet_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
}elseif($_GET["t"]==1 and $_GET["wzt"]==4) {
	# chalet.be 728x90
	echo "<div style=\"position:fixed;width:728px;height:90px;background-image:url('http://www.chalet.be/pic/banners/htmlbanners/chaletbe_htmlbanner_728x90.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.chalet.be/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:22px;left:295px;\">\n";
	winter_land();
	echo "</div>";

	echo "<div style=\"position:absolute;top:50px;left:295px;\">\n";
	winter_thema();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:35px;left:460px;\"><input type=\"image\" src=\"http://www.chalet.be/pic/banners/htmlbanners/chalet_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";

}elseif($_GET["t"]==2 and $_GET["wzt"]==4) {
	# chalet.be 468x60
	echo "<div style=\"position:fixed;width:468px;height:60px;background-image:url('http://www.chalet.be/pic/banners/htmlbanners/chaletbe_htmlbanner_468x60.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.chalet.be/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:7px;left:95px;\">\n";
	winter_land();
	echo "</div>";

	echo "<div style=\"position:absolute;top:32px;left:95px;\">\n";
	winter_thema();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:19px;left:263px;\"><input type=\"image\" src=\"http://www.chalet.be/pic/banners/htmlbanners/chalet_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==3 and $_GET["wzt"]==4) {
	# chalet.be 250x250
	echo "<div style=\"position:fixed;width:250px;height:250px;background-image:url('http://www.chalet.be/pic/banners/htmlbanners/chaletbe_htmlbanner_250x250.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.chalet.be/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:113px;left:20px;\">\n";
	winter_land("120px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:144px;left:20px;\">\n";
	winter_thema("120px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:128px;left:150px;\"><input type=\"image\" src=\"http://www.chalet.be/pic/banners/htmlbanners/chalet_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==4 and $_GET["wzt"]==4) {
	# chalet.be 234x60
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:234px;height:60px;background-image:url('http://www.chalet.be/pic/banners/htmlbanners/chaletbe_htmlbanner_234x60.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.chalet.be/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:5px;left:80px;\">\n";
	winter_land("100px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:33px;left:80px;\">\n";
	winter_thema("100px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:7px;left:193px;\"><input type=\"image\" src=\"http://www.chalet.be/pic/banners/htmlbanners/chalet_zoekenboek_klein.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==5 and $_GET["wzt"]==4) {
	# chalet.be 120x600
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:120px;height:600px;background-image:url('http://www.chalet.be/pic/banners/htmlbanners/chaletbe_htmlbanner_120x600.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.chalet.be/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:235px;left:3px;\">\n";
	winter_land("110px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:279px;left:3px;\">\n";
	winter_thema("110px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:340px;left:16px;\"><input type=\"image\" src=\"http://www.chalet.be/pic/banners/htmlbanners/chalet_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==6 and $_GET["wzt"]==4) {
	# chalet.be 300x250
	echo "<div style=\"position:fixed;width:300px;height:250px;background-image:url('http://www.chalet.be/pic/banners/htmlbanners/chaletbe_htmlbanner_300x250.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.chalet.be/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:113px;left:40px;\">\n";
	winter_land("120px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:144px;left:40px;\">\n";
	winter_thema("120px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:128px;left:170px;\"><input type=\"image\" src=\"http://www.chalet.be/pic/banners/htmlbanners/chalet_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==1 and $_GET["wzt"]==5) {
	# superski 728x90
	echo "<div style=\"position:fixed;width:728px;height:90px;background-image:url('http://www.superski.nl/pic/banners/htmlbanners/superski_htmlbanner_728x90.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.superski.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:22px;left:495px;\">\n";
	winter_land("130px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:50px;left:495px;\">\n";
	winter_thema("130px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:35px;left:635px;\"><input type=\"image\" src=\"http://www.superski.nl/pic/banners/htmlbanners/superski_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";

} elseif($_GET["t"]==2 and $_GET["wzt"]==5) {
	# superski 468x60
	echo "<div style=\"position:fixed;width:468px;height:60px;background-image:url('http://www.superski.nl/pic/banners/htmlbanners/superski_htmlbanner_468x60.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.superski.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:7px;left:230px;\">\n";
	winter_land("130px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:32px;left:230px;\">\n";
	winter_thema("130px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:19px;left:373px;\"><input type=\"image\" src=\"http://www.superski.nl/pic/banners/htmlbanners/superski_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==3 and $_GET["wzt"]==5) {
	# superski 250x250
	echo "<div style=\"position:fixed;width:250px;height:250px;background-image:url('http://www.superski.nl/pic/banners/htmlbanners/superski_htmlbanner_250x250.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.superski.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:193px;left:20px;\">\n";
	winter_land("120px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:220px;left:20px;\">\n";
	winter_thema("120px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:208px;left:150px;\"><input type=\"image\" src=\"http://www.superski.nl/pic/banners/htmlbanners/superski_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==4 and $_GET["wzt"]==5) {
	# superski 234x60
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:234px;height:60px;background-image:url('http://www.superski.nl/pic/banners/htmlbanners/superski_htmlbanner_234x60.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.superski.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:6px;left:95px;\">\n";
	winter_land("100px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:30px;left:95px;\">\n";
	winter_thema("100px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:7px;left:198px;\"><input type=\"image\" src=\"http://www.superski.nl/pic/banners/htmlbanners/superski_zoekenboek_klein.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==5 and $_GET["wzt"]==5) {
	# superski 120x600
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:120px;height:600px;background-image:url('http://www.superski.nl/pic/banners/htmlbanners/superski_htmlbanner_120x600.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.superski.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:460px;left:3px;\">\n"; #235
	winter_land("110px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:494px;left:3px;\">\n";
	winter_thema("110px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:535px;left:16px;\"><input type=\"image\" src=\"http://www.superski.nl/pic/banners/htmlbanners/superski_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==6 and $_GET["wzt"]==5) {
	# superski 300x250
	echo "<div style=\"position:fixed;width:300px;height:250px;background-image:url('http://www.superski.nl/pic/banners/htmlbanners/superski_htmlbanner_300x250.jpg?c=1');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.superski.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:188px;left:40px;\">\n";
	winter_land("120px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:219px;left:40px;\">\n";
	winter_thema("120px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:203px;left:170px;\"><input type=\"image\" src=\"http://www.superski.nl/pic/banners/htmlbanners/superski_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==1 and $_GET["wzt"]==6) {
	# italissima.be 728x90
	echo "<div style=\"position:fixed;width:728px;height:90px;background-image:url('http://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_728x90.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:22px;left:320px;\">\n";
	italissima_regio();
	echo "</div>";

	echo "<div style=\"position:absolute;top:50px;left:320px;\">\n";
	italissima_datum();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:35px;left:459px;\"><input type=\"image\" src=\"http://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==2 and $_GET["wzt"]==6) {
	# italissima.be 468x60
	echo "<div style=\"position:fixed;width:468px;height:60px;background-image:url('http://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_468x60.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:7px;left:230px;\">\n";
	italissima_regio();
	echo "</div>";

	echo "<div style=\"position:absolute;top:32px;left:230px;\">\n";
	italissima_datum();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:19px;left:372px;\"><input type=\"image\" src=\"http://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==3 and $_GET["wzt"]==6) {
	# italissima.be 250x250
	echo "<div style=\"position:fixed;width:250px;height:250px;background-image:url('http://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_250x250.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:133px;left:12px;\">\n";
	italissima_regio();
	echo "</div>";

	echo "<div style=\"position:absolute;top:169px;left:12px;\">\n";
	italissima_datum();
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:205px;left:12px;\">\n";
	italissima_personen();
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:169px;left:153px;\"><input type=\"image\" src=\"http://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==4 and $_GET["wzt"]==6) {
	# italissima.be 234x60
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:234px;height:60px;background-image:url('http://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_234x60.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:5px;left:92px;\">\n";
	italissima_regio("100px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:33px;left:92px;\">\n";
	italissima_datum("100px");
	echo "</div>\n";
	echo "<div style=\"position:absolute;top:7px;left:199px;\"><input type=\"image\" src=\"http://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek_klein.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";
} elseif($_GET["t"]==5 and $_GET["wzt"]==6) {
	# italissima.be 120x600
	echo "<style>\nselect {\nfont-size:8pt;\n}\n</style>\n";

	echo "<div style=\"position:fixed;width:120px;height:600px;background-image:url('http://www.italissima.nl/pic/banners/htmlbanners/italissima_htmlbanner_120x600.png');\">\n";
	echo "<form name=\"frm\" method=\"get\" action=\"http://www.italissima.nl/\" target=\"_blank\">\n";
	echo "<div style=\"position:absolute;top:275px;left:3px;\">\n";
	italissima_regio("110px");
	echo "</div>";

	echo "<div style=\"position:absolute;top:310px;left:3px;\">\n";
	italissima_datum("110px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:345px;left:3px;\">\n";
	italissima_personen("110px");
	echo "</div>\n";

	echo "<div style=\"position:absolute;top:390px;left:16px;\"><input type=\"image\" src=\"http://www.italissima.nl/pic/banners/htmlbanners/italissima_zoekenboek.gif\" name=\"submit\" id=\"submit\" onclick=\"formsubmit('s');return false;\" style=\"cursor:pointer;border:none;\" alt=\"\" /></div>\n";
	echo "</form>\n";
	echo "</div>\n";

}
echo "</body>\n";
echo "</html>\n";

?>