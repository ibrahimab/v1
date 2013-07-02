<?php

header('Content-Type: text/xml; charset=utf-8');

echo "<";
echo "?xml version=\"1.0\" encoding=\"UTF-8\"?";
echo ">\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

include("admin/vars.php");


# Gewone pagina's
$sitemap_url["hourly"][]=$vars["basehref"];
$sitemap_url["hourly"][]=$vars["basehref"].txt("menu_zoek-en-boek").".php";
$sitemap_url["daily"][]=$vars["basehref"].txt("menu_skigebieden").".php";
$sitemap_url["daily"][]=$vars["basehref"].txt("menu_aanbiedingen").".php";

if($vars["website"]=="C" or $vars["website"]=="B") {
	$sitemap_url["weekly"][]=$vars["basehref"].txt("menu_weekendski").".php";
}

if($vars["website"]=="C" or $vars["website"]=="B") {
	$sitemap_url["weekly"][]=$vars["basehref"].txt("menu_vraag-ons-advies").".php";
}
$sitemap_url["weekly"][]=$vars["basehref"].txt("menu_contact").".php";

if($vars["nieuwsbrief_aanbieden"]) {
	$sitemap_url["weekly"][]=$vars["basehref"].txt("menu_nieuwsbrief").".php";
}

$sitemap_url["monthly"][]=$vars["basehref"].txt("menu_wie-zijn-wij").".php";



foreach ($sitemap_url as $key => $value) {
	foreach ($value as $key2 => $value2) {
		echo "<url>\n";
		echo "<loc>".$value2."</loc>\n";
		echo "<changefreq>".$key."</changefreq>\n";
		if($key=="hourly") {
			echo "<priority>1.0</priority>\n";
		} elseif($key=="daily") {
			echo "<priority>0.8</priority>\n";
		} elseif($key=="weekly") {
			echo "<priority>0.5</priority>\n";
		} elseif($key=="monthly") {
			echo "<priority>0.1</priority>\n";
		}
		echo "</url>\n";
	}
}

# Accommodaties
$db->query("SELECT begincode, type_id, soortaccommodatie, naam, tnaam FROM view_accommodatie WHERE archief=0 AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' ORDER BY type_id;");
while($db->next_record()) {
	echo "<url>\n";
	echo "<loc>".seo_acc_url($db->f("begincode").$db->f("type_id"),$db->f("soortaccommodatie"),$db->f("naam"),$db->f("tnaam"))."</loc>\n";
	echo "<changefreq>weekly</changefreq>\n";
	echo "<priority>0.9</priority>\n";
	echo "</url>\n";
}

# Regio's
$db->query("SELECT DISTINCT skigebied FROM view_accommodatie WHERE archief=0 AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' ORDER BY skigebied;");
while($db->next_record()) {
	echo "<url>\n";
	echo "<loc>".$vars["basehref"].txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".wt_convert2url_seo($db->f("skigebied"))."/</loc>\n";
	echo "<changefreq>weekly</changefreq>\n";
	echo "<priority>0.8</priority>\n";
	echo "</url>\n";
}

# Plaatsen
$db->query("SELECT DISTINCT plaats FROM view_accommodatie WHERE archief=0 AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' ORDER BY plaats;");
while($db->next_record()) {
	echo "<url>\n";
	echo "<loc>".$vars["basehref"].txt("menu_plaats")."/".wt_convert2url_seo($db->f("plaats"))."/</loc>\n";
	echo "<changefreq>weekly</changefreq>\n";
	echo "<priority>0.8</priority>\n";
	echo "</url>\n";
}

# Landen
$db->query("SELECT DISTINCT land".$vars["ttv"]." AS land FROM view_accommodatie WHERE archief=0 AND atonen=1 AND ttonen=1 AND websites LIKE '%".$vars["website"]."%' ORDER BY land;");
while($db->next_record()) {
	echo "<url>\n";
	echo "<loc>".$vars["basehref"].txt("menu_land")."/".wt_convert2url_seo($db->f("land"))."/</loc>\n";
	echo "<changefreq>weekly</changefreq>\n";
	echo "<priority>0.8</priority>\n";
	echo "</url>\n";
}

# Thema's
if($vars["website"]=="C" or $vars["website"]=="B" or $vars["website"]=="E" or $vars["website"]=="Z") {
	$db->query("SELECT url".$vars["ttv"]." AS url FROM thema WHERE wzt='".intval($vars["seizoentype"])."' AND actief=1 ORDER BY 1;");
	while($db->next_record()) {
		echo "<url>\n";
		echo "<loc>".$vars["basehref"].txt("canonical_accommodatiepagina")."/".txt("menu_thema")."/".$db->f("url")."/</loc>\n";
		echo "<changefreq>weekly</changefreq>\n";
		echo "<priority>0.8</priority>\n";
		echo "</url>\n";
	}
}

echo "</urlset>";

?>