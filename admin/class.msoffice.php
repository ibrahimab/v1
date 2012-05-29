<?php

class ms_office {

	function ms_office() {
		return true;
		$this->filename="document";
		$this->margin="70.85pt 70.85pt 70.85pt 70.85pt;";
		$this->landscape=false;
	}

	function page_break() {
#		$return="<br clear=all style='mso-special-character:line-break;page-break-before:always'>";
		$return="<p style=\"page-break-before:always\">&nbsp;</p>";
		return $return;
	}

	function create_word_document() {
	if(!$this->test) {
		$this->filename=strtolower($this->filename);
		$this->filename=preg_replace("/[^a-z0-9]/","_",$this->filename);
		header("Content-type: application/msword");
		header("Content-Disposition: attachment; filename=".$this->filename.".doc");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	}
	 ?><html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=ProgId content=Word.Document>
<meta name=Generator content="Microsoft Word 10">
<meta name=Originator content="Microsoft Word 10">
<!--[if !mso]>
<style>
v\:* {behavior:url(#default#VML);}
o\:* {behavior:url(#default#VML);}
w\:* {behavior:url(#default#VML);}
.shape {behavior:url(#default#VML);}
</style>
<![endif]-->
<title><?php echo $this->title; ?></title>
<!--[if gte mso 9]><xml>
 <o:DocumentProperties>
  <o:Author><?php echo $this->author; ?></o:Author>
  <o:Company><?php echo $this->company; ?></o:Company>
 </o:DocumentProperties>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:WordDocument>
  <w:View>Print</w:View>
  <w:GrammarState>Clean</w:GrammarState>
  <w:HyphenationZone>21</w:HyphenationZone>
  <w:Compatibility>
   <w:ApplyBreakingRules/>
   <w:UseFELayout/>
  </w:Compatibility>
  <w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>
 </w:WordDocument>
</xml><![endif]-->
<style>
<!--
 /* Font Definitions */
 @font-face
	{font-family:"MS Mincho";
	panose-1:2 2 6 9 4 2 5 8 3 4;
	mso-font-alt:"\FF2D\FF33 \660E\671D";
	mso-font-charset:128;
	mso-generic-font-family:modern;
	mso-font-pitch:fixed;
	mso-font-signature:-1610612033 1757936891 16 0 131231 0;}
@font-face
	{font-family:Verdana;
	panose-1:2 11 6 4 3 5 4 4 2 4;
	mso-font-charset:0;
	mso-generic-font-family:swiss;
	mso-font-pitch:variable;
	mso-font-signature:536871559 0 0 0 415 0;}
@font-face
	{font-family:"\@MS Mincho";
	panose-1:2 2 6 9 4 2 5 8 3 4;
	mso-font-charset:128;
	mso-generic-font-family:modern;
	mso-font-pitch:fixed;
	mso-font-signature:-1610612033 1757936891 16 0 131231 0;}
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{mso-style-parent:"";
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:Verdana;
	mso-fareast-font-family:"Times New Roman";
	mso-bidi-font-family:"Times New Roman";
	mso-fareast-language:NL;}
@page Section1 {
<?php if($this->landscape) { ?>
	size:841.9pt 595.3pt;
	mso-page-orientation:landscape;
<?php } else { ?>
	size:595.3pt 841.9pt;
<?php } ?>
	margin:<?php echo $this->margin; ?>;
	mso-header-margin:35.4pt;
	mso-footer-margin:35.4pt;
	mso-paper-source:0;}
div.Section1
	{page:Section1;}
-->
</style>
<?php if(!$this->test) echo "<!--[if gte mso 10]>"; ?>
<style>
 /* Style Definitions */
 table.MsoNormalTable
	{mso-style-name:Standaardtabel;
	mso-tstyle-rowband-size:0;
	mso-tstyle-colband-size:0;
	mso-style-noshow:yes;
	mso-style-parent:"";
	mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
	mso-para-margin:0cm;
	mso-para-margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:"Times New Roman";}
table.MsoTableSimple1
	{mso-style-name:"Eenvoudige tabel 1";
	mso-tstyle-rowband-size:0;
	mso-tstyle-colband-size:0;
	border-top:solid green 1.5pt;
	border-left:none;
	border-bottom:solid green 1.5pt;
	border-right:none;
	mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
	mso-para-margin:0cm;
	mso-para-margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:"Times New Roman";
	mso-fareast-font-family:"MS Mincho";}
table.MsoTableSimple1FirstRow
	{mso-style-name:"Eenvoudige tabel 1";
	mso-table-condition:first-row;
	mso-tstyle-border-top:1.5pt solid #CC3333;
	mso-tstyle-border-bottom:.75pt solid #CC3333;}
table.MsoTableSimple1LastRow
	{mso-style-name:"Eenvoudige tabel 1";
	mso-table-condition:last-row;
	mso-tstyle-border-top:.75pt solid #CC3333;
	mso-tstyle-border-left:cell-none;
	mso-tstyle-border-bottom:1.5pt solid #CC3333;
	mso-tstyle-border-right:cell-none;
	mso-tstyle-diagonal-down:cell-none;
	mso-tstyle-diagonal-up:cell-none;
	mso-tstyle-border-insideh:cell-none;
	mso-tstyle-border-insidev:cell-none;}
<?php if(!$this->test) echo "-->"; ?>

</style>
<?php if($this->css) echo "<style>\n".$this->css."\n</style>\n"; ?>
</head>

<body lang=NL style='tab-interval:35.4pt'>

<div class=Section1>

<?php echo $this->html; ?>

</div>

</body>

</html><?php }

} ?>