<?php

$mustlogin=true;

include("admin/vars.php");

$form=new form2("frm");
$form->settings["fullname"]="vertalingen";
$form->settings["layout"]["css"]=false;
$form->settings["message"]["submitbutton"]["nl"]="VERSTUREN";

if($_GET["taal"]) {
	$vertaal_taal = $_GET["taal"];
	$doorloop_array=array("");
} else {
	$vertaal_taal = "en";
	$doorloop_array=array("","v", "z", "i"); # alleen Vallandry-afwijkingen moeten in het Engels vertaald worden
}

while(list($afwijkingkey,$afwijkingvalue)=each($doorloop_array)) {
	if($afwijkingvalue) {
		$afwijking="_".$afwijkingvalue;
	} else {
		$afwijking="";
	}

	while(list($key,$value)=each($txta["nl".$afwijking])) {
		if(!$txta[$vertaal_taal.$afwijking][$key] and $value) {

			$vertaal_array["site_breed"][$key] = $value;

			// $form->field_htmlrow("","<b>Site-brede tekst &quot;".$key."&quot;</b><p><div style=\"width:676px\"><i>Nederlands:</i><br><div style=\"border:1px solid #000000;;padding:5px;\">".nl2br(wt_he($value))."</div></div>");
			// $form->field_textarea(0,ereg_replace("-","_",$key)."_1","<i>".wt_he($vars["vertaal_talen"][$vertaal_taal])." (txta[".$key."])</i>","","","",array("newline"=>true,"title_html"=>true));
			// $form->field_htmlrow("","<hr>");
			$vars["onvertaald"]=true;
		}
	}

	while(list($key,$value)=each($txt["nl".$afwijking])) {
		while(list($key2,$value2)=each($value)) {
			// echo $key."=".$key2."===".$value2."<br/>";
			if(!$txt[$vertaal_taal.$afwijking][$key][$key2] and $value2) {

				$vertaal_array[$key][$key2] = $value2;

				// $form->field_htmlrow("","<b>Pagina &quot;".($key=="vars" ? "algemeen" : ($key=="index" ? "hoofdpagina" : $key))."&quot; - onderdeel &quot;".$key2."&quot;</b><p><div style=\"width:676px\"><i>Nederlands:</i><br><div style=\"border:1px solid #000000;padding:5px;\">".nl2br(wt_he($value2))."</div></div>");
				// $form->field_textarea(0,ereg_replace("-","_",$key).ereg_replace("-","_",$key2).$afwijkingvalue."_2","<i>".wt_he($vars["vertaal_talen"][$vertaal_taal])." (txt[".$key."][".$key2."])</i>","","","",array("newline"=>true,"title_html"=>true));
				// $form->field_htmlrow("","<hr>");
				$vars["onvertaald"]=true;
			}
		}
	}
}
if(is_array($nieuwe_vertaling)) {
	while(list($key,$value)=each($nieuwe_vertaling[$vertaal_taal])) {
		while(list($key2,$value2)=each($value)) {
			if(!$txt[$vertaal_taal.$afwijking][$key][$key2] and $value2) {

				$vertaal_array[$key][$key2] = $value2;

				// $form->field_htmlrow("","<b>Pagina &quot;".($key=="vars" ? "algemeen" : ($key=="index" ? "hoofdpagina" : $key))."&quot; - onderdeel &quot;".$key2."&quot;</b><p><div style=\"width:676px\"><i>Nederlands:</i><br><div style=\"border:1px solid #000000;padding:5px;\">".nl2br(wt_he($txt["nl"][$key][$key2]))."</div></div>");
				// $form->field_textarea(0,ereg_replace("-","_",$key).ereg_replace("-","_",$key2)."_3","<i>".wt_he($vars["vertaal_talen"][$vertaal_taal])." (txt[".$key."][".$key2."])</i>","","","",array("newline"=>true,"title_html"=>true));
				// $form->field_htmlrow("","<hr>");
				$vars["onvertaald"]=true;
			}
		}
	}
}




