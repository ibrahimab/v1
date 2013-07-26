<?php



#
#
# XML-export voor HomeAway
#
#
#


set_time_limit(0);
$geen_tracker_cookie=true;
$unixdir="../";
include("../admin/vars.php");

$wzt=1;
$homeaway_types=array(
	"242"=>"1138508",
	"240"=>"1138509",
	"453"=>"1138510",
	"249"=>"1138511",
	"3167"=>"1138512",
	"244"=>"1138513",
	"248"=>"1138514",
	"4034"=>"1138515",
	"114"=>"1138516",
	"6635"=>"1138517",
	"5491"=>"1138519",
	"7910"=>"1138520",
	"6577"=>"1138521",
	"1723"=>"1138522",
	"4695"=>"1138523",
	"3177"=>"1138524",
	"5319"=>"1138525",
);

foreach ($homeaway_types as $key => $value) {
	$alletypes.=",".$key;
}

$alletypes=substr($alletypes,1);


if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html2") {
	header("Content-Type: text/plain; charset=utf-8");
} else {
	header("Content-Type: text/xml; charset=utf-8");
	echo "<";
	echo "?xml version=\"1.0\" encoding=\"utf-8\"?";
	echo ">\n";
}


if($_GET["feedtype"]=="rates") {

	#
	# availability-and-prices
	#

	echo "<ratePeriodsBatch>\n<documentVersion>1.0</documentVersion>\n<advertisers>\n<advertiser>\n<assignedId>CHALETWINTERSPORTS</assignedId>\n";

	$availability_keuzes[1]="directly";
	$availability_keuzes[2]="request";
	$availability_keuzes[3]="unavailable";

	# Afwijkende vertrekdagen?
	if($alletypes) {
		$typeid_inquery_vertrekdag=$alletypes;
	} else {
		$typeid_inquery_vertrekdag="ALL";
	}
	include("../content/vertrekdagaanpassing.html");

	$db->query("SELECT t.type_id, ta.wederverkoop_verkoopprijs, ta.beschikbaar, ta.week, ta.seizoen_id, ta.c_bruto, ta.bruto, ta.voorraad_garantie, ta.voorraad_allotment, ta.voorraad_vervallen_allotment, ta.voorraad_optie_leverancier, ta.voorraad_xml, ta.voorraad_request, ta.voorraad_optie_klant, l.begincode, a.toonper, a.accommodatie_id, a.aankomst_plusmin, a.vertrek_plusmin FROM tarief ta, accommodatie a, type t, plaats p, skigebied s, land l WHERE ta.week>'".time()."' AND ta.blokkeren_wederverkoop=0 AND ta.type_id=t.type_id AND p.land_id=l.land_id AND p.skigebied_id=s.skigebied_id AND a.plaats_id=p.plaats_id AND t.accommodatie_id=a.accommodatie_id AND a.wzt IN (".$wzt.")".($alletypes ? " AND t.type_id IN (".$alletypes.")" : "")." ORDER BY t.type_id, ta.week;");
	while($db->next_record()) {
		$aanbieding[$db->f("seizoen_id")]=aanbiedinginfo($db->f("type_id"),$db->f("seizoen_id"),$db->f("week"));
		if(($db->f("toonper")==3 and $db->f("c_bruto")>0) or ($db->f("toonper")==1 and $db->f("bruto")>0)) {

			# Beschikbaarheid
			$availability=3; # standaard: niet beschikbaar
			if($db->f("wederverkoop_verkoopprijs")>0) {
				# 1 = groen, 2 = oranje, 3 = zwart
				if($db->f("voorraad_garantie")+$db->f("voorraad_allotment")+$db->f("voorraad_vervallen_allotment")+$db->f("voorraad_optie_leverancier")+$db->f("voorraad_xml")-$db->f("voorraad_optie_klant")>=1) {
					$availability=1;
				} elseif($db->f("voorraad_request")>=1 or $db->f("voorraad_optie_klant")>=1) {
					$availability=2;
				}
			}

			if($availability<>3) {


				if(!$typeid_gehad[$db->f("type_id")]) {

					if($typeid_gehad) {
						echo "</ratePeriods>\n";
						echo "</advertiserRatePeriods>\n";
					}

					echo "<advertiserRatePeriods>\n";
					echo "<listingExternalId>".$homeaway_types[$db->f("type_id")]."</listingExternalId>\n";
					echo "<unitExternalId>".$homeaway_types[$db->f("type_id")]."</unitExternalId>\n";
					echo "<ratePeriods>\n";

					$typeid_gehad[$db->f("type_id")]=true;

				}

				echo "	<ratePeriod>\n";
#				echo "		<unit_id>".xml_text($db->f("begincode").$db->f("type_id"))."</unit_id>\n";

				# Exacte aankomstdatum
				$week=$db->f("week");
				if($vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$week)] or $db->f("aankomst_plusmin")) {
					$aangepaste_unixtime=mktime(0,0,0,date("m",$week),date("d",$week)+$vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$week)]+$db->f("aankomst_plusmin"),date("Y",$week));
					$exacte_unixtime=$aangepaste_unixtime;
				} else {
					$exacte_unixtime=$week;
				}

				# Aantal nachten

				# Afwijkende vertrekdag
				unset($aantalnachten_afwijking);
				$aantalnachten_afwijking=$vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$week)];
				$volgendeweek=mktime(0,0,0,date("n",$week),date("j",$week)+7,date("Y",$week));
				$aantalnachten_afwijking-=$vertrekdag[$db->f("type_id")][$db->f("seizoen_id")][date("dm",$volgendeweek)];
				# Afwijkende verblijfsduur
				$aantalnachten_afwijking=$aantalnachten_afwijking+$db->f("aankomst_plusmin")-$db->f("vertrek_plusmin");
				$nachten=7-$aantalnachten_afwijking;
#				echo "		<nights>".xml_text($nachten)."</nights>\n";

				echo "<dateRange>\n";
				echo "		<beginDate>".xml_text(date("Y-m-d",$exacte_unixtime))."</beginDate>\n";
				$enddate=mktime(0,0,0,date("m",$exacte_unixtime),date("d",$exacte_unixtime)+$nachten,date("Y",$exacte_unixtime));
				echo "		<endDate>".xml_text(date("Y-m-d",$enddate))."</endDate>\n";
				echo "</dateRange>\n";

				echo "<minimumStay>ONE_WEEK</minimumStay>\n";
				echo "<name>\n<texts>\n<text locale='en'>\n<textValue>Winter</textValue>\n</text>\n</texts></name>\n";

				# Prijs
				# aanbiedingen verwerken
				$prijs=verwerk_aanbieding($db->f("wederverkoop_verkoopprijs"),$aanbieding[$db->f("seizoen_id")]["typeid_sort"][$db->f("type_id")],$db->f("week"));
				echo "<rates>\n<rate rateType='WEEKLY'>\n";
				echo "<amount currency='EUR'>".xml_text(number_format($prijs,2,".",""))."</amount>\n";
				echo "</rate>\n";
				echo "</rates>\n";

#				echo "		<availability>".xml_text($availability_keuzes[$availability])."</availability>\n";

				echo "	</ratePeriod>\n";

			}
		}
	}
	if($typeid_gehad) {
		echo "</ratePeriods>\n";
		echo "</advertiserRatePeriods>\n";
	}
	echo "</advertiser>\n</advertisers>\n\n</ratePeriodsBatch>";
}


?>