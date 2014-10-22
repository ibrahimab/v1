<?php

$mustlogin=true;

include("admin/vars.php");

$form=new form2("frm");
$form->settings["fullname"]="vertalingen";
$form->settings["layout"]["css"]=false;
$form->settings["message"]["submitbutton"]["nl"]="VERSTUREN";

if($_GET["taal"]) {
	$vertaal_taal = $_GET["taal"];
	$doorloop_array=array("");
} else {
	$vertaal_taal = "en";
	$doorloop_array=array("","v", "z", "i"); # alleen Vallandry-afwijkingen moeten in het Engels vertaald worden
}


while(list($afwijkingkey,$afwijkingvalue)=each($doorloop_array)) {
	if($afwijkingvalue) {
		$afwijking="_".$afwijkingvalue;
	} else {
		$afwijking="";
	}

	if($vertaal_taal=="en") {
		while(list($key,$value)=each($txta["nl".$afwijking])) {
			if(!$txta[$vertaal_taal.$afwijking][$key] and $value) {
				$form->field_htmlrow("","<b>Site-brede tekst &quot;".$key."&quot;</b><p><div style=\"width:676px\"><i>Nederlands:</i><br><div style=\"border:1px solid #000000;;padding:5px;\">".nl2br(wt_he($value))."</div></div>");
				$form->field_textarea(0,ereg_replace("-","_",$key)."_1","<i>".wt_he($vars["vertaal_talen"][$vertaal_taal])." (txta[".$key."])</i>","","","",array("newline"=>true,"title_html"=>true));
				$form->field_htmlrow("","<hr>");
				$vars["onvertaald"]=true;
			}
		}
	}

	while(list($key,$value)=each($txt["nl".$afwijking])) {
		while(list($key2,$value2)=each($value)) {
			// echo $key."=".$key2."===".$value2."<br/>";
			if(!$txt[$vertaal_taal.$afwijking][$key][$key2] and $value2) {
				$form->field_htmlrow("","<b>Pagina &quot;".($key=="vars" ? "algemeen" : ($key=="index" ? "hoofdpagina" : $key))."&quot; - onderdeel &quot;".$key2."&quot;</b><p><div style=\"width:676px\"><i>Nederlands:</i><br><div style=\"border:1px solid #000000;padding:5px;\">".nl2br(wt_he($value2))."</div></div>");
				$form->field_textarea(0,ereg_replace("-","_",$key).ereg_replace("-","_",$key2).$afwijkingvalue."_2","<i>".wt_he($vars["vertaal_talen"][$vertaal_taal])." (txt[".$key."][".$key2."])</i>","","","",array("newline"=>true,"title_html"=>true));
				$form->field_htmlrow("","<hr>");
				$vars["onvertaald"]=true;
			}
		}
	}
}
if(is_array($nieuwe_vertaling)) {
	while(list($key,$value)=each($nieuwe_vertaling["en"])) {
		while(list($key2,$value2)=each($value)) {
			if(!$txt[$vertaal_taal.$afwijking][$key][$key2] and $value2) {
				$form->field_htmlrow("","<b>Pagina &quot;".($key=="vars" ? "algemeen" : ($key=="index" ? "hoofdpagina" : $key))."&quot; - onderdeel &quot;".$key2."&quot;</b><p><div style=\"width:676px\"><i>Nederlands:</i><br><div style=\"border:1px solid #000000;padding:5px;\">".nl2br(wt_he($txt["nl"][$key][$key2]))."</div></div>");
				$form->field_textarea(0,ereg_replace("-","_",$key).ereg_replace("-","_",$key2)."_3","<i>".wt_he($vars["vertaal_talen"][$vertaal_taal])." (txt[".$key."][".$key2."])</i>","","","",array("newline"=>true,"title_html"=>true));
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