<?php



# Uitleg:
# https://support.google.com/webmasters/answer/80472?hl=nl
# http://www.reelseo.com/vimeo-sitemaps/
# http://moz.com/blog/video-sitemap-guide-for-vimeo-and-youtube
# http://vimeo.com/moogaloop.swf?clip_id=64372859
# http://player.vimeo.com/external/64372859.sd.mp4



$geen_tracker_cookie=true;
include("admin/vars.php");

header('Content-Type: text/xml; charset=utf-8');

echo "<";
echo "?xml version=\"1.0\" encoding=\"UTF-8\"?";
echo ">\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:video=\"http://www.google.com/schemas/sitemap-video/1.0\">\n";

# Regio's
$db->query("SELECT naam, video_url, descriptiontag".$vars["ttv"]." AS descriptiontag FROM skigebied WHERE websites LIKE '%".$vars["website"]."%' AND video=1 ORDER BY naam;");
while($db->next_record()) {

	$video_url=$db->f("video_url");
	$vimeo_url="http://vimeo.com/moogaloop.swf?clip_id=".preg_replace("@[^0-9]*@","",$video_url);

	if(!$video_al_gehad[$vimeo_url]) {

		$video_al_gehad[$vimeo_url]=true;

		$url=$vars["basehref"].txt("canonical_accommodatiepagina")."/".txt("menu_skigebied")."/".wt_convert2url_seo($db->f("naam"));

		echo "<url>\n";

		echo "<loc>".$url."</loc>\n";
		echo "<video:video>\n";
		echo "<video:title>".xml_text($db->f("naam"))."</video:title>\n";
		echo "<video:description>".xml_text($db->f("descriptiontag"))."</video:description>\n";
		echo "<video:thumbnail_loc>".$vars["basehref"]."pic/video-preview_".$vars["seizoentype"].".jpg</video:thumbnail_loc>\n";
		echo "<video:player_loc allow_embed=\"yes\">".$vimeo_url."</video:player_loc>\n";
		echo "</video:video>\n";
		echo "</url>\n";
	}
}

# Plaatsen
$db->query("SELECT naam, video_url, descriptiontag".$vars["ttv"]." AS descriptiontag FROM plaats WHERE websites LIKE '%".$vars["website"]."%' AND video=1 ORDER BY naam;");
while($db->next_record()) {

	$video_url=$db->f("video_url");
	$vimeo_url="http://vimeo.com/moogaloop.swf?clip_id=".preg_replace("@[^0-9]*@","",$video_url);

	if(!$video_al_gehad[$vimeo_url]) {

		$video_al_gehad[$vimeo_url]=true;

		$url=$vars["basehref"].txt("menu_plaats")."/".wt_convert2url_seo($db->f("naam"));

		echo "<url>\n";

		echo "<loc>".$url."</loc>\n";
		echo "<video:video>\n";
		echo "<video:title>".xml_text($db->f("naam"))."</video:title>\n";
		echo "<video:description>".xml_text($db->f("descriptiontag"))."</video:description>\n";
		echo "<video:thumbnail_loc>".$vars["basehref"]."pic/video-preview_".$vars["seizoentype"].".jpg</video:thumbnail_loc>\n";
		echo "<video:player_loc allow_embed=\"yes\">".$vimeo_url."</video:player_loc>\n";
		echo "</video:video>\n";
		echo "</url>\n";
	}
}


# Accommodaties/types
$db->query("SELECT t.type_id, l.begincode, a.soortaccommodatie, a.naam, t.naam".$vars["ttv"]." AS tnaam, a.korteomschrijving".$vars["ttv"]." AS korteomschrijving, t.korteomschrijving".$vars["ttv"]." AS tkorteomschrijving, a.video_url, t.video_url AS tvideo_url FROM accommodatie a, type t, land l, plaats p WHERE (a.video=1 OR t.video=1) AND t.accommodatie_id=a.accommodatie_id AND a.plaats_id=p.plaats_id AND p.land_id=l.land_id AND a.tonen=1 AND t.tonen=1 AND a.archief=0 AND a.websites LIKE '%".$vars["website"]."%' ORDER BY t.type_id;");
while($db->next_record()) {

	if($db->f("tvideo_url")) {
		$video_url=$db->f("tvideo_url");
	} else {
		$video_url=$db->f("video_url");
	}

	$vimeo_url="http://vimeo.com/moogaloop.swf?clip_id=".preg_replace("@[^0-9]*@","",$video_url);

	if(!$video_al_gehad[$vimeo_url]) {

		$video_al_gehad[$vimeo_url]=true;

		$url=seo_acc_url($db->f("begincode").$db->f("type_id"),$db->f("soortaccommodatie"),$db->f("naam"),$db->f("tnaam"))."#fotos";

		echo "<url>\n";

		echo "<loc>".$url."</loc>\n";
		echo "<video:video>\n";
		echo "<video:title>".xml_text(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".trim($db->f("naam"),$db->f("tnaam")))."</video:title>\n";
		if($db->f("tkorteomschrijving")) {
			$description=$db->f("tkorteomschrijving");
		} else {
			$description=$db->f("korteomschrijving");
		}
		echo "<video:description>".xml_text(trim($description))."</video:description>\n";
		echo "<video:thumbnail_loc>".$vars["basehref"]."pic/video-preview_".$vars["seizoentype"].".jpg</video:thumbnail_loc>\n";
		echo "<video:player_loc allow_embed=\"yes\">".$vimeo_url."</video:player_loc>\n";
		echo "</video:video>\n";
		echo "</url>\n";
	}
}


echo "</urlset>";



?>