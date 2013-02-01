<?php

// create handle for new PDF document
$pdf = pdf_new();

function pdf_show_xy_right(&$pdf, $text, $right, $bottom) {
   $fontname = pdf_get_parameter($pdf, "fontname", 0);
   $font = pdf_findfont($pdf, $fontname, "host", 0);
   $size = pdf_get_value($pdf, "fontsize", 0);
   $width = pdf_stringwidth($pdf, $text, $font, $size);
   pdf_show_xy($pdf, $text, $right-$width, $bottom);
}

pdf_set_parameter($pdf,"FontOutline", "Tahoma==fonts/tahoma.ttf");
PDF_set_info($pdf, "Creator", "Chalet.nl B.V.");
PDF_set_info($pdf, "Author", "Chalet.nl B.V.");
PDF_set_info($pdf, "Title", "Factuur");

// open a file

$tempfile="tmp/temppdf_".time()."_".substr(md5(uniqid(rand(),true)),0,8).".pdf";
pdf_open_file($pdf, $tempfile);

pdf_begin_page($pdf, 595, 842);

$font = pdf_findfont($pdf, "Tahoma", "host",1);
pdf_setfont($pdf, $font, 10);


$image1=PDF_open_image_file($pdf, "gif", "pic/factuur_logo.gif","",0);
pdf_place_image($pdf,$image1,35,695,0.23);

// print text
pdf_show_xy_right($pdf,"Chalet.nl B.V.",545,810);
pdf_show_xy_right($pdf,"Wipmolenlaan 3",545,797);
pdf_show_xy_right($pdf,"3447 GJ Woerden",545,784);

pdf_show_xy_right($pdf,"Tel.: 0348 434649",545,760);
pdf_show_xy_right($pdf,"Fax: 0348 690752",545,747);
pdf_show_xy_right($pdf,"KvK nr. 30209634",545,734);

pdf_show_xy_right($pdf,"Bankrek. 84.93.06.671",545,710);
pdf_show_xy_right($pdf,"BTW NL-8169.23.462.B.01",545,697);

pdf_show_xy_right($pdf,"IBAN: NL21 ABNA 0849 3066 71",545,673);
pdf_show_xy_right($pdf,"BIC: ABNANL2A",545,660);
pdf_show_xy_right($pdf,"ABN AMRO Woerden",545,647);


#pdf_show_xy($pdf, "than are dreamt of in your philosophy", 50,730);
// end page
pdf_end_page($pdf);
// close and save file
pdf_close($pdf);

header('Content-type: application/pdf');
#header('Content-Disposition: attachment; filename="downloaded.pdf"');
readfile($tempfile);

unlink($tempfile);

?>