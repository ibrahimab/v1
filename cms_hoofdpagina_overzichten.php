<?php

$mustlogin=true;
include("admin/vars.php");


if($_POST["cms_meldingen"]) {
	@reset($_POST["cms_hoofdpagina_soorten"]);
	while(list($key,$value)=@each($_POST["cms_hoofdpagina_soorten"])) {
		$db->query("UPDATE cmshoofdpagina SET rol='".addslashes($value)."', tonen='".addslashes($_POST["toon"][$key])."' WHERE cmshoofdpagina_id='".addslashes($key)."';");
#		echo $db->lastquery."<br>";
	}
	header("Location: ".$_SERVER["REQUEST_URI"]);
	exit;
}

$layout->display_all($cms->page_title);

?>