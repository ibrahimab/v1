<?php


include("admin/vars.php");

if($vars["websitetype"]<>7) {
	header("Location: ".$vars["path"]);
	exit;
}

$meta_description="Op dit blog vind je artikelen die te maken hebben met Italië en het aanbod van Italissima, aanbieder van agriturismi en andere vakantiehuizen in Italië.";

if($_GET["b"]) {
	$db->query("SELECT blog_id, titel, inleiding, inhoud, UNIX_TIMESTAMP(plaatsingsdatum) AS plaatsingsdatum, accommodatiecodes FROM blog WHERE 1=1 AND blog_id='".addslashes($_GET["b"])."' AND actief=1 AND plaatsingsdatum<NOW();");
	if($db->next_record()) {
		$blog["blog_id"]=$db->f("blog_id");
		$blog["titel"]=$db->f("titel");
		$blog["inleiding"]=$db->f("inleiding");
		$blog["inhoud"]=$db->f("inhoud");
		$blog["plaatsingsdatum"]=$db->f("plaatsingsdatum");
		$blog["accommodatiecodes"]=$db->f("accommodatiecodes");

		if(preg_match("/(https?:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9]+))/",$blog["inhoud"],$regs)) {
			$blog["youtube"]=$regs[2];
			$blog["inhoud"]=str_replace($regs[1],"",$blog["inhoud"]);
		}

		# reactie-form
		$form=new form2("frm");
		$form->settings["fullname"]="blogreactie";
		$form->settings["layout"]["css"]=false;
		$form->settings["message"]["submitbutton"]["nl"]="VERSTUREN";
		#$form->settings["target"]="_blank";

		# Optionele instellingen (onderstaande regels bevatten de standaard-waarden)
		$form->settings["go_nowhere"]=false;			# bij true: ga na form=okay nergens heen
		$form->settings["goto"]=$vars["path"]."blog.php?b=".$blog["blog_id"]."#reacties";

		#_field: (obl),id,title,db,prevalue,options,layout

#		$form->field_text(1,"test","test",array("field"=>"test")); # (opslaan in databaseveld "test")
		$form->field_text(1,"naam","Naam");
		$form->field_email(0,"email","E-mailadres","","","",array("add_html_after_field"=>"<br><span style=\"font-size:0.8em;font-style:italic;\">Je mailadres wordt niet op de website gepubliceerd.</span>"));
		$form->field_textarea(1,"reactie","Reactie","","","",array("newline"=>true));
		$form->field_htmlrow("",html("recaptcha_uitleg","accommodatiemail")."* <img src=\"".$vars["path"]."pic/captcha_image.php?c=1\" id=\"captcha_img\"><input type=\"text\" name=\"captcha\" maxlength=\"5\" autocomplete=\"off\" class=\"wtform_input\">&nbsp;&nbsp;&nbsp;<span id=\"captcha_juist\"><img src=\"".$vars["path"]."pic/vinkje_goedgekeurd.gif\" class=\"vinkje_goedgekeurd\"></span><span id=\"captcha_onjuist\">".html("captcha_onjuist","accommodatiemail",array("h_1"=>"<a href=\"#\" id=\"captcha_reload\">","h_2"=>"</a>"))."</span><br/><br/>");
		$form->field_htmlrow("","<i>Bij misbruik behoudt Italissima zich het recht voor om reacties (deels) te verwijderen. Het plaatsen van links is niet mogelijk.</i>");

		$form->check_input();

		if($form->filled) {

			// uitgezet op 6 augustus 2013 op verzoek van Bjorn
			// $filter_array=array("http://","https://");
			// while(list($key,$value)=each($filter_array)) {
			// 	$pos=strpos(" ".$form->input["reactie"],$value);
			// 	if($pos!==false) {
			// 		$form->error("reactie","het plaatsen van links is vanwege spam-misbruik helaas niet toegestaan");
			// 		$niet_opslaan=true;
			// 		break;
			// 	}
			// }

			if(!$_SESSION["captcha_okay"]) {
				$form->error("reactie","het plaatsen van een reactie kan alleen na het correct overtypen van de 5 letters");
			}
		}

		if($form->okay) {

			# bericht filteren op spam
			$filter_array=array("<a href=\"","[url=","[link=");

			while(list($key,$value)=each($filter_array)) {
				$pos=strpos(" ".$form->input["reactie"],$value);
				if($pos!==false) {
					$niet_opslaan=true;
				}
			}

			if(!$niet_opslaan) {
				# Opslaan in database
				$db2->query("INSERT INTO blog_reactie SET blog_id='".$db->f("blog_id")."', naam='".addslashes($form->input["naam"])."', email='".addslashes($form->input["email"])."', inhoud='".addslashes(trim($form->input["reactie"]))."', adddatetime=NOW(), editdatetime=NOW();");

				# Mailtje naar Sélina en Bjorn sturen
				$mail=new wt_mail;
				$mail->fromname="Italissima";
				$mail->from="info@italissima.nl";
				$mail->subject="Nieuwe blog-reactie";

				$mail->plaintext="Bij de blogpost \"".$db->f("titel")."\" is de volgende reactie geplaatst:\n\nNaam: ".$form->input["naam"]."\nE-mail: ".($form->input["email"] ? $form->input["email"] : "niet ingevuld")."\nReactie:\n\n".$form->input["reactie"]."\n\nGa naar de volgende URL om reacties te bewerken of te wissen:\nhttps://www.chalet.nl/cms_blog.php?show=44&44k0=".$db->f("blog_id")."\n\n";

				$mail->toname="Sélina";
				$mail->to="selina@chalet.nl";
				$mail->send();

				$mail->toname="Bjorn";
				$mail->to="bjorn@chalet.nl";
				$mail->send();
			}
		}
		$form->end_declaration();




		# blok rechts bepalen
		if($blog["accommodatiecodes"]) {

			$acc=preg_split("/,/",$blog["accommodatiecodes"]);
			unset($inquery);
			while(list($key,$value)=each($acc)) {
				if(preg_match("/^[A-Z]([0-9]+)$/",$value,$regs)) {
					$inquery.=",".$regs[1];
				}
			}

			if($inquery) {
				$db->query("SELECT accommodatie_id, type_id, naam, skigebied, plaats, begincode, soortaccommodatie FROM view_accommodatie WHERE type_id IN (".substr($inquery,1).") ORDER BY FIND_IN_SET(type_id,'".substr($inquery,1)."');");
				while($db->next_record()) {
					$accurl=$vars["path"]."accommodatie/".$db->f("begincode").$db->f("type_id")."/";

					$file="pic/cms/types_specifiek/".$db->f("type_id").".jpg";
					if(!file_exists($file)) {
						$file="pic/cms/accommodaties/".$db->f("accommodatie_id").".jpg";
					}
					if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
						if(!file_exists($file)) {
							$file="pic/cms/accommodaties/2031.jpg";
						}
					}

					if(file_exists($file)) {
						$blog["blok_rechts"].="<div class=\"blog_opvalblok\" onclick=\"document.location.href='".htmlentities($accurl)."'\">";
						$blog["blok_rechts"].="<div class=\"blog_opval_regel1\">".wt_he($db->f("skigebied"))."</div>";
						$blog["blok_rechts"].="<div class=\"blog_opval_regel2\">".wt_he($db->f("plaats"))."</div>";
						$blog["blok_rechts"].="<div class=\"overlay_foto\">";

						$blog["blok_rechts"].="<img src=\"".htmlentities($vars["path"].$file)."\" width=\"200\">";
						$blog["blok_rechts"].="<div class=\"blog_opval_regel3\">".wt_he(ucfirst($vars["soortaccommodatie"][$db->f("soortaccommodatie")])." ".$db->f("naam"))."</div>";
						$blog["blok_rechts"].="</div>";
						$blog["blok_rechts"].="</div>"; # afsluiten class blog_opvalblok
					}
				}
			}
		}

		$title["blog"]=$blog["titel"];
		$breadcrumbs[txt("menu_blog").".php"]=txt("title_blog");
		$breadcrumbs["last"]=$blog["titel"];
	} else {
		header("Location: ".$vars["path"]."blog.php");
		exit;
	}
} elseif($_GET["archief"]) {
	$title["blog"]="Blog-archief";
	$breadcrumbs[txt("menu_blog").".php"]=txt("title_blog");
	$breadcrumbs["last"]="Archief";
} else {
	if($_GET["cat"]) {
		$title["blog"].=" - ".ucfirst($vars["blogcategorie_italissima"][$_GET["cat"]]);
		$breadcrumbs[txt("menu_blog").".php"]=txt("title_blog");
		$breadcrumbs[txt("menu_blog").".php?archief=1"]="Archief";
		$breadcrumbs["last"]=ucfirst($vars["blogcategorie_italissima"][$_GET["cat"]]);
	}
}

include "content/opmaak.php";

?>