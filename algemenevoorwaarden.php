<?php

$language_content=true;
include("admin/vars.php");

if (in_array($vars['website'], $vars['anvr'])) {
	redirect($vars['basehref'] . txt("menu_voorwaarden") . '.php');
}

include "content/opmaak.php";

?>