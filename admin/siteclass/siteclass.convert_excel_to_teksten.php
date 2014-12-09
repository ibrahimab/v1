<?php

/**
* load Excel with translations, convert to PHP and write to content/_teksten.php
*/
class convert_excel_to_teksten
{


	public $language = "de";
	public $insert_before_language = "en";

	function __construct()
	{

	}

	function convert() {
		$this->read_teksten();
		$this->read_excel();
		$this->write_teksten();


		echo "Uitkomst1:".nl2br(wt_he($this->content[1]));
		echo "Uitkomst2:".nl2br(wt_he($this->content[2]));
		exit;
	}

	function read_teksten() {
		global $vars;

		$this->content[1] = file_get_contents($vars["unixdir"]."content/_teksten.php");
		$this->content[2] = file_get_contents($vars["unixdir"]."content/_teksten_intern.php");

	}

	function write_teksten() {
		file_put_contents($vars["unixdir"]."content/_teksten_de.php", $this->content[1]);
		file_put_contents($vars["unixdir"]."content/_teksten_intern_de.php", $this->content[2]);
	}

	function convert_to_php($page, $part, $translation) {

		if($page=="site_breed") {
			$return = "\$txta[\"".$this->language."\"][\"".$part."\"]=\"".str_replace('"', '\\"', $translation)."\";";
		} else {
			$return = "\$txt[\"".$this->language."\"][\"".$page."\"][\"".$part."\"]=\"".str_replace('"', '\\"', $translation)."\";";
		}
		return $return;
	}

	function paste_in_teksten($page, $part, $php) {
		foreach ($this->content as $key => $value) {

			if($page=="site_breed") {
				$this->content[$key] = str_replace("\$txta[\"".$this->insert_before_language."\"][\"".$part."\"]", $php."\n\$txta[\"".$this->insert_before_language."\"][\"".$part."\"]", $this->content[$key]);
			} else {
				$this->content[$key] = str_replace("\$txt[\"".$this->insert_before_language."\"][\"".$page."\"][\"".$part."\"]", $php."\n\$txt[\"".$this->insert_before_language."\"][\"".$page."\"][\"".$part."\"]", $this->content[$key]);
			}

			$this->teller++;
		}
	}

	function read_excel() {

		$objPHPExcel = PHPExcel_IOFactory::load("/tmp/duits.xls");

		date_default_timezone_set('UTC');

		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
			$worksheetTitle     = $worksheet->getTitle();
			$highestRow         = $worksheet->getHighestRow(); // e.g. 10
			$highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			$nrColumns = ord($highestColumn) - 64;


			$xfIndex = $worksheet->getCell('A2')->getXfIndex();

			// $worksheet->getCellByColumnAndRow(1,1)->setValue("Filiaal");
			// $worksheet->getCellByColumnAndRow(2,1)->setValue("Plaats filiaal");

			for ($row = 1; $row <= $highestRow; ++ $row) {
				$page = wt_utf8_decode($worksheet->getCellByColumnAndRow(0,$row)->getValue());
				$part = wt_utf8_decode($worksheet->getCellByColumnAndRow(1,$row)->getValue());
				$dutch = wt_utf8_decode($worksheet->getCellByColumnAndRow(2,$row)->getValue());
				$translation = wt_utf8_decode($worksheet->getCellByColumnAndRow(3,$row)->getValue());


				// echo wt_he($page." - ".$part." - ".$dutch." - ".$translation)."<br />";

				$php = $this->convert_to_php($page, $part, $translation);
				$this->paste_in_teksten($page, $part, $php);

			}
		}
	}
}


?>