<?php

# Vallandry

include("admin/vars.php");
if($vars["websitetype"]<>6) {
	header("Location: ".$vars["path"]);
	exit;
}
if($_GET["chaletpark"]) {
	if(!$txt["nl"]["chalets"][$_GET["chaletpark"]]) {
		header("Location: ".$vars["path"]);
		exit;
	} else {
		$title["chalets"]=ucfirst(txt($_GET["chaletpark"],"chalets"));
	}
}
if($_GET["seizoen"]) {
	if($_GET["seizoen"]=="winter") {
		$vars["seizoentype"]=1;
	} else {
		$vars["seizoentype"]=2;
	}
}

# jQuery UI theme laden (t.b.v. autocomplete)
$vars["page_with_jqueryui"]=true;

# jQuery Chosen laden
$vars["jquery_chosen"]=true;

# Zoeken op accommodatiecode: redirect naar die accommodatie
if(ereg("^[A-Za-z]([0-9]+)$",trim($_GET["fzt"]),$regs)) {
	$db->query("SELECT type_id, begincode FROM view_accommodatie WHERE type_id='".addslashes($regs[1])."' AND websites LIKE '%".$vars["website"]."%' AND atonen=1 AND ttonen=1 AND archief=0 AND wzt='".$vars["seizoentype"]."'");
	if($db->next_record()) {
		header("Location: ".$path."accommodatie/".$db->f("begincode").$db->f("type_id")."/");
		exit;
	}
}

include "content/opmaak.php";

?>