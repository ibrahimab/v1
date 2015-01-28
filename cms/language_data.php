<?php
$language_options = array(
	lang_nl => html("dutch", "beoordelingen"),
	lang_en => html("english", "beoordelingen"),
	lang_de => html("german", "beoordelingen"),
);
$flag_template = " <img src='/pic/flags/[[language]].png' height='15' />";
$nl_flag = str_replace("[[language]]",lang_nl, $flag_template);
$en_flag = str_replace("[[language]]",lang_en, $flag_template);
$de_flag = str_replace("[[language]]",lang_de, $flag_template);
$button_template = "<br><button class='btn-translation' value='[[language]]' >[[button_text]]</button>";
$button_all = str_replace(array("[[language]]","[[button_text]]"), array('all', html("translate_all", "beoordelingen")), $button_template);
$button_nl = str_replace(array("[[language]]","[[button_text]]"), array(lang_nl, html("translate_language", "beoordelingen")), $button_template);
$button_en = str_replace(array("[[language]]","[[button_text]]"), array(lang_en, html("translate_language", "beoordelingen")), $button_template);
$button_de = str_replace(array("[[language]]","[[button_text]]"), array(lang_de, html("translate_language", "beoordelingen")), $button_template);