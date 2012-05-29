<?php

$mustlogin=true;

include("admin/vars.php");
if($_POST["filled"]) {
	while(list($key,$value)=@each($_POST["input"])) {
		if($value=="on") {
			if(ereg("^plaats_([0-9]+)$",$key,$regs)) {
				$db->query("UPDATE plaats SET pdfupload_datum=NOW(), pdfupload_user='".addslashes($login->user_id)."' WHERE plaats_id='".addslashes($regs[1])."';");
			} elseif(ereg("^route_nl_([0-9]+)$",$key,$regs)) {
				$db->query("UPDATE accommodatie SET pdfupload_datum=NOW(), pdfupload_user='".addslashes($login->user_id)."' WHERE accommodatie_id='".addslashes($regs[1])."';");
			} elseif(ereg("^route_en_([0-9]+)$",$key,$regs)) {
				$db->query("UPDATE accommodatie SET pdfupload_datum_en=NOW(), pdfupload_user_en='".addslashes($login->user_id)."' WHERE accommodatie_id='".addslashes($regs[1])."';");
			}
		}
	}
	header("Location: cms_pdfcontrole.php");
	exit;
}

$layout->display_all($cms->page_title);

?>