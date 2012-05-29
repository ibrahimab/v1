<?php

$robot_noindex=true;
$vars["verberg_lastacc"]=true;
include("admin/vars.php");

# Totale reissom opvragen voor TradeTracker, Cleafs en Google Analytics
if($_GET["aanvraagnr"]) {
	$_GET["aanvraagnr"]=intval($_GET["aanvraagnr"]);
	$gegevens=get_boekinginfo($_GET["aanvraagnr"]);
	if($gegevens["fin"]["totale_reissom"]>0) {
		$totalereissom=round($gegevens["fin"]["totale_reissom"],2);
	}
	$share_url=$gegevens["stap1"]["accinfo"]["url_seo"];
} else {
	$share_url=$vars["basehref"];
}
if($totalereissom>0) {
	$vars["googleanalytics_extra"]="_gaq.push(['_addTrans',
    '".htmlentities($_GET["aanvraagnr"])."',           // order ID - required
    '',  // affiliation or store name
    '".htmlentities($totalereissom)."',          // total - required
    '',           // tax
    '',              // shipping
    '',       // city
    '',     // state or province
    ''             // country
  ]);
  _gaq.push(['_addItem',
    '".htmlentities($_GET["aanvraagnr"])."',           // order ID - required
    '1',           // SKU/code - required
    '".htmlentities($gegevens["stap1"]["accinfo"]["begincode"].$gegevens["stap1"]["accinfo"]["typeid"])."',        // product name
    '',   // category or variation
    '".htmlentities($totalereissom)."',          // unit price - required
    '1'               // quantity - required
  ]);
  _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers";

}

include "content/opmaak.php";

?>