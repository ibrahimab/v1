<?php

#
# Toevoegen: 	
#
#	- automatische thankyou-mail (met eventueel alle ingevulde gegevens)
#	- mail sturen via mail-class
#	- elk field aanroepen met een eigen naam
#	- $form->output["naam"]
#	- $form->status:
#		- okay
#	- $form->done (array):
#		- mail
#	- bij declareren form nog niet opmaken (in HTML). Pas later, zodat extra controles mogelijk zijn.
#	- bij uploaden: aangeven wat voor soort bestand (ACCEPT=ContentTypes (media types for file upload))
#	- alles met CSS-classes vormgeven
#	- minder tabellen gebruiken (meer met style="padding..." werken)
#	- mogelijkheid velden te verbergen (zie ITIM Licensees: My account)
#	- mogelijkheid zelf HTML aan form toe te voegen (tussen 2 velden in bijvoorbeeld): form->html($content);
#	- mogelijkheid formulier helemaal zelf in te delen ($form->settings["createtable"]=false);
# 	- niet-ingevulde velden opsommen bij foutmelding
#	- 2 tabellen vullen met 1 form (voorbeeld: chalets en tarieven)

if (!function_exists("remove_magic_quotes")) {
	function remove_magic_quotes($vars,$suffix = '') {
		eval("\$vars_val =& \$GLOBALS[$vars]$suffix;");
		if (is_array($vars_val)) {
			reset($vars_val);
			while (list($key,$val) = each($vars_val)) remove_magic_quotes($vars,$suffix."[$key]");
		} else {
			$vars_val = stripslashes($vars_val);
			eval("\$GLOBALS$suffix = \$vars_val;");
		}
	}
}
if(get_magic_quotes_gpc() and !$magicquotesremoved) {
	remove_magic_quotes('_POST');
	remove_magic_quotes('_COOKIES');
	remove_magic_quotes('_GET');
	$magicquotesremoved=true;
}

