<?php

$mustlogin=true;

include("admin/vars.php");

$form=new form2("frm");
$form->settings["fullname"]="vertalingen";
$form->settings["layout"]["css"]=false;
$form->settings["message"]["submitbutton"]["nl"]="VERSTUREN";

// $doorloop_array=array("","t","b","v","z");
$doorloop_array=array("","v"); # alleen Vallandry-afwijkingen moeten in het Engels vertaald worden
while(list($afwijkingkey,$afwijkingvalue)=each($doorloop_array)) {
	if($afwijkingvalue) {
		$afwijking="_".$afwijkingvalue;
	} else {
		$afwijking="";
	}

	while(list($key,$value)=each($txta["nl".$afwijking])) {
		if(!$txta["en".$afwijking][$key] and $value) {
			$form->field_htmlrow("","<b>Site-brede tekst &quot;".$key."&quot;</b><p><div style=\"width:490px\"><i>Nederlands:</i><br><div style=\"border:1px solid #000000;;padding:5px;\">".nl2br(htmlentities($value))."</div></div>");
			$form->field_textarea(0,ereg_replace("-","",$key),"<i>Engels (txta[".$key."])</i>","","","",array("newline"=>true,"title_html"=>true));
			$form->field_htmlrow("","<hr>");
			$vars["onvertaald"]=true;
		}
	}

	while(list($key,$value)=each($txt["nl".$afwijking])) {
		while(list($key2,$value2)=each($value)) {
			if(!$txt["en".$afwijking][$key][$key2] and $value2) {
				$form->field_htmlrow("","<b>Pagina &quot;".($key=="vars" ? "algemeen" : ($key=="index" ? "hoofdpagina" : $key))."&quot; - onderdeel &quot;".$key2."&quot;</b><p><div style=\"width:490px\"><i>Nederlands:</i><br><div style=\"border:1px solid #000000;padding:5px;\">".nl2br(htmlentities($value2))."</div></div>");
				$form->field_textarea(0,ereg_replace("-","",$key).$key2,"<i>Engels (txt[".$key."][".$key2."])</i>","","","",array("newline"=>true,"title_html"=>true));
				$form->field_htmlrow("","<hr>");
				$vars["onvertaald"]=true;
			}
		}
	}
}
if(is_array($nieuwe_vertaling)) {
	while(list($key,$value)=each($nieuwe_vertaling["en"])) {
		while(list($key2,$value2)=each($value)) {
			if(!$txt["en".$afwijking][$key][$key2] and $value2) {
				$form->field_htmlrow("","<b>Pagina &quot;".($key=="vars" ? "algemeen" : ($key=="index" ? "hoofdpagina" : $key))."&quot; - onderdeel &quot;".$key2."&quot;</b><p><div style=\"width:490px\"><i>Nederlands:</i><br><div style=\"border:1px solid #000000;padding:5px;\">".nl2br(htmlentities($txt["nl"][$key][$key2]))."</div></div>");
				$form->field_textarea(0,ereg_replace("-","",$key).$key2,"<i>Engels (txt[".$key."][".$key2."])</i>","","","",array("newline"=>true,"title_html"=>true));
				$form->field_htmlrow("","<hr>");
				$vars["onvertaald"]=true;
			}
		}
	}
}

$form->check_input();

if($form->okay) {
	$form->mail("jeroen@webtastic.nl","","Vertalingen Chalet.nl");
}
$form->end_declaration();

$layout->display_all($cms->page_title);

?>