if(is_array($vertaal_array)) {
	if($_GET["download"]) {
		//
		// download translations as Excel-file
		//

		$objPHPExcel = new \PHPExcel();

		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->SetCellValue("A1", "Pagina");
		$objPHPExcel->getActiveSheet()->SetCellValue("B1", "Onderdeel");
		$objPHPExcel->getActiveSheet()->SetCellValue("C1", "Nederlands");
		$objPHPExcel->getActiveSheet()->SetCellValue("D1", $vars["vertaal_talen"][$vertaal_taal]);

		$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(70);
		$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(70);

		$objPHPExcel->getActiveSheet()->getStyle("A1:D1")->getFont()->setBold(true);

		$row = 1;
		foreach ($vertaal_array as $key => $value) {
			foreach ($value as $key2 => $value2) {
				$row++;

				$objPHPExcel->getActiveSheet()->SetCellValue("A".$row, wt_utf8_encode($key));
				$objPHPExcel->getActiveSheet()->SetCellValue("B".$row, wt_utf8_encode($key2));
				$objPHPExcel->getActiveSheet()->SetCellValue("C".$row, wt_utf8_encode($value2));

				// ->getStyle("D$row")->getAlignment()->setWrapText(true);

			}
		}

		// wrap text
		$objPHPExcel->getActiveSheet()->getStyle("C1:C".$objPHPExcel->getActiveSheet()->getHighestRow())
		    ->getAlignment()->setWrapText(true);

		// vertical align: top
	    $objPHPExcel->getActiveSheet()->getStyle("A1:D".$objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);


	    // write as Excel 5
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="vertalingen_'.strtolower($vars["vertaal_talen"][$vertaal_taal]).'-'.date("Y-m-d").'.xls"');
		header("X-Robots-Tag: noindex, nofollow", true);
		$objWriter->save('php://output');
		exit;
	} else {

		foreach ($vertaal_array as $key => $value) {

			foreach ($value as $key2 => $value2) {

				if($key=="site_breed") {
					// $titel_html = "Site-brede tekst &quot;".$key."&quot;";
					$form->field_htmlrow("","<b>Site-brede tekst &quot;".$key2."&quot;</b><p><div style=\"width:676px\"><i>Nederlands:</i><br><div style=\"border:1px solid #000000;;padding:5px;\">".nl2br(wt_he($value2))."</div></div>");
					$form->field_textarea(0,ereg_replace("-","_",$key2)."_1","<i>".wt_he($vars["vertaal_talen"][$vertaal_taal])." (txta[".$key2."])</i>","","","",array("newline"=>true,"title_html"=>true));
				} else {
					// $titel_html = "Pagina &quot;".($key=="vars" ? "algemeen" : ($key=="index" ? "hoofdpagina" : $key))."&quot; - onderdeel &quot;".$key2."&quot;";
					$form->field_htmlrow("","<b>Pagina &quot;".($key=="vars" ? "algemeen" : ($key=="index" ? "hoofdpagina" : $key))."&quot; - onderdeel &quot;".$key2."&quot;</b><p><div style=\"width:676px\"><i>Nederlands:</i><br><div style=\"border:1px solid #000000;padding:5px;\">".nl2br(wt_he($txt["nl"][$key][$key2]))."</div></div>");
					$form->field_textarea(0,ereg_replace("-","_",$key).ereg_replace("-","_",$key2)."_3","<i>".wt_he($vars["vertaal_talen"][$vertaal_taal])." (txt[".$key."][".$key2."])</i>","","","",array("newline"=>true,"title_html"=>true));
				}
				$form->field_htmlrow("","<hr>");
			}
		}
	}
}

$form->check_input();

if($form->okay) {
	$form->mail("jeroen@webtastic.nl","","Vertalingen Chalet.nl");
}
$form->end_declaration();

$layout->display_all($cms->page_title);

?>