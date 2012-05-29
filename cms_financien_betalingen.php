<?php

set_time_limit(0);
$mustlogin=true;

include("admin/vars.php");

if($_POST["inkoopbetalingen_filled"]) {
	# inkoopbetalingen aanmaken
	while(list($key,$value)=@each($_POST["nieuwebetaling"])) {
		if(preg_match("/^gar_([0-9]+)$/",$key,$regs)) {
			$db->query("INSERT INTO boeking_betaling_lev SET garantie_id='".addslashes($regs[1])."', bedrag='".addslashes($value)."', datum=NOW(), adddatetime=NOW(), editdatetime=NOW();");
		} else {
			$db->query("INSERT INTO boeking_betaling_lev SET boeking_id='".addslashes($key)."', bedrag='".addslashes($value)."', datum=NOW(), adddatetime=NOW(), editdatetime=NOW();");
			$gegevens=get_boekinginfo($key);
			chalet_log("inkoopbetaling ".date("d-m-Y")." onderweg: € ".number_format($value,2,',','.'),true,true);
		}
	}
	
	# goedkeuring opslaan	
	while(list($key,$value)=@each($_POST["nieuwegoedkeuring"])) {
		$db->query("UPDATE boeking_betaling_lev SET bedrag_goedgekeurd='".addslashes($value)."', editdatetime=NOW() WHERE boeking_betaling_lev_id='".addslashes($key)."';");
		$db->query("SELECT boeking_id FROM boeking_betaling_lev WHERE boeking_betaling_lev_id='".addslashes($key)."';");
		if($db->next_record()) {
			$gegevens=get_boekinginfo($db->f("boeking_id"));
			chalet_log("inkoopbetaling goedgekeurd: € ".number_format($value,2,',','.'),true,true);
		}
	}
	header("Location: ".$_SERVER["REQUEST_URI"]."#lev_".intval($_POST["leverancier_id"]));
	exit;
}

$layout->display_all($cms->page_title);


?>