Class Form {

function init() {
	global $db;
	# Standard values
#	$time=@file("http://www.postvak.net/time.php");
#	$this->time=$time[0];
	if(!$this->time) $this->time=time();
	if($_SERVER["QUERY_STRING"]) $this->query="?".$_SERVER["QUERY_STRING"];
	if(!$this->echooutput) $this->echooutput=false;
	if(!isset($this->echothankyoumessage)) $this->echothankyoumessage=false;
	if(!$this->formtablewidth) $this->formtablewidth="25%";
	if(!$this->fontface) $this->fontface="Verdana, Arial, Helvetica, sans-serif";
	if(!$this->fontsize) $this->fontsize="2";
	if(!$this->fontcolor) $this->fontcolor="";
	if(!isset($this->external_css)) $this->external_css=false;
	if(!$this->fonterrorcolor) $this->fonterrorcolor="red";
	if(!$this->numberquestions) $this->numberquestions=false;
	if(!isset($this->oblstars)) $this->oblstars=true;
	if(!isset($this->db["getfromdb"])) $this->db["getfromdb"]=true;
	if($this->language=="en") {
		@setlocale(LC_ALL,'en_EN');
		if(!$this->oblstarstext) $this->oblstarstext="Compulsory field";
#		if(!$this->oblerrormessage) $this->oblerrormessage="Not all fields are filled out correctly";
		if(!$this->errormessage["notokay"]) $this->errormessage["notokay"]="Not all fields are filled out correctly";
		if(!$this->errormessage["obl"]) {
			if($this->oblstars) {
				$this->errormessage["obl"]="Fields marked with an asterisk (*) are compulsory";
			} else {
				$this->errormessage["obl"]="Not all compulsory fields have been filled out";
			}
		}
#		if(!$this->errormessage["obl"]) $this->errormessage["obl"]="Fields marked with an asterisk (*) are compulsory";
		if(!$this->errormessage["email"]) $this->errormessage["email"]="Not a valid e-mail address";
		if(!$this->errormessage["nointeger"]) $this->errormessage["nointeger"]="Only numbers are allowed";
		if(!$this->errormessage["2decimals"]) $this->errormessage["2decimals"]="Don't use more than 2 decimals";
		if(!$this->errormessage["doubleselect"]) $this->errormessage["doubleselect"]="Each value must be unique";
		if(!$this->errormessage["onjuistedatum"]) $this->errormessage["onjuistedatum"]="wrong date";
		if(!$this->errormessage["dbkeyerror"]) $this->errormessage["dbkeyerror"]="Duplicate entry";
		if(!$this->errormessage["wrongfiletype"]) $this->errormessage["wrongfiletype"]="Wrong filetype";
		if(!$this->errormessage["wrongimagesize"]) $this->errormessage["wrongimagesize"]="Wrong image size";
		if(!$this->errormessage["abuse"]) $this->errormessage["abuse"]="Text not allowed to prevent spam abuse";
		if(!isset($this->send_button)) $this->send_button="OK";
		if(!$this->confirmmessage) $this->confirmmessage="Is this correct?";
		if(!$this->yesmessage) $this->yesmessage="YES";
		if(!$this->nomessage) $this->nomessage="NO";
	} else {
#		@setlocale(LC_ALL,'nl_NL');
		@setlocale(LC_ALL,"nl_NL.ISO_8859-1");
		if(!$this->oblstarstext) $this->oblstarstext="Verplicht veld";
#		if(!$this->oblerrormessage) $this->oblerrormessage="U heeft niet alle velden correct ingevuld";
		if(!$this->errormessage["notokay"]) $this->errormessage["notokay"]="U heeft niet alle velden correct ingevuld";
		if(!$this->errormessage["obl"]) {
			if($this->oblstars) {
				$this->errormessage["obl"]="Velden met een asterisk (*) zijn verplicht";
			} else {
				$this->errormessage["obl"]="Niet alle verplichte velden zijn ingevuld";
			}
		}
		if(!$this->errormessage["email"]) $this->errormessage["email"]="Geen geldig e-mailadres";
		if(!$this->errormessage["nointeger"]) $this->errormessage["nointeger"]="Alleen cijfers zijn toegestaan";
		if(!$this->errormessage["2decimals"]) $this->errormessage["2decimals"]="Gebruik maximaal 2 cijfers achter de komma";
		if(!$this->errormessage["doubleselect"]) $this->errormessage["doubleselect"]="Elke ingevulde waarde moet uniek zijn";
		if(!$this->errormessage["onjuistedatum"]) $this->errormessage["onjuistedatum"]="Onjuiste datum";
		if(!$this->errormessage["dbkeyerror"]) $this->errormessage["dbkeyerror"]="Dubbele invoer; record met deze gegevens bestaat reeds";
		if(!$this->errormessage["wrongfiletype"]) $this->errormessage["wrongfiletype"]="Onjuist bestandsformaat geupload";
		if(!$this->errormessage["wrongimagesize"]) $this->errormessage["wrongimagesize"]="De afbeelding heeft niet de juiste afmetingen";
		if(!$this->errormessage["abuse"]) $this->errormessage["abuse"]="Tekst niet toegestaan vanwege mogelijk spammisbruik";
		if(!isset($this->send_button)) $this->send_button="OK";
		if(!$this->confirmmessage) $this->confirmmessage="Zijn deze gegevens correct?";
		if(!$this->yesmessage) $this->yesmessage="JA";
		if(!$this->nomessage) $this->nomessage="NEE";
	}
	if(!$this->tablewidth) $this->tablewidth="450";
	if(!$this->tablestyle) $this->tablestyle=0;
	if(!$this->table_bgcolor) $this->table_bgcolor="#FFFFFF";
	if(!$this->field_bgcolor) $this->field_bgcolor="#FFFFFF";
	if(!$this->field_bordercolor) $this->field_bordercolor="#878481";
	if(!isset($this->showborder)) $this->showborder=true;
	if(!$this->table_cellpadding) $this->table_cellpadding=0;
	if(!isset($this->mail["towt"])) $this->mail["towt"]=true;
	if(!$this->mail["fromname"]) $this->mail["fromname"]="WebTastic FormMail";
	if(!$this->mail["fromaddress"]) $this->mail["fromaddress"]="formmail@webtastic.nl";
	if(!$this->mail["textheader"]) $this->mail["textheader"]="De volgende gegevens zijn via de website ingevoerd:";
	if(!$this->mail["bgcolor"]) $this->mail["bgcolor"]="#ebebeb";
	if(!$this->mail["textcolor"]) $this->mail["textcolor"]="black";
	if(!$this->mail["textcolorfieldnames"]) $this->mail["textcolorfieldnames"]="blue";
	if(!$this->mail["subject"]) $this->mail["subject"]="Invoer ".$this->name;
	if(!$this->mail["address"]) $this->mail["address"]="track@webtastic.nl";
	if(!$this->thankyoumessage) $this->thankyoumessage="Bedankt voor uw gegevens. Uw aanvraag zal zo spoedig mogelijk worden verwerkt.";
	if(!$this->uploadmessage) $this->uploadmessage="Bestand is reeds ontvangen";
	if($this->db["getfromdb"] and $this->db["keywherequery"] and isset($db)) {
		$db->query("SELECT * FROM ".$this->db["tablename"]." WHERE ".$this->db["keywherequery"].";");
		if($db->next_record()) {
			$fieldnames=$db->metadata($this->db["tablename"],true);
			while(list($key,$value)=each($fieldnames["meta"])) {
				$this->db["values"][$key]=$db->f($key);
			}
		}
	}
	$this->initdone=1;
}

function nospaces($name) {
	return ereg_replace("[ -]","_",strtolower($name));
}

function set($varname,$value) { 
	$this->$varname=$value;
}

function form_mailerror($message) {
	mail("track@webtastic.nl","Form-foutmelding ".$_SERVER["SERVER_NAME"],"\nOp ".$_SERVER["SERVER_NAME"]." is in het bestand ".$_SERVER[PHP_SELF]." de volgende foutmelding geconstateerd:\n\n".$message,"From: WebTastic Automail <automail@webtastic.nl>");
}

function echof($content) {
	$content=eregi_replace("€","&#8364;",$content);
	$this->htmlform=$this->htmlform.$content;
}

function startform() {
	if(!$this->external_css) {
?>
<style type="text/css"><!-- 

.formstyle100
{ <?php if($this->tablestyle) { ?>
BACKGROUND-COLOR: <?php echo $this->field_bgcolor ?>; 
BORDER-BOTTOM-COLOR: <?php echo $this->field_bordercolor ?>; 
BORDER-BOTTOM-STYLE: solid; 
BORDER-LEFT-COLOR: <?php echo $this->field_bordercolor ?>; 
BORDER-LEFT-STYLE: solid; 
BORDER-RIGHT-COLOR: <?php echo $this->field_bordercolor ?>; 
BORDER-RIGHT-STYLE: solid; 
BORDER-TOP-COLOR: <?php echo $this->field_bordercolor ?>; 
BORDER-TOP-STYLE: solid;
COLOR: #000000; <?php } ?>
font-family: <?php echo $this->fontface ?>;
font-size: <?php if($this->cssfontsize) echo $this->cssfontsize; elseif($this->fontsize=="2") echo "0.8em"; else echo "1.0em"; ?>;
width: 100%;
}

.formstyle
{ <?php if($this->tablestyle) { ?>
BACKGROUND-COLOR: <?php echo $this->field_bgcolor ?>; 
BORDER-BOTTOM-COLOR: <?php echo $this->field_bordercolor ?>; 
BORDER-BOTTOM-STYLE: solid; 
BORDER-LEFT-COLOR: <?php echo $this->field_bordercolor ?>; 
BORDER-LEFT-STYLE: solid; 
BORDER-RIGHT-COLOR: <?php echo $this->field_bordercolor ?>; 
BORDER-RIGHT-STYLE: solid; 
BORDER-TOP-COLOR: <?php echo $this->field_bordercolor ?>; 
BORDER-TOP-STYLE: solid;
COLOR: #000000; <?php } ?>
font-family:  <?php echo $this->fontface ?>;
font-size: <?php if($this->cssfontsize) echo $this->cssfontsize; elseif($this->fontsize=="2") echo "0.8em"; else echo "1.0em"; ?>;
}

.submitbutton {
font-family:  <?php echo $this->fontface ?>;
font-size: <?php if($this->cssfontsize) echo $this->cssfontsize; elseif($this->fontsize=="2") echo "0.8em"; else echo "1.0em"; ?>;
}

--></STYLE>

<?	}
	echo $this->textbeforeform."<FORM method=\"post\" action=\"".$_SERVER["PHP_SELF"].$this->query."#form\" name=\"".$this->nospaces($this->name)."\"";
	if(isset($this->uploadfield)) echo " enctype=\"multipart/form-data\"";
	echo "><A NAME=\"form\"></A>\n";
	if(isset($this->not_okay) and $_POST["filled"]==1 and !$this->dontdisplayerrors) {
		echo $this->font($this->fonterrorcolor).$this->errormessage["notokay"];
		ksort($this->not_okay);
		while(list($key,$value)=each($this->not_okay)) {
			if(!@in_array($value,$errormsgs) and $value<>"1") $errormsgs[]=$value;
		}
		if(is_array($errormsgs)) {
			echo ":<BR><TABLE cellspacing=\"0\" cellpadding=\"1\" border=\"0\">";
			while(list($key,$value)=each($errormsgs)) {
				echo "<TR><TD valign=\"top\" align=\"left\">".$this->font($this->fonterrorcolor)."-".$this->closefont()."</TD><TD>&nbsp;</TD><TD valign=\"top\" align=\"left\">".$this->font($this->fonterrorcolor).$value.$this->closefont()."</TD></TR>";
			}
			echo "</TABLE>";
		} else {
			echo ".";
		}
		echo "<P></FONT>";
	}
	if($this->showborder) {
		echo "\n<TABLE width=\"".$this->tablewidth."\" border=0 bgcolor=\"".$this->field_bordercolor."\" cellspacing=0 cellpadding=2><tr><td width=100% valign=middle>";
		echo "\n<TABLE width=100% cellspacing=0 cellpadding=10 bgcolor=\"".$this->table_bgcolor."\"><TR><TD>";
	}
	echo "\n<TABLE border=0 width=100% bgcolor=\"".$this->table_bgcolor."\" cellspacing=0 cellpadding=".$this->table_cellpadding."><TR><TD width=100% colspan=4>";
#	echo "<B>".$title."</B>";
	echo "<INPUT TYPE=\"hidden\" name=\"filled\" value=\"1\">";
	if(function_exists(sessionlink)) echo sessionlink(2);
	echo "</TD></TR>";
	if($this->top_sendbutton) {
		echo $this->submitbutton();
		echo "<TR><TD colspan=2>&nbsp;</TD></TR>";
	}
}

function stopform() {
	if($this->oblstars and $this->obl_counter>0) echo "<TR><TD colspan=2>&nbsp;</TD></TR><TR><TD colspan=2 align=left>".$this->font()."*&nbsp=&nbsp;".$this->oblstarstext.$this->closefont()."</TD></TR>";
	echo $this->submitbutton();
	if($this->showborder) {
		echo "</TD></TR></TABLE></TD></TR></TABLE>";
	}
	echo "</TD></TR></TABLE></FORM>";
}

function submitbutton() {
	$this->submitbuttoncounter++;
	$return="<TR><TD colspan=2 align=center>";
	$return.="<INPUT TYPE=\"submit\" value=\" ".$this->send_button." \" id=\"submit".$this->submitbuttoncounter.$this->nospaces($this->name)."\" onclick=\"document.".$this->nospaces($this->name).".submit".$this->submitbuttoncounter.$this->nospaces($this->name).".disabled=1;document.".$this->nospaces($this->name).".submit();\" class=\"submitbutton\">";
	if($this->extrabutton) $return.="&nbsp;&nbsp;".$this->extrabutton;
	$return.="</TD></TR>";
	return $return;
}

function font($color='') {
	if(!$this->cssfont or ($this->cssfont and $color)) {
		$this->fonttagopen=true;
		$return="<FONT";
		if(!$this->cssfont) $return.=" FACE=\"".$this->fontface."\" SIZE=\"".$this->fontsize."\"";
		if($this->fontcolor and !$color) $color=$this->fontcolor;
		if($color) $return=$return." COLOR=\"".$color."\"";
		$return=$return.">\n";
		return $return;
	}
}

function closefont() {
	if($this->fonttagopen) {
		$this->fonttagopen=false;
		return "</FONT>";		
	}
}

function fieldstart($type,$title,$dbfieldname='',$obl,$align=0,$spread=true,$totalwidth=0,$extrahtmltext='') {
#	global $filled;
	$this->counter++;
	$this->fieldnames[$this->counter]=$title;
	$this->fieldtype[$this->counter]=$type;
	$this->obl[$this->counter]=$obl;
	if($dbfieldname<>"dontsave") {
		if($dbfieldname) $this->dbfieldnames[$this->counter]=$dbfieldname; else $this->dbfieldnames[$this->counter]=$this->nospaces($title);
	}
	if($obl) $this->obl_counter++;
	if(!$this->initdone) $this->init();
	if($align==2) $this->echof("\n<TD nowrap>&nbsp;"); else {
		$this->echof("\n<TR".($this->tr["id"] ? " id=\"".$this->tr["id"]."\"" : "").($this->tr["style"] ? " style=\"".$this->tr["style"]."\"" : "")."><TD valign=top align=left");
		if($totalwidth) $this->echof(" colspan=2");
		$this->echof("><TABLE border=0 width=100% cellspacing=0 cellpadding=0>\n<TR><TD><IMG SRC=\"pic/leeg.gif\" alt=\"\" width=\"1\" height=\"4\"></TD></TR><TR><TD valign=bottom>");
	}
	if($_POST["filled"] and $type=="date") {
		if(!$_POST["input"][$this->counter]["day"] and !$_POST["input"][$this->counter]["month"] and !$_POST["input"][$this->counter]["year"]) {
			# Niks
		} else {
			if(!@checkdate($_POST["input"][$this->counter]["month"],$_POST["input"][$this->counter]["day"],$_POST["input"][$this->counter]["year"])) $this->not_okay[$this->counter]=$this->fieldnames[$this->counter].": ".$this->errormessage["onjuistedatum"];
		}
	}
	if($type=="upload") {
		if($_POST["input"][$this->counter]=="none") $_POST["input"][$this->counter]="";
	}
	if($_POST["filled"]) {
		# Controle op spammisbruik
		if(@eregi("Content-Type",$_POST["input"][$this->counter]) and @eregi("MIME",$_POST["input"][$this->counter])) {
			if(!$this->not_okay[$this->counter]) $this->not_okay[$this->counter]=$this->errormessage["abuse"];
		}
		
		if($obl) {
			if(!$_POST["input"][$this->counter] and $_POST["input"][$this->counter]<>"0") {
				if($align==2) {
					if(!$this->not_okay[$this->counter]) $this->not_okay[$this->counter]=1;
				} else {
					if(!$this->not_okay[$this->counter]) $this->not_okay[$this->counter]=$this->errormessage["obl"];
				}
			}
		}
		if($type=="email") {
			if(eregi("^[-_0-9a-z.&]*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,4}$",$_POST["input"][$this->counter],$regs)) {
				# Mailadres = okay
			} else {
				if($_POST["input"][$this->counter]) $this->not_okay[$this->counter]=$this->errormessage["email"];
			}
		} elseif($type=="date") {
			if(!$_POST["input"][$this->counter]["month"] and !$_POST["input"][$this->counter]["day"] and !$_POST["input"][$this->counter]["year"]) {
				if($obl) $this->not_okay[$this->counter]=$this->errormessage["obl"]; 
			} elseif(!$_POST["input"][$this->counter]["month"] or !$_POST["input"][$this->counter]["day"] or !$_POST["input"][$this->counter]["year"]) $this->not_okay[$this->counter]=$this->fieldnames[$this->counter].": ".$this->errormessage["onjuistedatum"];
		} elseif($type=="integer" or $type=="currency_nd") {
			if(ereg("[^0-9]",$_POST["input"][$this->counter]) and $_POST["input"][$this->counter]) $this->not_okay[$this->counter]=$this->errormessage["nointeger"];
		} elseif(($type=="currency" or $type=="percentage") and $_POST["input"][$this->counter]) {
			$_POST["input"][$this->counter]=ereg_replace(",",".",$_POST["input"][$this->counter]);
			if(ereg("[^0-9\.]",$_POST["input"][$this->counter])) {
				$this->not_okay[$this->counter]=$this->errormessage["nointeger"];
			} else {
				if(ereg("\.[0-9]{3,}",$_POST["input"][$this->counter])) $this->not_okay[$this->counter]=$this->errormessage["2decimals"];
			}
		}
	}
	if($type=="select" and $align==2) {
		# Niks	
	} else {
		if(($type=="currency" or $type=="currency_nd") and $align<>3) $this->echof("<TABLE width=100% cellspacing=0 cellpadding=0 border=0><TR><TD algin=\"left\">");
		if($this->not_okay[$this->counter] and $_POST["filled"]) $this->echof($this->font($this->fonterrorcolor)); elseif($type<>"yesno") $this->echof($this->font());
		if($this->numberquestions) $this->echof($this->counter.".&nbsp;");
		if($align<>3) {
			$title=ereg_replace("&lt;B&gt;","<B>",htmlentities($title));
			$title=ereg_replace("&lt;/B&gt;","</1B>",$title);
#			if($type=="currency") $this->echof("<TABLE width=100% cellspacing=0 cellpadding=0 border=0><TR><TD algin=\"left\">");
			if($this->td["left"]["id"]) $this->echof("<DIV id=\"".$this->td["left"]["id"]."\"".($this->td["left"]["style"] ? " style=\"".$this->td["left"]["style"]."\"" : "").">");
			$this->echof($title);
			if($this->td["left"]["id"]) $this->echof("</DIV>");
			if($obl and $this->oblstars) $this->echof("*");
			if($type=="currency" or $type=="currency_nd") $this->echof("</TD><TD valign=top align=\"right\">&nbsp;&nbsp;&euro;&nbsp;</TD></TR></TABLE>");
			if($align==2) $this->echof("&nbsp;");
			$this->echof($this->closefont());
			if($extrahtmltext) $this->echof($extrahtmltext);
			if($align<>2) $this->echof("</TD></TR></TABLE>");
			$this->echof("</TD>");
			if($totalwidth) {
				$this->echof("</TR>");
			} else {
				$this->echof("<TD valign=top align=left");
				if($align==2 and $spread) $this->echof(" width=70%"); else $this->echof(" width=65%");
				$this->echof(">");
				if($align==1) {
					$this->echof("<TABLE width=100% border=0 cellspacing=0 cellpadding=0>\n<TR><TD");
					if($spread) $this->echof(" width=70%");
					$this->echof(">");
				}
			}
		}
	}
}

function fieldstop($align=0) {
	if($align==1) {
		$this->echof("</TD>"); 
	} elseif($align==2) {
		$this->echof("</TD></TR></TABLE>\n</TD></TR>");
		$this->verticalspace();
	} elseif($align==3) {
		$this->echof("</TD></TR></TABLE>\n</TD></TR>");
		$this->verticalspace();
	} else {
		$this->echof("</TD></TR>");
		$this->verticalspace();
	}
}

function verticalspace() {
	$this->echof("<TR><TD colspan=2><IMG SRC=\"pic/leeg.gif\" width=\"1\" height=\"10\" alt=\"\"></TD></TR>");
}

function only_text($text,$center=false) {
	if(!$this->initdone) $this->init();
	if($center) $align=" align=\"center\"";
	$this->echof("<TR".($this->tr["id"] ? " id=\"".$this->tr["id"]."\"" : "").($this->tr["style"] ? " style=\"".$this->tr["style"]."\"" : "")."><TD colspan=2".$align."><TABLE border=0 cellspacing=0 cellpadding=0><TR><TD valign=bottom".$align.">".$this->font().$text.$this->closefont()."</TD></TR></TABLE></TD></TR>");
	$this->verticalspace();
}

function extra_submitbutton() {
	$this->echof($this->submitbutton());
}

function field_noedit($name,$value,$inoutput=false) {
	if(!$this->initdone) $this->init();
	$this->verticalspace();
	$this->echof("<TR".($this->tr["id"] ? " id=\"".$this->tr["id"]."\"" : "").($this->tr["style"] ? " style=\"".$this->tr["style"]."\"" : "")."><TD valign=\"top\"><TABLE border=0 cellspacing=0 cellpadding=0><TR><TD valign=bottom>".$this->font().$name.$this->closefont()."</TD></TR></TABLE></TD><TD><TABLE border=0 cellspacing=0 cellpadding=0><TR><TD valign=bottom>".$this->font().nl2br($value).$this->closefont()."</TD></TR></TABLE></TR>");
	$this->verticalspace();
	if($inoutput) {
		$this->counter++;
		$this->fieldnames[$this->counter]=$name;
#		$this->fieldvalues[$this->counter]=$value;
		$_POST["input"][$this->counter]=$value;
	}
}

function field_hidden($name,$value,$dbfieldname='',$inoutput=false) {
	if(!$this->initdone) $this->init();
	if($inoutput) {
		$this->counter++;
		$this->fieldnames[$this->counter]=$name;
		$_POST["input"][$this->counter]=$value;
		$showname="input[".$this->counter."]";
	} else {
		$showname=$name;
	}
	$this->echof("<INPUT TYPE=\"hidden\" name=\"".$showname."\" value=\"".$value."\">");
	if($dbfieldname) {
		$this->db["hiddenfield"][$dbfieldname]=$value;
	}
}

function field_onlyinoutput($name,$value,$dbfieldname='') {
	if(!$this->initdone) $this->init();
	$this->counter++;
	$this->fieldnames[$this->counter]=$name;
	$_POST["input"][$this->counter]=$value;
	$showname="input[".$this->counter."]";
	if($dbfieldname) {
		$this->db["hiddenfield"][$dbfieldname]=$value;
	}
}

function field_putindb($value,$dbfieldname) {
	# LET OP! $value en $key staan bij de functie field_putindb verkeerd om!
	if(!$this->initdone) $this->init();
	$this->counter++;
	if($dbfieldname) {
		$this->db["hiddenfield"][$dbfieldname]=$value;
	}
}

function picture($url,$center=false,$clickurl='',$alttext='',$target='') {
	if(!$this->initdone) $this->init();
	$this->picture[$picturecount]["url"]=$url;
	if($center) $align=" align=\"center\"";
	$this->echof("<TR><TD colspan=2".$align."><TABLE border=1 cellspacing=0 cellpadding=12><TR><TD valign=bottom".$align.">");
	if($clickurl) {
		$this->echof("<A HREF=\"".$clickurl."\"");
		if($target) $this->echof(" target=\"".$target."\"");
		$this->echof(">");
	}
	$this->echof("<IMG SRC=\"".$url."\"");
	if($alttext) $this->echof(" alt=\"".$alttext."\"");
	$this->echof(">");
	if($clickurl) $this->echof("</A>");
	$this->echof("</TD></TR></TABLE></TD></TR>");
}

function field_liketext($type='text',$title,$dbfieldname='',$totalwidth=0,$pretext='',$size=0,$maxlength=0,$spread=true,$align=0) {
#	global $filled;
	if(!$this->initdone) $this->init();
	if($type<>"password") {
		if(isset($this->db["values"][$dbfieldname])) $pretext=$this->db["values"][$dbfieldname]; elseif(isset($this->db["values"][$this->nospaces($title)])) $pretext=$this->db["values"][$this->nospaces($title)];
	}
	if($totalwidth) $this->echof("<TR><TD colspan=2>");
	if(!$_POST["filled"] and !$this->prevalues) $_POST["input"][$this->counter]=$pretext;
	$this->echof("<INPUT TYPE=\"".$type."\" name=\"input[".$this->counter."]\"");
	if($size) $this->echof(" SIZE=\"".$size."\"");
	if($maxlength) $this->echof(" MAXLENGTH=\"".$maxlength."\"");
	if($this->fieldtype[$this->counter]=="currency") $_POST["input"][$this->counter]=ereg_replace("\.",",",$_POST["input"][$this->counter]);
	$this->echof(" VALUE=\"".@htmlentities($_POST["input"][$this->counter])."\" class=\"formstyle");
	if($this->fieldtype[$this->counter]=="currency") $_POST["input"][$this->counter]=ereg_replace(",",".",$_POST["input"][$this->counter]);
	if($spread) $this->echof("100");
	$this->echof("\">");
	$this->fieldstop($align);
}

function field_text($obl=0,$title,$dbfieldname='',$pretext='',$align=0,$size=0,$spread=true,$totalwidth=0,$maxlength=0) {
#	global $filled;
	$this->fieldstart("text",$title,$dbfieldname,$obl,$align,$spread,$totalwidth);
	$this->field_liketext("text",$title,$dbfieldname,$totalwidth,$pretext,$size,$maxlength,$spread,$align);
}

function field_integer($obl=0,$title,$dbfieldname='',$pretext='',$align=0,$size=0,$spread=true,$totalwidth=0,$maxlength=0) {
#	global $filled;
	$this->fieldstart("integer",$title,$dbfieldname,$obl,$align,$spread,$totalwidth);
	$this->field_liketext("text",$title,$dbfieldname,$totalwidth,$pretext,$size,$maxlength,$spread,$align);
}

function field_currency($obl=0,$title,$dbfieldname='',$pretext='',$align=0,$size=0,$spread=true,$totalwidth=0,$maxlength=0) {
#	global $filled;
	$this->fieldstart("currency",$title,$dbfieldname,$obl,$align,$spread,$totalwidth);
	$this->field_liketext("text",$title,$dbfieldname,$totalwidth,$pretext,$size,$maxlength,$spread,$align);
}

function field_percentage($obl=0,$title,$dbfieldname='',$pretext='',$align=0,$size=0,$spread=true,$totalwidth=0,$maxlength=0) {
#	global $filled;
	$this->fieldstart("percentage",$title,$dbfieldname,$obl,$align,$spread,$totalwidth);
	$this->field_liketext("text",$title,$dbfieldname,$totalwidth,$pretext,$size,$maxlength,$spread,$align);
}

function field_currency_nd($obl=0,$title,$dbfieldname='',$pretext='',$align=0,$size=0,$spread=true,$totalwidth=0,$maxlength=0) {
#	global $filled;
	$this->fieldstart("currency_nd",$title,$dbfieldname,$obl,$align,$spread,$totalwidth);
	$this->field_liketext("text",$title,$dbfieldname,$totalwidth,$pretext,$size,$maxlength,$spread,$align);
}

function field_password($obl=0,$title,$dbfieldname='',$pretext='',$align=0,$size=0,$spread=true,$totalwidth=0,$maxlength=0) {
#	global $filled;
	$this->fieldstart("password",$title,$dbfieldname,$obl,$align,$spread,$totalwidth,'',$minlength);
	$this->field_liketext("password",$title,$dbfieldname,$totalwidth,$pretext,$size,$maxlength,$spread,$align);
}

function field_email($obl=0,$title,$dbfieldname='',$pretext='',$align=0,$size=0,$spread=true,$totalwidth=0,$maxlength=0) {
#	global $filled;
	$this->fieldstart("email",$title,$dbfieldname,$obl,$align,$spread,$totalwidth);
	$this->field_liketext("text",$title,$dbfieldname,$totalwidth,$pretext,$size,$maxlength,$spread,$align);
}

function field_select($obl=0,$title,$dbfieldname='',$select,$preselect=0,$emptyfield=0,$totalwidth=0,$align=0,$displayplusurl='') {
	if($displayplusurl=="1") {
		$displayplusurl="&nbsp;&nbsp;<A HREF=\"".$displayplusurl."\">+</A>";
	}
	$this->fieldstart("select",$title,$dbfieldname,$obl,$align,'',$totalwidth,$displayplusurl);
	if($this->db["values"][$dbfieldname]) {
		$preselect=$this->db["values"][$dbfieldname]; 
	} elseif($this->db["values"][$this->nospaces($title)]) {
		$preselect=$this->db["values"][$this->nospaces($title)];
	}
	if($totalwidth) $this->echof("<TR><TD colspan=2>");
	$this->fieldvalues[$this->counter]=$select;
	$this->echof("<SELECT name=\"input[".$this->counter."]\" class=\"formstyle");
	if($align<>2 and $align<>1) $this->echof("100");
	$this->echof("\">\n");
	if($emptyfield) {
		$this->echof("<OPTION VALUE=\"\"");
		if((!$_POST["filled"] and !$preselect) or ($_POST["filled"] and !$_POST["input"][$this->counter])) $this->echof(" selected");
		$this->echof("></OPTION>");
	}
	if(is_array($select)) {
		while(list($key,$value) = each($select)) {
			$this->echof("<OPTION VALUE=\"".$key."\"");
			if($_POST["input"][$this->counter]==$key or (!$_POST["filled"] and !$this->prevalues and $preselect==$key)) $this->echof(" selected");
			$this->echof(">".htmlentities($value)."</OPTION>\n");
		}
	}
	$this->echof("</SELECT>");
	$this->fieldstop($align);
}

function field_multipleselect($obl=0,$title,$dbfieldname='',$select,$preselect=0,$emptyfield=0,$totalwidth=0,$align=0,$displayplusurl='') {
	# Emptyfield staat altijd uit!!
	$emptyfield=0;
	if($displayplusurl=="1") {
		$displayplusurl="&nbsp;&nbsp;<A HREF=\"".$displayplusurl."\">+</A>";
	}
	$this->fieldstart("multipleselect",$title,$dbfieldname,$obl,$align,'',$totalwidth,$displayplusurl);
	if($this->db["values"][$dbfieldname]) {
		$preselect=$this->db["values"][$dbfieldname]; 
	} elseif($this->db["values"][$this->nospaces($title)]) {
		$preselect=$this->db["values"][$this->nospaces($title)];
	}
	if(ereg("[-,]",$preselect)) $preselect=split("[,-]",$preselect);
	if($totalwidth) $this->echof("<TR><TD colspan=2>");
	$this->fieldvalues[$this->counter]=$select;
	$this->echof("<SELECT name=\"input[".$this->counter."][]\" multiple class=\"formstyle");
	if($align<>2 and $align<>1) $this->echof("100");
	$this->echof("\">\n");
	if($emptyfield) {
		$this->echof("<OPTION VALUE=\"\"");
		if((!$_POST["filled"] and !$preselect) or ($_POST["filled"] and !$_POST["input"][$this->counter])) $this->echof(" selected");
		$this->echof("></OPTION>");
	}
	if(is_array($select)) {
#		$tempcounter=0;
		while(list($key,$value) = each($select)) {
			$this->echof("<OPTION VALUE=\"".$key."\"");
			if(@in_array($key,$_POST["input"][$this->counter]) or (!$_POST["filled"] and !$this->prevalues and (@in_array($key,$preselect) or $preselect==$key))) $this->echof(" selected");
			$this->echof(">".htmlentities($value)."</OPTION>\n");
#			$tempcounter++;
		}
	}
	$this->echof("</SELECT>");
	$this->fieldstop($align);
}


function field_radio($obl=0,$title,$dbfieldname='',$select,$preselect=0,$undereachother=0,$totalwidth=0) {
#	global $filled;
	if(is_array($select)) {
		$this->fieldstart("radio",$title,$dbfieldname,$obl,0,'',$totalwidth);
		if($this->db["values"][$dbfieldname]) $preselect=$this->db["values"][$dbfieldname]; elseif($this->db["values"][$this->nospaces($title)]) $preselect=$this->db["values"][$this->nospaces($title)];
		if($totalwidth) $this->echof("<TR><TD colspan=2>");
		$this->fieldvalues[$this->counter]=$select;
		if($undereachother) $this->echof("<TABLE border=0 cellspacing=0 cellpadding=0><TR><TD><TABLE border=0>"); else $this->echof($this->font());
		while(list($key,$value) = each($select)) {
			if($undereachother) $this->echof("<TR><TD valign=top align=left>");
			$this->echof("<INPUT TYPE=\"radio\" name=\"input[".$this->counter."]\" VALUE=\"".$key."\" id=\"".$this->counter.$key."\"");
			if((($_POST["filled"] or $this->prevalues) and $_POST["input"][$this->counter]==$key) or (!$_POST["filled"] and $preselect==$key)) $this->echof(" checked");
			$this->echof(">");
			if($undereachother) $this->echof("</TD><TD valign=top align=left>".$this->font());
			$this->echof("<LABEL FOR=\"".$this->counter.$key."\">".$value."</LABEL>");
			if($undereachother) $this->echof($this->closefont()."</TD></TR>"); else $this->echof("&nbsp;&nbsp;");
		}
		if($undereachother) $this->echof("</TABLE></TD></TR></TABLE>"); else $this->echof($this->closefont());
		$this->fieldstop();
	}
}

function field_checkbox($obl=0,$title,$dbfieldname='',$select,$preselect=0,$undereachother=0,$totalwidth=0) {
	if(is_array($select)) {
		if(ereg("[-,]",$preselect)) $preselect=split("[,-]",$preselect);
		$this->fieldstart("checkbox",$title,$dbfieldname,$obl,0,'',$totalwidth);
		if($this->db["values"][$dbfieldname]) $preselect=split("[,-]",$this->db["values"][$dbfieldname]); elseif($this->db["values"][$this->nospaces($title)]) $preselect=split("[,-]",$this->db["values"][$this->nospaces($title)]);
		if($totalwidth) $this->echof("<TR><TD colspan=2>");
		$this->fieldvalues[$this->counter]=$select;
		if($undereachother) $this->echof("<TABLE border=0 cellspacing=0 cellpadding=0><TR><TD><TABLE border=0 cellspacing=0 cellpadding=0>"); else $this->echof($this->font());
		while(list($key,$value)=each($select)) {
			if($undereachother) $this->echof("<TR><TD valign=top align=left>");
			$this->echof("<INPUT TYPE=\"checkbox\" name=\"input[".$this->counter."][".$key."]\" id=\"".$this->counter.$key."\"");
			if($_POST["filled"] and $_POST["input"][$this->counter][$key]=="on") {
				$this->echof(" checked");
			} elseif(!$_POST["filled"]) {
				if(is_array($preselect)) {
					if(in_array($key,$preselect) or $preselect[$key]=="on") {
						$this->echof(" checked");
					}
				} elseif($preselect==$key) {
					$this->echof(" checked");
				}
			}
			$this->echof(">");

			if($undereachother) $this->echof("</TD><TD valign=middle align=left>&nbsp;".$this->font());
			$this->echof("<LABEL FOR=\"".$this->counter.$key."\">".$value."</LABEL>");
			if($undereachother) $this->echof($this->closefont()."</TD></TR>"); else $this->echof("&nbsp;&nbsp;");
		}
#		if($undereachother) $this->echof($this->closefont()."</TD></TR>"); else $this->echof("&nbsp;&nbsp;");
		if($undereachother) $this->echof("</TABLE></TD></TR></TABLE>"); else $this->echof($this->closefont());
		$this->fieldstop();
	}
}

function field_checkbox_yesno($title,$dbfieldname='',$preselect=0,$javascriptfunction="") {
	$this->fieldstart("yesno",$title,$dbfieldname,$obl,3,'',1);
	if($this->db["values"][$dbfieldname]) $preselect=$this->db["values"][$dbfieldname]; elseif($this->db["values"][$this->nospaces($title)]) $preselect=$this->db["values"][$this->nospaces($title)];
	$this->echof("<TABLE border=0 cellspacing=0 cellpadding=0><TR><TD valign=top align=left><INPUT TYPE=\"checkbox\" name=\"input[".$this->counter."]\" VALUE=\"1\" id=\"yesno".$this->counter."\"");
	if(($preselect==1 and !$_POST["filled"]) or $_POST["input"][$this->counter]==1) $this->echof(" checked");
	if($javascriptfunction) $this->echof(" onclick=\"".$javascriptfunction."\"");
	$this->echof("></TD><TD valign=middle align=left>");
	$this->echof($this->font()."<LABEL FOR=\"yesno".$this->counter."\">&nbsp;".$title."</LABEL>".$this->closefont()."</TD></TR></TABLE>");
	$this->fieldstop(3);
}

function field_textarea($obl=0,$title,$dbfieldname='',$prevalue='',$rows=5,$cols=40,$totalwidth=0) {
	$this->fieldstart("textarea",$title,$dbfieldname,$obl,'','',$totalwidth);
	if($this->db["values"][$dbfieldname]) $prevalue=$this->db["values"][$dbfieldname]; elseif($this->db["values"][$this->nospaces($title)]) $prevalue=$this->db["values"][$this->nospaces($title)];
	if(!$_POST["filled"] and $prevalue) $_POST["input"][$this->counter]=$prevalue;
	if($totalwidth) $this->echof("<TR><TD colspan=2>");
	$this->echof("<TEXTAREA name=\"input[".$this->counter."]\" ROWS=\"".$rows."\" COLS=\"".$cols."\" class=\"formstyle100\">");
	$this->echof(htmlentities($_POST["input"][$this->counter])."</TEXTAREA>");
	$this->fieldstop();
}

function field_date($obl=0,$title,$dbfieldname='',$unixtime=0,$day,$month,$year,$spread=true,$totalwidth=0,$startyear=0,$endyear=0,$align=0,$calendar=false,$time="no",$extra="") {
#	global $filled;
	$this->fieldstart("date",$title,$dbfieldname,$obl,$align,$spread,$totalwidth);
	if($this->db["values"][$dbfieldname]) $unixtime=$this->db["values"][$dbfieldname]; elseif($this->db["values"][$this->nospaces($title)]) $unixtime=$this->db["values"][$this->nospaces($title)];
	$months[0]="";
	for($i=1;$i<13;$i++) {
		$months[$i]=strftime("%B",mktime(0,0,0,$i,1,2004));
	}
	if(!$_POST["filled"] and !$this->prevalues and $unixtime) {
		$_POST["input"][$this->counter]["day"]=date("j",$unixtime);
		$_POST["input"][$this->counter]["month"]=date("n",$unixtime);
		$_POST["input"][$this->counter]["year"]=date("Y",$unixtime);
		if($time<>"no") {
			$_POST["input"][$this->counter]["hour"]=date("G",$unixtime);
			$_POST["input"][$this->counter]["minute"]=date("i",$unixtime);
		}
	}
	if(!$_POST["filled"] and !$this->prevalues and $day) $_POST["input"][$this->counter]["day"]=$day;
	if(!$_POST["filled"] and !$this->prevalues and $month) $_POST["input"][$this->counter]["month"]=$month;
	if(!$_POST["filled"] and !$this->prevalues and $year) $_POST["input"][$this->counter]["year"]=$year;
	if($totalwidth) $this->echof("<TR><TD colspan=2>");
	$this->echof("<TABLE border=0 cellspacing=0 cellpadding=0><TR>");
	if($calendar) $this->echof("<TD><A HREF=\"#\" onClick=\"window.open('calendar.php?lang=".$this->language."&input=".$this->counter."&month='+document.forms[0].elements['input[".$this->counter."][month]'].value+'&year='+document.forms[0].elements['input[".$this->counter."][year]'].value, '_blank', 'scrollbars=no,width=345,height=250,left=0,top=0');\"><IMG SRC=\"pic/calendar.gif\" border=\"0\" width=\"16\" height=\"15\" alt=\"Kalender\"></A></TD><TD><IMG SRC=\"pic/leeg.gif\" alt=\"\" width=\"5\" height=\"1\"></TD>");
	$this->echof("<TD><SELECT name=\"input[".$this->counter."][day]\" ".($extra["day_onfocus"] ? "onfocus=\"".$extra["day_onfocus"]."\" " : "").($extra["day_onchange"] ? "onchange=\"".$extra["day_onchange"]."\" " : "")."class=\"formstyle\">");
	$this->echof("<OPTION></OPTION>");
	for($i=1;$i<32;$i++) {
		$this->echof("<OPTION value=\"".$i."\"");
		if($_POST["input"][$this->counter]["day"]==$i) $this->echof(" selected");
		$this->echof(">".$i."</OPTION>\n");
	}
	$this->echof("</SELECT>&nbsp;</TD>");
	$this->echof("<TD><SELECT name=\"input[".$this->counter."][month]\" ".($extra["month_onfocus"] ? "onfocus=\"".$extra["month_onfocus"]."\" " : "").($extra["month_onchange"] ? "onchange=\"".$extra["month_onchange"]."\" " : "")."class=\"formstyle\">");
	while(list($key,$value) = each($months)) {
		$this->echof("<OPTION value=\"".$key."\"");
		if($_POST["input"][$this->counter]["month"]==$key) $this->echof(" selected");
		$this->echof(">".$value."</OPTION>\n");
	}
	$this->echof("</SELECT>&nbsp;</TD>");
	$this->echof("<TD><SELECT name=\"input[".$this->counter."][year]\" ".($extra["year_onfocus"] ? "onfocus=\"".$extra["year_onfocus"]."\" " : "").($extra["year_onchange"] ? "onchange=\"".$extra["year_onchange"]."\" " : "")."class=\"formstyle\">");
	$this->echof("<OPTION></OPTION>");
	if(!$startyear) $startyear=date("Y");
	if(!$endyear) $endyear=date("Y")+5;
	if($unixtime) {
		if($startyear>date("Y",$unixtime)) $startyear=date("Y",$unixtime);
		if($endyear<date("Y",$unixtime)) $endyear=date("Y",$unixtime);
	}
	if($startyear<$endyear) {
		for($i=$startyear;$i<=$endyear;$i++) {
			$this->echof("<OPTION value=\"".$i."\"");
			if($_POST["input"][$this->counter]["year"]==$i) $this->echof(" selected");
			$this->echof(">".$i."</OPTION>\n");
		}
	} else {
		for($i=$startyear;$i>=$endyear;$i-=1) {
			$this->echof("<OPTION value=\"".$i."\"");
			if($_POST["input"][$this->counter]["year"]==$i) $this->echof(" selected");
			$this->echof(">".$i."</OPTION>\n");
		}
	}
	$this->echof("</SELECT></TD>");
	if($time=="texttime") {
		$this->echof("<TD>&nbsp;&nbsp;<INPUT TYPE=\"text\" name=\"input[".$this->counter."][hour]\" value=\"".htmlentities($_POST["input"][$this->counter]["hour"])."\" size=\"2\" maxlength=\"2\" ".($extra["hour_onblur"] ? " onblur=\"".$extra["hour_onblur"]."\" " : "")."class=\"formstyle\">:<INPUT TYPE=\"text\" name=\"input[".$this->counter."][minute]\" value=\"".htmlentities($_POST["input"][$this->counter]["minute"])."\" size=\"2\" maxlength=\"2\" class=\"formstyle\"></TD>");

	}
	$this->echof("</TR></TABLE>");
	$this->fieldstop($align);
}

function field_upload($obl=0,$title,$movefileto='',$renamefileto='',$mustbefiletype='',$dbcountafterfilename=false,$align=0,$spread=true,$totalwidth=0,$width=0,$height=0) {
#	global $_FILES;
	if($_FILES["input"]["name"][$this->counter+1]) {
		$filename=split('\.',$_FILES["input"]["name"][$this->counter+1]);
	} elseif($_POST["input"]["name"][$this->counter+1]) {
		$filename=split('\.',$_POST["input"]["name"][$this->counter+1]);
	}
	if($filename) {
		$aantal=count($filename);
		$extension=strtolower($filename[$aantal-1]);
		if($mustbefiletype) {
			$mustbefiletypearray=split(",",$mustbefiletype);
			if(!in_array($extension,$mustbefiletypearray)) $this->not_okay[$this->counter+1]=$this->errormessage["wrongfiletype"];
		}
	}
	if($_FILES["input"]["tmp_name"][$this->counter+1] and $_FILES["input"]["tmp_name"][$this->counter+1]<>"none") {
		if(file_exists("tmp/")) $this->uploadfilename[$this->counter+1]="tmp/phpupload".md5(uniqid(rand())); else $this->uploadfilename[$this->counter+1]="/tmp/phpupload".md5(uniqid(rand()));;
		move_uploaded_file($_FILES["input"]["tmp_name"][$this->counter+1],$this->uploadfilename[$this->counter+1]);
		if($width and $height) {
			# Image-afmetingen checken
			$size=getimagesize($this->uploadfilename[$this->counter+1]);
			if($size[0]<>$width or $size[1]<>$height) $this->not_okay[$this->counter+1]=$this->errormessage["wrongimagesize"];
		}
	}
	if($_POST["filled"] and $_POST["input"]["tmp_name"][$this->counter+1] and $obl) $obl=0;
	$this->fieldstart("upload",$title,$dbfieldname,$obl,$align,$spread,$totalwidth);
	if($totalwidth) $this->echof("<TR><TD colspan=2>");
	$this->uploadfield[$this->counter]=1;
	if($dbcountafterfilename) $this->$dbcountafterfilename[$this->counter]=1;
	if($movefileto) {
		if($renamefileto) {
			$newfilename=trim($renamefileto);
			$this->uploadnewfilenameextension[$this->counter]=".".$extension;
		} else {
			$newfilename=$_POST["input"]["name"][$this->counter];
		}
		$this->uploadnewfilename[$this->counter]=$newfilename;
		$this->uploadnewfiledir[$this->counter]=$movefileto;
	}
	if(!$this->not_okay[$this->counter] and $_FILES["input"]["tmp_name"][$this->counter]<>"none" and $_FILES["input"]["tmp_name"][$this->counter]<>"") {
		$_POST["input"]["tmp_name"][$this->counter]=$_FILES["input"]["tmp_name"][$this->counter];
		$_POST["input"]["type"][$this->counter]=$_FILES["input"]["type"][$this->counter];
		$_POST["input"]["name"][$this->counter]=$_FILES["input"]["name"][$this->counter];
	} elseif($_POST["input"]["tmp_name"][$this->counter]) {
		$this->uploadfilename[$this->counter]=$_POST["input"]["tmp_name"][$this->counter];
	}
	if($_POST["filled"] and $_POST["input"]["tmp_name"][$this->counter]) {
		$this->echof("<TABLE cellspacing=0 cellpadding=0><TR><TD>".$this->font().$this->uploadmessage.$this->closefont()."</TD></TR></TABLE>");
		$this->echof("<INPUT TYPE=\"hidden\" name=\"input[tmp_name][".$this->counter."]\" VALUE=\"".$this->uploadfilename[$this->counter]."\">");
		$this->echof("<INPUT TYPE=\"hidden\" name=\"input[type][".$this->counter."]\" VALUE=\"".$_POST["input"]["type"][$this->counter]."\">");
		$this->echof("<INPUT TYPE=\"hidden\" name=\"input[name][".$this->counter."]\" VALUE=\"".$_POST["input"]["name"][$this->counter]."\">");
	} else {
		$this->echof("<INPUT TYPE=\"file\" name=\"input[".$this->counter."]\"");
		$this->echof(" class=\"formstyle");
		if($spread) $this->echof("100");
		$this->echof("\">");
	}
	$this->fieldstop($align);
}

function button_extra($title,$onclick) {
	$this->extrabutton="<INPUT TYPE=\"button\" value=\" ".$title." \" onclick=\"".$onclick."\" class=\"submitbutton\">";
}

function showheader($text) {
	$this->headertext.=$text;
}

function showfooter($text) {
	$this->footertext.=$text;
}

#function prevalues($array) {
#	global $_POST;
#	$this->prevalues=1;
#	while(list($key,$value) = each($array)) {
#		$_POST["input"][$key]=$value;
#	}
#}

function querystring($name,$value) {
	if($name and ($value or $value=="0")) {
		if($this->query) $this->query.="&".$name."=".$value; else $this->query="?".$name."=".$value;
		return true;
	} else {
		return false;
	}
}

function dissectpostvars() {
	$var=$_POST;
	if(is_array($var)) {
		while(list($key,$value) = each($var)) {
			if(is_array($value)) {
				while(list($key2,$value2) = each($value)) {
					if(is_array($value2)) {
						while(list($key3,$value3) = each($value2)) {
							echo "<INPUT TYPE=\"hidden\" NAME=\"".$key."[".$key2."][".$key3."]\" VALUE=\"".htmlentities($value3)."\">";
						}
					} else {
						echo "<INPUT TYPE=\"hidden\" NAME=\"".$key."[".$key2."]\" VALUE=\"".htmlentities($value2)."\">";
					}
				}
			} else {
				echo "<INPUT TYPE=\"hidden\" NAME=\"".$key."\" VALUE=\"".htmlentities($value)."\">";
			}
		}
	}
}

#
# Nieuwe functies (ter test!!)
# Handleinput
function handleinput() {
	global $db,$php_errormsg,$SESSION;
	if($_POST["filled"] and !isset($this->not_okay) and $_POST["confirm"]<>"2") {
		$this->cssfonttemp=$this->cssfont;
		$this->cssfont=false;
		$this->formstatus="okay";
		$this->output="<TABLE width=\"600\" border=\"1\" bordercolor=\"#878481\" cellspacing=\"0\" cellpadding=\"5\">\n";
		$this->output.="<TR><TD width=\"".$this->formtablewidth."\" valign=top align=left>".$this->font($this->mail["textcolorfieldnames"])."<B>Formulier</B></FONT></TD><TD valign=top align=left>".$this->font().ucfirst($this->name)."</TD></TR>\n";
		$this->output.="<TR><TD width=\"".$this->formtablewidth."\" valign=top align=left>".$this->font($this->mail["textcolorfieldnames"])."<B>Ingevuld op</B></FONT></TD><TD valign=top align=left>".$this->font().date("j-n-Y, G:i",$this->time)."u.</TD></TR>\n";
		while(list($name,$value)=each($this->fieldnames)) {
			if($this->fieldtype[$name]<>"upload") {
				if($this->fieldtype[$name]=="yesno") {
					if($_POST["input"][$name]==1) {
						$this->output.="<TR><TD colspan=2 valign=top align=left>".$this->font($this->mail["textcolorfieldnames"])."<B>".ucfirst($this->fieldnames[$name])."</B>\n";
					}
				} elseif($this->fieldtype[$name]<>"password") {
					$this->output.="<TR><TD width=\"".$this->formtablewidth."\" valign=top align=left>".$this->font($this->mail["textcolorfieldnames"])."<B>".ucfirst($this->fieldnames[$name])."</B></FONT></TD><TD valign=top align=left>".$this->font()."\n";
				}
				if(is_array($_POST["input"][$name])) {
					if(isset($_POST["input"][$name]["day"])) {
						if($_POST["input"][$name]["day"]>0 and $_POST["input"][$name]["month"]>0 and $_POST["input"][$name]["year"]>0) {
							$_POST["input"][$name]["unixtime"]=mktime($_POST["input"][$name]["hour"],$_POST["input"][$name]["minute"],0,$_POST["input"][$name]["month"],$_POST["input"][$name]["day"],$_POST["input"][$name]["year"]);
							$this->output.=$_POST["input"][$name]["day"]."-".$_POST["input"][$name]["month"]."-".$_POST["input"][$name]["year"];
							if($this->db["save"] and $this->dbfieldnames[$name]) {
								if($insertquery) $insertquery.=", ".$this->dbfieldnames[$name]."='".addslashes($_POST["input"][$name]["unixtime"])."'"; else $insertquery=$this->dbfieldnames[$name]."='".addslashes($_POST["input"][$name]["unixtime"])."'";
							}
						} elseif(!$this->obl[$name]) {
							if($this->db["save"] and $this->dbfieldnames[$name]) {
								if($insertquery) $insertquery.=", ".$this->dbfieldnames[$name]."='".addslashes($_POST["input"][$name]["unixtime"])."'"; else $insertquery=$this->dbfieldnames[$name]."='".addslashes($_POST["input"][$name]["unixtime"])."'";
							}
						}
					} else {
						unset($printbreak,$insertquery2);
						reset($_POST["input"][$name]);
						while(list($name2,$value2) = each($_POST["input"][$name])) {
							# Indien multipleselect: waarden omdraaien
							if($this->fieldtype[$name]=="multipleselect") {
								$tempname2=$name2;
								$name2=$value2;
								$value2=$tempname2;
							}
							$name2=addslashes($name2);
							if($this->db["save"]) {
								if($insertquery2) $insertquery2.=",".$name2.""; else $insertquery2="".$name2."";
							}
							if($printbreak) $this->output=$this->output."<BR>";
							$this->output=$this->output.$this->fieldvalues[$name][$name2];
							$printbreak=1;
						}
						if($this->db["save"] and $this->dbfieldnames[$name]) {
							if($insertquery) $insertquery.=", ".$this->dbfieldnames[$name]."='".$insertquery2."'"; else $insertquery=$this->dbfieldnames[$name]."='".$insertquery2."'";
						}
					}
				} else {
					if($this->fieldtype[$name]<>"yesno") {
						if($this->fieldvalues[$name][$_POST["input"][$name]]) {
							$this->output=$this->output.htmlentities($this->fieldvalues[$name][$_POST["input"][$name]]); 
						} elseif($this->fieldtype[$name]=="password" and $_POST["input"][$name]) {
							$_POST["input"][$name]=md5($_POST["input"][$name]);
						} else {
							# E-mail klikbaar maken
							$tempoutput=eregi_replace("([^a-z0-9]|^)([0-9a-z][-_0-9a-z.]*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,4})([^a-z]|$)","\\1<A HREF=\"mailto:\\2\">\\2</A>\\4",htmlentities($_POST["input"][$name]));
							# http-links klikbaar maken
							$tempoutput=eregi_replace("(http://[^[:blank:]]+)","<A HREF=\"\\1\" target=\"_blank\">\\1</A>",$tempoutput);
							$this->output=$this->output.nl2br($tempoutput);
						}
					}
					if($this->db["save"] and $this->dbfieldnames[$name]) {
						if($this->fieldtype[$name]<>"password" or ($this->fieldtype[$name]=="password" and $_POST["input"][$name])) {
						


#
#
# TEST !!!
#
#
$tempisquery=$_POST["input"][$name];
$tempisquery=ereg_replace("[“”]","\"",$tempisquery);
$tempisquery=ereg_replace("’","'",$tempisquery);
$tempisquery=ereg_replace("–","-",$tempisquery);
$tempisquery=addslashes($tempisquery);
							if($insertquery) $insertquery.=", ".$this->dbfieldnames[$name]."='".$tempisquery."'"; else $insertquery=$this->dbfieldnames[$name]."='".$tempisquery."'";
						}
					}
				}
				if(!$this->input[$this->fieldnames[$name]]) $this->input[$this->fieldnames[$name]]=$_POST["input"][$name];
			}
			$this->output=$this->output."&nbsp;</FONT></TD></TR>\n";
		}
		$this->output=$this->output."</TABLE><BR>";
		$this->cssfont=$this->cssfonttemp;
		if($this->askconfirm and $_POST["confirm"]==0) {
			echo "<FORM METHOD=\"post\" name=\"confirmform\" action=\"".$_SERVER["PHP_SELF"];
			if($_SERVER["QUERY_STRING"]) echo "?".$_SERVER["QUERY_STRING"];
			echo "\">";
			$this->dissectpostvars();
			echo "<INPUT TYPE=\"hidden\" name=\"confirm\" value=\"\">";
			echo $this->output.$this->font().$this->confirmmessage;
			echo "<BR><INPUT TYPE=\"button\" VALUE=\" ".$this->yesmessage." \" onclick=\"document.forms.confirmform.confirm.value='1';submit();\">&nbsp;<INPUT TYPE=\"button\" VALUE=\" ".$this->nomessage." \" onclick=\"document.forms.confirmform.confirm.value='2';submit();\">";
			if($this->stopmessage and $this->clickback) {
				echo "&nbsp;<INPUT TYPE=\"button\" VALUE=\" ".$this->stopmessage." \" onclick=\"document.location='".$this->clickback."';\">";
			}
			echo "</FORM><BR>";
		} else {
			if($this->mail["theform"]) {
				$this->sendemail();
			}
			if($this->db["save"] and isset($db)) {
				if(is_array($this->db["hiddenfield"])) {
					while(list($key,$value) = each($this->db["hiddenfield"])) {
						$value=addslashes($value);
						if($insertquery) $insertquery.=", ".$key."='".$value."'"; else $insertquery=$key."='".$value."'";
					}
				}
				if(is_array($this->db["setcurrenttime"])) {
					while(list($key,$value) = each($this->db["setcurrenttime"])) {
						if($insertquery) $insertquery.=", ".$key."='".$this->time."'"; else $insertquery=$key."='".$this->time."'";
					}
				}
				if($this->db["keywherequery"]) {
					$db->query("UPDATE ".$this->db["tablename"]." SET ".$insertquery." WHERE ".$this->db["keywherequery"].";");
					$this->insertid=$this->db["keywherequeryid"];
				} else {
					$db->query("INSERT INTO ".$this->db["tablename"]." SET ".$insertquery.";");
					$this->insertid=$db->insert_id();
					if($db->Errno=="1062") {
						$this->not_okay[$this->counter+10]=$this->errormessage["dbkeyerror"];
					}
				}
			}
			if(is_array($this->uploadnewfilename)) {
				while(list($key,$value)=each($this->uploadnewfilename)) {
					unset($php_errormsg);
					if($this->uploadfilename[$key]) {
						copy($this->uploadfilename[$key],$this->uploadnewfiledir[$key].$this->insertid.$this->uploadnewfilename[$key].$this->uploadnewfilenameextension[$key]);
						unlink($this->uploadfilename[$key]);
					}
					if($php_errormsg) $this->form_mailerror($php_errormsg);
				}
			}
			if($this->gotopage and !$this->not_okay) {
				$this->formstatus="gotopage";
				if($this->addinsertid==1) $this->gotopage.="?insertid=".$this->insertid;
				if($this->addinsertid==2) $this->gotopage.="&insertid=".$this->insertid;
				header("Location: ".$this->gotopage);
#				echo"<HTML><HEAD><META HTTP-EQUIV=Refresh CONTENT=\"0; URL=".$this->gotopage."\"></HEAD><BODY>\n";
#				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
#				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
#				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
#				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
#				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
#				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
#				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
#				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
#				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
#				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
#				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
#				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
#				echo "</BODY></HTML>";
				exit;
			} elseif($this->thankyoumessage and !$this->not_okay) {
#				echo "<A NAME=\"form\"></A>";
#				if($this->includeheader) include($this->includeheader);
#				if($this->headertext) echo $this->headertext;
#				if($this->echooutput) echo $this->output;
#				if($this->echomessage) echo $this->font().$this->thankyoumessage."<P>";
#				if($this->clickback and $this->clickbackmessage) echo "<A HREF=\"".$this->clickback."\">".$this->clickbackmessage."</A><BR>";
#				echo "</FONT>";
#				if($this->footertext) echo $this->footertext;
#				if($this->includefooter) include($this->includefooter);
			}
		}
	}
}

function sendemail() {
	global $allfuntions_version;
	$header="<HTML><HEAD></HEAD><BODY BGCOLOR=\"".$this->mail["bgcolor"]."\" text=\"".$this->mail["textcolor"]."\">\n".$this->font().$this->mail["textheader"]."<P>\n";
	$footer=$this->mail["textfooter"]."<P></BODY></HTML>\n";
	$subject=$this->mail["subject"];
	$body=eregi_replace("</TD>","</TD>\n",$header.$this->output.$footer);
	$htmlheader="Content-Type: text/html; charset=iso-8859-1\n";
	if(is_array($this->uploadfilename)) {
		$mime_boundary = "--==================_846811060==_";
		$htmlheader="--".$mime_boundary."\n".$htmlheader;
		$mimeheader="MIME-version: 1.0\nContent-type: multipart/mixed; boundary=\"$mime_boundary\"\nContent-transfer-encoding: 7BIT\n\n";
		while(list($key,$value)=each($this->uploadfilename)) {
			$mimetype=$_POST["input"]["type"][$key];
			$mime_filename=$_POST["input"]["name"][$key];;
#			$sourcefile=$_POST["input"]["tmp_name"][$key];
			$sourcefile=$value;
			$body.="--" . $mime_boundary . "\nContent-type: " . $mimetype . "; name=\"$mime_filename\";\nContent-Transfer-Encoding: base64\nContent-disposition: attachment; filename=\"$mime_filename\"\n\n";
			if (@is_readable($sourcefile)) {
				$fd = @fopen($sourcefile, "r");
				$contents = @fread($fd, filesize($sourcefile));
				$body.=@chunk_split(base64_encode($contents));
				@fclose($fd);	
			}
			@unlink($sourcefile);
		}
		$body.="\n"."--".$mime_boundary."--"."\n";
	}
	$mailheader="X-Mailer: WebTastic FormMail - www.webtastic.nl\nX-Originating-IP: ".$_SERVER["REMOTE_ADDR"]."\n".($_SERVER["REMOTE_HOST"] ? "X-Originating-Host: ".$_SERVER["REMOTE_HOST"]."\n" : "").($_SERVER["HTTP_USER_AGENT"] ? "X-Originating-User-Agent: ".$_SERVER["HTTP_USER_AGENT"]."\n" : "")."X-Originating-Request-URI: http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."\n".($extraheaders ? $extraheaders."\n" : "")."From: ".$this->mail["fromname"]." <".$this->mail["fromaddress"].">\n";
	$mailheader.=$mimeheader.$htmlheader;
	if(class_exists("wt_mail") and $allfuntions_version>=2) {
		$mail=new wt_mail;
		$mail->fromname=$this->mail["fromname"];
		$mail->from=$this->mail["fromaddress"];
#		$mail->toname="Jeroen";
		$mail->to=$this->mail["address"];
		$mail->subject=$subject;
		$mail->html=$body;
		$mail->send();
	} else {
		if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" and !eregi("@webtastic",$this->mail["address"])) $this->mail["address"]="formtest@webtastic.nl";
	#	if($_SERVER["REMOTE_ADDR"]=="131.211.226.172") {
	#		mail("jeroen@webtastic.nl",$subject,$body,"Bcc: track@webtastic.nl\n".$mailheader);
	#	} else {
			if($_SERVER["HTTPS"]<>"on") $bcc="Bcc: track@webtastic.nl\n"; else $bcc="";
			mail($this->mail["address"],$subject,$body,$bcc.$mailheader);
	#	}
	}
}

#
# Functie: displayform
function displayform() {
	global $db,$php_errormsg,$SESSION;
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		if(!$this->name) echo "<FONT size=3 color=red><B>Foutmelding: het formulier heeft geen naam!</B></FONT><BR>";
	}
	if(!$_POST["filled"] or isset($this->not_okay) or $_POST["confirm"]=="2" or $this->gotoform) {
		if($this->gotoform) {
			if(!$this->formstatus) $this->formstatus="form";
		}
		if($this->includeheader) include($this->includeheader);
		if($this->headertext) echo $this->headertext;
		$this->startform();
		echo $this->htmlform;
		$this->stopform();
		if($this->footertext) echo $this->footertext;
		if($this->includefooter) include($this->includefooter);
	} elseif($this->echothankyoumessage) {
		echo "<A NAME=\"form\"></A>";
		if($this->includeheader) include($this->includeheader);
#		if($this->headertext) echo $this->headertext;
		if($this->echooutput) echo $this->output;
		if($this->echothankyoumessage) echo $this->font().$this->thankyoumessage."<P>";
		if($this->clickback and $this->clickbackmessage) echo "<A HREF=\"".$this->clickback."\">".$this->clickbackmessage."</A><BR>";
		echo "</FONT>";
#		if($this->footertext) echo $this->footertext;
		if($this->includefooter) include($this->includefooter);
	}
}

}
?>