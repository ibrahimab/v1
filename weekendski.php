<?php

$vars["verberg_zoekenboeklinks"]=true;

include("admin/vars.php");

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