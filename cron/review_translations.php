<?php
//error_reporting(E_ALL);
set_time_limit(0);
$cron = true;
$css = false;
@require(__DIR__."/../admin/vars.php");
/** @var DB_Sql $db */
$db2 = clone $db;
if (!$db->connect()) {
	die('Unable to connect to database');
}
$google_translate = new google_translate();
$errors = array();
$updated_reviews = 0;

$db->query("SELECT
			be.boeking_id,
			be.websitetekst_gewijzigd,
			be.websitetekst_gewijzigd_en,
			be.websitetekst_gewijzigd_de,
			be.websitetekst_gewijzigd_nl,
			be.tekst_language
		FROM boeking_enquete be
		WHERE be.tekst_language is null
			and (be.websitetekst_gewijzigd is not null and be.websitetekst_gewijzigd != '')
			and be.boeking_id is not null
			and be.websitetekst_gewijzigd_en is null
			and be.websitetekst_gewijzigd_de is null
			and be.websitetekst_gewijzigd_nl is null");
update_translations('boeking_enquete', 'boeking_id', 'websitetekst_gewijzigd');

echo 'Review translations added: '.$updated_reviews.PHP_EOL.PHP_EOL;
if (count($errors)) {
	var_dump($errors);
}

function update_translations($table, $primary_key_column, $text_column){
	global $vars, $errors, $updated_reviews, $db, $db2, $google_translate;
	while($db->next_record()) {
		$update_rows = array();
		$sql_column_update = array();
		$id = $db->f($primary_key_column);
		echo 'Processing review id: '.$id.' from table '.$table.'... '.PHP_EOL;
		$text = $db->f($text_column);
		$detected_language = $google_translate->detect_language($text);
		if(!$detected_language) {
			$errors[] = $google_translate->get_last_error();
			$detected_language = lang_nl;
		}
		$update_rows["tekst_language"] = $detected_language;
		if(!in_array($detected_language, $vars["supported_languages"])) {
			$errors[] = 'Unsupported language detected for comment id: '.$id
				. ' language detected: '.$detected_language
				. ' text: '.$text
				. 'hard-codding dutch language ';
			$detected_language = lang_nl;
		}
		foreach(array_diff($vars["supported_languages"], array($detected_language)) as $language) {
			$translated_text = $google_translate->translate_text($detected_language, $language, $text);
			$translated_text = wt_utf8_decode($translated_text);

			if(!$translated_text) {
				$errors[] = $google_translate->get_last_error();
				continue;
			}
			$update_rows["websitetekst_gewijzigd_".$language] = $translated_text;
		}
		if(count($update_rows)) {
			foreach ($update_rows as $column => $value){
				$sql_column_update[] = $column.' = "'.$value.'" ';
			}
			$result = $db2->query('update '.$table.' set '.implode(', ', $sql_column_update).' where '.$primary_key_column.' = '.$id);
			if($result) {
				$updated_reviews++;
				echo 'review processed'.PHP_EOL;
			}
		}
	}
}