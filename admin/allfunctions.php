<?php

$allfuntions_version=2;


# voorkomen dat er externe scripts worden ge-include
@ini_set('allow_url_include','0');

# register_globals handmatig uitzetten
if($_GET["wt_register_globals_on"]) unset($wt_register_globals_on);
if(ini_get('register_globals') and !$wt_register_globals_on) {
	@reset($_GET);
	while(list($key,$val) = @each($_GET)) {
		unset($$key);
	}
	@reset($_GET);
	@reset($_POST);
	while(list($key,$val) = @each($_POST)) {
		unset($$key);
	}
	@reset($_POST);
	@reset($_COOKIE);
	while(list($key,$val) = @each($_COOKIE)) {
		unset($$key);
	}
	@reset($_COOKIE);
}

# magic quotes verwijderen
if (!function_exists("remove_magic_quotes")) {
	function remove_magic_quotes($vars,$suffix = '') {
		@eval("\$vars_val =& \$GLOBALS[$vars]$suffix;");
		if (is_array($vars_val)) {
			reset($vars_val);
			while(list($key,$val)=each($vars_val)) remove_magic_quotes($vars,$suffix."[$key]");
			reset($vars_val);
		} else {
			$vars_val=stripslashes($vars_val);
			@eval("\$GLOBALS$suffix = \$vars_val;");
		}
	}
}

if(get_magic_quotes_gpc() and !$magicquotesremoved and $NU_EVEN_NIET) {
	// uitgezet omdat chalet.eu (met mod_fcgid) een fout genereerde met deze functie
	remove_magic_quotes('_POST');
	@reset($_POST);
	remove_magic_quotes('_COOKIE');
	@reset($_COOKIE);
	remove_magic_quotes('_GET');
	$magicquotesremoved=true;
	@reset($_GET);
}

if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
	# Zorgen dat errors lokaal getoond worden
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);
}

# Error handling
if($vars["wt_disable_error_handler"] or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/extern-html" or ($_SERVER["USER"]=="root" and ereg("\.postvak\.net$",$_SERVER["HOSTNAME"])) or (defined("wt_test") and constant("wt_test") === true)) {
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" and $_SERVER["HTTP_HOST"]=="ss.postvak.net") {
		# Lokaal
#		@set_error_handler('LocalErrorHandler');
	}
} else {
	# Online
	@set_error_handler('errorHandler');
	if(file_exists("404.php")) {
		@error_reporting(E_ERROR|E_PARSE);
		@ini_set('display_errors','1');
		@ini_set('html_errors',false);
		@ini_set('error_prepend_string','<html><head><META http-equiv="refresh" content="0;URL=/404.php?wtfatalerror=');
		@ini_set('error_append_string','"></head></html>');
	}
	$GLOBALS["wt_error_handler"]=true;
}

function errorHandler($errno,$errstr,$errfile,$errline,$errcontext) {
	if(!isset($GLOBALS["errorcounter"])) $GLOBALS["errorcounter"]=0;
	if(error_reporting()<>0 and $GLOBALS["errorcounter"]<=4 and $errno<>8 and $errno<>2048 and $errno<>8192) {
		if($_ENV["LOGNAME"] and $_SERVER["SCRIPT_FILENAME"]) {
			$script=$_SERVER["SCRIPT_FILENAME"];
		}
		if(preg_match("/simplexml_load_file/",$errstr)) {
			$GLOBALS["errorcounterfunction"]["simplexml"]++;
			if($GLOBALS["errorcounterfunction"]["simplexml"]>1) $nietopslaan=true;
		}

		# MySQL-server te druk: touch tekstbestand
		if(preg_match("/Too many connections/",$errstr) or preg_match("/Lock wait timeout exceeded/",$errstr) or preg_match("/locks exceeds the lock table size/",$errstr)) {
			@touch($GLOBALS["vars"]["unixdir"]."tmp/mysql_too_busy.txt");
			$mysql_connection_error=true;
		}

		// php-session-errors veroorzaakt door hack-poging niet opslaan
		if(preg_match("@open.*sess_.*O_RDWR.*failed.*Permission denied@",$errstr) and $errno==2) {
			$nietopslaan=true;
		}
		if(preg_match("@Failed to write session data.*Please verify that the current setting of session\.save_path is correct@",$errstr) and $errno==2) {
			$nietopslaan=true;
		}
		if(preg_match("@Unknown: write failed: No space left on device@",$errstr) and $errno==2) {
			$nietopslaan=true;
		}


		if(preg_match("/pconnect/",$errstr) or preg_match("/next_record/",$errstr) or preg_match("/lost mysql connection/",$errstr) or preg_match("/Lock wait timeout exceeded/",$errstr) or preg_match("/locks exceeds the lock table size/",$errstr) or preg_match("/MySQL server has gone away/",$errstr) or  preg_match("/Lost connection to MySQL server during query/",$errstr) or preg_match("/Deadlock found when trying to get lock/",$errstr)) {

			if($GLOBALS["vars"]["wt_error_handler_mysql_connect_error_hide"]) {
				// als $vars["wt_error_handler_mysql_connect_error_hide"] aan staat, MySQL-connect-errors niet loggen
				$nietopslaan=true;

			} else {

				$GLOBALS["errorcounterfunction"]["mysql"]++;
				$mysql_connection_error=true;

				# MySQL-connectiefouten: max 1x per 15 minuten loggen
				if(@filemtime($GLOBALS["vars"]["unixdir"]."tmp/mysql_connection_error.txt")<time()-900) {
					@touch($GLOBALS["vars"]["unixdir"]."tmp/mysql_connection_error.txt");
					if($GLOBALS["errorcounterfunction"]["mysql"]>1) $nietopslaan=true;
				} else {
					$nietopslaan=true;
				}
			}
		}

		if(!$nietopslaan) {
			# gegevens uit _WT_FILENAME_ en _WT_LINENUMBER_ filteren
			if(preg_match("/_WT_FILENAME_(.*)_WT_FILENAME__WT_LINENUMBER_(.*)_WT_LINENUMBER_(.*)$/",$errstr,$regs)) {
				$errfile=$regs[1];
				$errline=$regs[2];
				$errstr=$regs[3];
			}

			# MySQL-foutmeldingen als "_notice" versturen
			if($GLOBALS["vars"]["wt_error_handler_mysql_notice"] and $mysql_connection_error) {
				$errstr="_notice: ".$errstr;
			}

			$url="http".($_SERVER["HTTPS"]=="on" ? "s" : "")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
			$fp=@fopen("http://owp.webtastic.nl/error_log.php?l=".urlencode($errline)."&n=".urlencode($errno)."&f=".urlencode($errfile)."&u=".urlencode($url)."&s=".urlencode($errstr)."&r=".urlencode($_SERVER["HTTP_REFERER"])."&i=".urlencode($_SERVER["REMOTE_ADDR"])."&sc=".urlencode($script),"r");
			$GLOBALS["errorcounter"]++;
		}
	}
}

function LocalErrorHandler($errno,$errstr,$errfile,$errline,$errcontext) {
switch ($errno) {
	case E_USER_ERROR:
		echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
		echo "  Fatal error on line $errline in file $errfile";
		echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
		echo "Aborting...<br />\n";
		exit(1);
		break;

	case E_USER_WARNING:
		echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
		break;

	case E_USER_NOTICE:
		echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
		break;

	default:
		echo "Unknown error type: [$errno] $errstr<br />\n";
		break;
	}

	/* Don't execute PHP internal error handler */
	return true;
}

function wt_404($redirect=false) {
	if($_GET["wtfatalerror"]) {
		if($_SERVER["HTTP_REFERER"]) {
			$url=$_SERVER["HTTP_REFERER"];
		} else {
			$url="http".($_SERVER["HTTPS"]=="on" ? "s" : "")."://".$_SERVER["HTTP_HOST"]."/_ONBEKEND_";
		}
		$errstr=$_GET["wtfatalerror"];
		$errline=0;
		if(ereg(" in ([^[:space:]]+) on line ([0-9]+)",$_GET["wtfatalerror"],$regs)) {
			$errfile=$regs[1];
			$errline=$regs[2];
		}
#		mail("systeembeheer@webtastic.nl","Error","Fout: http://owp.webtastic.nl/error_log.php?l=".urlencode($errline)."&n=".urlencode($errno)."&f=".urlencode($errfile)."&u=".urlencode($url)."&s=".urlencode($errstr)."&r=".urlencode($_SERVER["HTTP_REFERER"])."&i=".urlencode($_SERVER["REMOTE_ADDR"])."&sc=".urlencode($script));
		$fp=@fopen("http://owp.webtastic.nl/error_log.php?l=".urlencode($errline)."&n=".urlencode($errno)."&f=".urlencode($errfile)."&u=".urlencode($url)."&s=".urlencode($errstr)."&r=".urlencode($_SERVER["HTTP_REFERER"])."&i=".urlencode($_SERVER["REMOTE_ADDR"])."&sc=".urlencode($script),"r");
	} elseif($_SERVER["REDIRECT_STATUS"]=="404") {
		if($redirect) {
			if(ereg("\.php\)\.$",$_SERVER["REQUEST_URI"],$regs)) {
				header("Location: ".substr($_SERVER["REQUEST_URI"],0,-2),true,301);
				exit;
			}
			if(ereg("\.php/$",$_SERVER["REQUEST_URI"])) {
				header("Location: ".substr($_SERVER["REQUEST_URI"],0,-1),true,301);
				exit;
			}
			if(ereg("\.php\.$",$_SERVER["REQUEST_URI"])) {
				header("Location: ".substr($_SERVER["REQUEST_URI"],0,-1),true,301);
				exit;
			}
			if(ereg("\.php\)$",$_SERVER["REQUEST_URI"],$regs)) {
				header("Location: ".substr($_SERVER["REQUEST_URI"],0,-1),true,301);
				exit;
			}
			if(ereg("favicon\.ico",$_SERVER["REQUEST_URI"]) and file_exists("favicon.ico")) {
				header("Location: /favicon.ico",true,301);
				exit;
			}
			if(ereg("^/([a-z0-9]+)$",$_SERVER["REQUEST_URI"],$regs) and file_exists($regs[1].".html")) {
				header("Location: /".$regs[1].".html",true,301);
				exit;
			}
			if(ereg("^/([a-z0-9_-]+)/?$",$_SERVER["REQUEST_URI"],$regs) and file_exists($regs[1].".php")) {
				header("Location: /".$regs[1].".php",true,301);
				exit;
			}
			if($_SERVER["REQUEST_URI"]=="/%22") {
				header("Location: /",true,301);
				exit;
			}
		}
		$niet_melden_url=array("robots\.txt","owssvr\.dll","cltreq\.asp","wpad\.dat","_vti_bin","_vti_inf");
		while(list($key,$value)=each($niet_melden_url)) {
			if(eregi($value,$_SERVER["REQUEST_URI"])) {
				$niet_melden=true;
			}
		}
		if(!$niet_melden) {
			$url="http".($_SERVER["HTTPS"]=="on" ? "s" : "")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
			$fp=@fopen("http://owp.webtastic.nl/error_log.php?u=".urlencode($url)."&r=".urlencode($_SERVER["HTTP_REFERER"])."&i=".urlencode($_SERVER["REMOTE_ADDR"])."&b=".urlencode($_SERVER["HTTP_USER_AGENT"]),"r");
		}
	}
}

class wt_mail {

	/*

	#voorbeeld:
	$mail=new wt_mail;
	$mail->fromname="Testmailer";
	$mail->from="test@webtastic.nl";
	$mail->toname="Jeroen";
	$mail->to="jeroen@webtastic.nl";
	$mail->subject="Onderwerp";

	$mail->plaintext="Hallo";

	$mail->html_top="";
	$mail->html="<B>Hallo</B>";
	$mail->html_bottom="";

	$mail->send();

	*/

	function wt_mail() {
		global $vars;
		if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" or $GLOBALS["vars"]["lokale_testserver"] or $GLOBALS["vars"]["acceptatie_testserver"] or ($_SERVER["USER"]=="root" and ereg("\.postvak\.net$",$_SERVER["HOSTNAME"])) or (defined("wt_test") and constant("wt_test") === true)) $this->test=true;
		$this->send_mail=true;
		if($_SERVER["HTTPS"]<>"on" or $vars["wt_mail_https_bcc"]) {
			if(WT_mail_no_send_bcc===true) {

			} else {
				$this->send_bcc=true;
			}
		}
		$this->bgcolor="#FFFFFF";
		$this->fontface="Verdana, Arial, Helvetica, sans-serif";
		$this->fontsize="12px";
		$this->fromname="WebTastic Automail";
		$this->from="automail@webtastic.nl";
		$this->converthtml=true;
		$this->css_hovercolor="#cc3333";
		$this->settings["version"]="2.0.1";
		$this->settings["imap_8bit"]=true;
		$this->settings["plaintext_wordwrap"]=true;
		$this->settings["plaintext_utf8"]=false; # werkt nog niet met attachments!
		$this->settings["utf8_headers"]=false;

		return true;
	}

	function attachment($file,$mimetype="",$inline=false,$otherfilename="") {
		if(file_exists($file)) {
			$f=fopen($file,'rb');
			$file_content=fread($f,filesize($file));
			fclose($f);
			$encoded=chunk_split(base64_encode($file_content));
			$md5=md5($encoded);
			if(!$mimetype) {
				if(function_exists("mime_content_type")) {
					$mimetype=mime_content_type($file);
				}
				if(!$mimetype) {
					$ext=strtolower(substr($file,strrpos($file,".")+1,strlen($file)-strrpos($file,".")-1));
					if($ext=="jpg") $mimetype="image/jpeg";
					if($ext=="gif") $mimetype="image/gif";
					if($ext=="png") $mimetype="image/png";
					if($ext=="pdf") $mimetype="application/pdf";
					if($ext=="txt") $mimetype="text/plain";
					if($ext=="htm") $mimetype="text/html";
					if($ext=="html") $mimetype="text/html";
				}
			}
			if($encoded and !$this->attachment[$this->attachmentcounter]["md5"][$md5] and $mimetype) {
				$this->attachmentcounter++;
				$this->attachment[$this->attachmentcounter]["encoded"]=$encoded;
				$this->attachment[$this->attachmentcounter]["mimetype"]=$mimetype;
				$this->attachment[$this->attachmentcounter]["md5"][$md5]=true;
				if($otherfilename) {
					$name=$otherfilename;
				} else {
					if(ereg("/",$file)) {
						$name=split("/",$file);
						$name=$name[count($name)-1];
					} else {
						$name=$file;
					}
				}
				$this->attachment[$this->attachmentcounter]["name"]=$name;
				if($inline) {
					$this->attachment[$this->attachmentcounter]["cidname"]=$this->attachmentcounter.$name;
					return $this->attachment[$this->attachmentcounter]["cidname"];
				}
			}
		}
	}

	function encode_header($text, $use_quotes=false) {

		//
		// use correct encoding
		//

		$return = trim($text);

		if($use_quotes and preg_match("/[^A-Za-z0-9 ]/",$return)) {
			// use quotes if non-alphanumeric
			$quote='"';
		}

		if(preg_match("/[^\x20-\x7f]/",$return)) {

			if($this->settings["utf8_headers"]) {
				if(function_exists(imap_8bit)) {
					# Quoted-printable
					$return="=?UTF-8?Q?".str_replace("=\r\n","",preg_replace("/\?/","=3F",imap_8bit($return)))."?=";
				} else {
					# Base64
					$return="=?UTF-8?B?".base64_encode($return)."?=";
				}
			} else {
				if(function_exists(imap_8bit)) {
					# Quoted-printable
					$return="=?ISO-8859-1?Q?".str_replace("=\r\n","",preg_replace("/\?/","=3F",imap_8bit($return)))."?=";
				} else {
					# Base64
					$return="=?ISO-8859-1?B?".base64_encode($return)."?=";
				}
			}
		}

		return $quote.$return.$quote;

	}

	function send() {
		unset($this->send_to,$this->send_subject,$this->send_plaintext,$this->send_html,$this->send_header,$this->send_body,$this->send_attachment);
		if($this->toname) {
			$this->send_to=$this->encode_header($this->toname, true)." <".trim($this->to).">";
		} else {
			$this->send_to=$this->to;
		}
		if($this->subject) {
			$this->send_subject=ereg_replace("ñ","-",$this->subject);
			$this->send_subject=$this->encode_header($this->send_subject);
		}

		# Header bepalen
		if($this->replyto) {
			if($this->test) {
				$this->send_header="Reply-To: reply_".ereg_replace("@","-at-",$this->replyto)."@webtastic.nl\n";
			} else {
				$this->send_header="Reply-To: ".$this->replyto."\n";
			}
		}
		if($_SERVER["SERVER_ADDR"]=="81.4.84.11" and $this->send_mail) $this->send_header.="Message-Id: <".strtoupper(md5(uniqid(rand(),1)))."@webtastic.netground.nl>\n";
		if($this->spamtest) $this->send_header.="X-WebTastic-Spam-Test: yes\n";
		$this->send_header.="Precedence: bulk\n";
		$this->send_header.="X-Mailer: WebTastic wt_mail v".$this->settings["version"]." - www.webtastic.nl\n";
		$this->send_header.="X-Loop: wt_mail\n";
		$hostname=@gethostbyaddr($_SERVER["REMOTE_ADDR"]);
		if(!eregi("[a-z]",$hostname)) unset($hostname);
		if($_SERVER["REMOTE_ADDR"]) $this->send_header.="X-Originating-IP: ".$_SERVER["REMOTE_ADDR"]."\n".($hostname && $hostname<>$_SERVER["REMOTE_HOST"] ? "X-Originating-Host: ".$hostname."\n" : "").($_SERVER["HTTP_USER_AGENT"] ? "X-Originating-User-Agent: ".$_SERVER["HTTP_USER_AGENT"]."\n" : "");
		if($_SERVER["SERVER_ADDR"]) $this->send_header.="X-Server-IP: ".$_SERVER["SERVER_ADDR"]."\n";
		if($this->extraheaders) $this->send_header.=$this->extraheaders."\n";
		if($this->xheaders) $this->send_header.=$this->xheaders."\n";

		if($this->fromname) {
			$this->send_header.="From: ".$this->encode_header($this->fromname, true)." <".$this->from.">";
		} else {
			$this->send_header.="From: ".$this->from;
		}

		# Boundary bepalen
		$boundary0="WT_000_".substr(md5(uniqid(rand(),1)),0,8).".".substr(md5(uniqid(rand(),1)),0,8);
		$boundary1="WT_001_".substr(md5(uniqid(rand(),1)),0,8).".".substr(md5(uniqid(rand(),1)),0,8);

		# Attachments verwerken ($this->send_attachment opstellen)
		if($this->attachment) {
			reset($this->attachment);
			while(list($key,$value)=each($this->attachment)) {
				$this->send_attachment.="\n--".$boundary0."\n";
				$this->send_attachment.="Content-Type: ".$value["mimetype"]."; name=\"".$value["name"]."\"\n";
				if($this->attachment[$key]["cidname"]) {
					$this->send_attachment.="Content-ID: <".$this->attachment[$key]["cidname"].">\n";
					$this->send_attachment.="Content-Disposition: inline; filename=\"".$value["name"]."\"\n";
					$temp["inline"]=true;
				} else {
					$this->send_attachment.="Content-Disposition: attachment; filename=\"".$value["name"]."\"\n";
				}
				$this->send_attachment.="Content-Transfer-Encoding: base64\n\n";
				$this->send_attachment.=$value["encoded"];
			}
		}

		$this->send_plaintext=$this->plaintext;

		if($this->settings["plaintext_wordwrap"]) {
			$this->send_plaintext=wordwrap($this->send_plaintext);
		}
		if($this->html) {
			# HTML-mail
			if($this->converthtml and !$this->plaintext) {
				# HTML-mail omzetten naar plaintext
				$this->send_plaintext=$this->html;
				$this->send_plaintext=eregi_replace("<!DOCTYPE[^>]*>"," ",$this->send_plaintext);
				$this->send_plaintext=eregi_replace("<title>[^<]*</title>"," ",$this->send_plaintext);
				$this->send_plaintext=ereg_replace("[".chr(13)."]","\n",$this->send_plaintext);
				$this->send_plaintext=ereg_replace("\n"," ",$this->send_plaintext);
				$this->send_plaintext=ereg_replace("[[:blank:]".chr(160)."]+"," ",$this->send_plaintext);
				$this->send_plaintext=@preg_replace("'<img [^>]*alt=\"([^\"]*)\"[^>]*?>'si","\\1",$this->send_plaintext);
				$this->send_plaintext=@preg_replace("'<a href=\"(http://[^[:blank:]]*)\"[^>]*?>(.*?)</a>'si","\\2 (\\1)",$this->send_plaintext);
				if(!$this->send_plaintext) $this->send_plaintext=$this->html;
				$this->send_plaintext=eregi_replace("</title>"," \n</title>",$this->send_plaintext);
				$this->send_plaintext=eregi_replace("</tr>","\n",$this->send_plaintext);
				$this->send_plaintext=eregi_replace("<p>","\n\n",$this->send_plaintext);
				$this->send_plaintext=eregi_replace("<br>","\n",$this->send_plaintext);
				$this->send_plaintext=eregi_replace("</td>"," ",$this->send_plaintext);
				$this->send_plaintext=eregi_replace("&nbsp;"," ",$this->send_plaintext);
				$htmlentitiestable=array_flip(get_html_translation_table(HTML_ENTITIES));
				$this->send_plaintext=strtr($this->send_plaintext,$htmlentitiestable);
				$this->send_plaintext=strip_tags($this->send_plaintext);
				$this->send_plaintext=ereg_replace("\n ","\n",$this->send_plaintext);
				$this->send_plaintext=ereg_replace(chr(10)."{3,}","\n\n",$this->send_plaintext);
				if($this->settings["plaintext_wordwrap"]) {
					if($this->send_plaintext) $this->send_plaintext=wordwrap($this->send_plaintext);
				}
			}

			if(!eregi("<html>.*</html>",$this->html) and !eregi("<\!DOCTYPE.*HTML.*PUBLIC",$this->html)) {
				if(!isset($this->html_top)) $this->html_top="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=iso-8859-1\"/>\n<style type=\"text/css\"><!--\na:hover{color:".$this->css_hovercolor."}\ntd{font-family: ".$this->fontface.";\nfont-size: ".$this->fontsize.";\n}\n--></style></head>\n<body bgcolor=\"".$this->bgcolor."\"><table><tr><td>\n";
				if(!isset($this->html_bottom)) $this->html_bottom="</td></tr></table></body></html>\n";
			}
			$this->send_html=$this->html_top.$this->html.$this->html_bottom."\n\n";
		}

		#
		# $this->send_body en $this-send_header bepalen
		#
		if($this->send_plaintext and !$this->send_html) {
			#
			# Plaintext-mail
			#
			if($this->send_attachment) {
				# plaintext-mail met attachment
				$this->send_header.="\nMIME-Version: 1.0\nContent-Type: multipart/mixed;\n	boundary=\"".$boundary0."\"";
				$this->send_body="This is a multi-part message in MIME format.\n";
				if(function_exists(imap_8bit) and $this->settings["imap_8bit"]) {
					$this->send_body.="\n--".$boundary0."\nContent-Type: text/plain; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: quoted-printable\n\n";
					$this->send_body.=imap_8bit($this->send_plaintext);
				} else {
					$this->send_body.="\n--".$boundary0."\nContent-Type: text/plain; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: 7bit\n\n";
					$this->send_body.=$this->send_plaintext;
				}
				$this->send_body.=$this->send_attachment;
				$this->send_body.="\n--".$boundary0."--\n";
			} else {
				# plaintext-mail zonder attachment
				if($this->settings["plaintext_utf8"]) {
					$this->send_header.="\nContent-Type: text/plain; charset=UTF-8";
				}
				$this->send_body=$this->send_plaintext;
			}
		} else {
			#
			# HTML-mail
			#
			if($this->send_attachment) {
				# HTML-mail met attachment
				if($temp["inline"]) {
					# Inline attachment(s)
					$this->send_header.="\nMIME-Version: 1.0\nContent-Type: multipart/related;\n	type=\"multipart/alternative\";\n	boundary=\"".$boundary0."\"";
				} else {
					# Gewone attachment(s)
					$this->send_header.="\nMIME-Version: 1.0\nContent-Type: multipart/mixed;\n	boundary=\"".$boundary0."\"";
				}
				$this->send_body="This is a multi-part message in MIME format.\n";
				$this->send_body.="\n--".$boundary0."\nContent-Type: multipart/alternative;\n	boundary=\"".$boundary1."\"\n\n";
				if(function_exists(imap_8bit) and $this->settings["imap_8bit"]) {
					$this->send_body.="\n--".$boundary1."\nContent-Type: text/plain; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: quoted-printable\n\n";
					$this->send_body.=imap_8bit($this->send_plaintext);
					$this->send_body.="\n--".$boundary1."\nContent-Type: text/html; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: quoted-printable\n\n";
					$this->send_body.=imap_8bit($this->send_html);
				} else {
					$this->send_body.="\n--".$boundary1."\nContent-Type: text/plain; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: 7bit\n\n";
					$this->send_body.=$this->send_plaintext;
					$this->send_body.="\n--".$boundary1."\nContent-Type: text/html; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: base64\n\n";
					$this->send_body.=chunk_split(base64_encode($this->send_html));
				}
				$this->send_body.="\n--".$boundary1."--\n";
				$this->send_body.=$this->send_attachment;
				$this->send_body.="\n--".$boundary0."--\n";
			} else {
				# HTML-mail zonder attachment
				$this->send_header.="\nMIME-Version: 1.0\nContent-Type: multipart/alternative;\n	boundary=\"".$boundary0."\"";
				$this->send_body="This is a multi-part message in MIME format.\n";
				if(function_exists(imap_8bit) and $this->settings["imap_8bit"]) {

					if($this->settings["plaintext_utf8"]) {
						$this->send_body.="\n--".$boundary0."\nContent-Type: text/plain; charset=\"UTF-8\"\nContent-Transfer-Encoding: quoted-printable\n\n";
						$this->send_body.=imap_8bit($this->send_plaintext);
					} else {
						$this->send_body.="\n--".$boundary0."\nContent-Type: text/plain; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: quoted-printable\n\n";
						$this->send_body.=imap_8bit($this->send_plaintext);
					}

					$this->send_body.="\n--".$boundary0."\nContent-Type: text/html; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: quoted-printable\n\n";
					$this->send_body.=imap_8bit($this->send_html);
				} else {
					$this->send_body.="\n--".$boundary0."\nContent-Type: text/plain; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: 7bit\n\n";
					$this->send_body.=$this->send_plaintext;
					$this->send_body.="\n--".$boundary0."\nContent-Type: text/html; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: base64\n\n";
					$this->send_body.=chunk_split(base64_encode($this->send_html));
				}
				$this->send_body.="\n--".$boundary0."--\n";
			}
		}

		# "\r\n" vervangen door "\n" in send_body
#		$this->send_body=ereg_replace("\r\n","\n",$this->send_body);
		$this->send_body=str_replace("\r\n","\n",$this->send_body);

		if(preg_match("/@webtastic\.nl/",$this->to)) {
			# geen BCC-trackmail bij interne mail
			$this->send_bcc=false;
		}

		# BCC-trackmail
		if($this->send_bcc) $bcc="Bcc: ".WT_trackmailaddress."\n"; else $bcc="";

		# BCC adressen?
		if($this->bcc) {
			$bcc.="Bcc: ".$this->bcc."\n";
		}

		# Mail verzenden
		if($this->send_mail) {
			if($this->test) {
				if($this->toname) {
					$this->toname_aangepast="\"".$this->toname." (".ereg_replace("@","_at_",$this->to).")\"";
				} else {
					$this->toname_aangepast=ereg_replace("@","_at_",$this->to);
				}
				if($GLOBALS["vars"]["lokale_testserver_mailadres"]) {
					$this->to=$GLOBALS["vars"]["lokale_testserver_mailadres"];
				} elseif($_SERVER["HTTP_HOST"]=="bl.postvak.net" or $_SERVER["HOSTNAME"]=="bl.postvak.net" or ($_SERVER["SERVER_ADDR"]=="172.16.6.1" and $_SERVER["REMOTE_ADDR"]=="172.16.6.40")) {
					$this->to="testform_bl@webtastic.nl";
				} elseif($_SERVER["HTTP_HOST"]=="ss.postvak.net" or $_SERVER["HOSTNAME"]=="ss.postvak.net" or (defined("wt_test_name") and constant("wt_test_name") === "macbook") or (defined("wt_test_name") and constant("wt_test_name") === "ss")) {
					$this->to="testform_ss@webtastic.nl";
				} else {
					$this->to="testform@webtastic.nl";
				}
				$this->send_to=trim($this->toname_aangepast)." <".trim($this->to).">";
				if($GLOBALS["wt_mail_testmailcounter"]<5) {
					mail($this->send_to,"Test: ".$this->send_subject,$this->send_body,$this->send_header);
					$GLOBALS["wt_mail_testmailcounter"]++;
				}
			} else {
				if($this->smtpmail) {
					$smtp=new SMTPMAIL;
					$smtp->close_after_send=false;
					if($this->returnpath) {
						$returnpath=$this->returnpath;
					} else {
						$returnpath=$this->from;
					}
					if(!$smtp->send_smtp_mail($this->to,$this->send_to,$this->send_subject,"X-WT_PHPSMTP: yes\n".$bcc.$this->send_header."\n".$this->send_body,"",$returnpath)) {
						echo "Error in sending mail!<BR>Error: ".$smtp->error;
					} else {
						echo "Mail sent succesfully!";
					}
				} else {
					if($this->returnpath) {
						mail($this->send_to,$this->send_subject,$this->send_body,$bcc.$this->send_header,"-f".$this->returnpath);
					} else {
						mail($this->send_to,$this->send_subject,$this->send_body,$bcc.$this->send_header);
					}
				}
				# Logfile
				$file="/var/log/sites/wt_mail.log";
				if(@file_exists($file)) {
					$fp=@fopen($file,'a');
					@fwrite($fp,date("M d H:i:s")."	http://".($_SERVER["HTTPS"]=="on" ? "s" : "").$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"].($_SERVER["QUERY_STRING"] ? "?".$_SERVER["QUERY_STRING"] : "")."	".$this->send_to."	".$this->send_subject."	".($this->html ? "html" : "plaintext")."\n");
					@fclose($fp);
				}
			}
		}
		$this->mailcounter++;
	}
}

function wt_jabber($to,$msg) {
	$filename="http://owp.webtastic.nl/jabber.php?t=".urlencode($to)."&m=".urlencode($msg);
#	$filename="http://ss.postvak.net/werkplek/jabber.php?t=".urlencode($to)."&m=".urlencode($msg);
	$dataFile=@fopen($filename,"r");
#	echo "\n\n".$filename."\n\n";
	if($dataFile) {
		$buffer=fgets($dataFile,4096);
		fclose($dataFile);
		return trim($buffer);
	} else {
		# Verbinding niet mogelijk
#		echo "\n\nFOUT met jabber!!\n\n";
		return "FOUT met jabber";
	}
}

function wt_validmail($mail="") {
	# Controleer of een mailadres klopt
	if(eregi("^[0-9a-z][-_+0-9a-z.&']*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,8}$",$mail)) {
		return true;
	} else {
		return false;
	}
}

function wt_ss_beep() {
	# Laat een beep horen op de server ss.postvak.net
	$fp = fsockopen("vps.postvak.net",11200,$errno,$errstr,1);
}

function wt_aantal_decimalen($value) {
	if ((int)$value == $value) {
		return 0;
	} else if (! is_numeric($value)) {
		// throw new Exception('numberOfDecimals: ' . $value . ' is not a number!');
		return false;
	}
	return strlen($value) - strrpos($value, '.') - 1;
}

function wt_get_extension($file,$filename="") {
	$temp=@getimagesize($_FILES["uploadedfile"]["tmp_name"]);
	if($temp["mime"]=="image/jpeg") {
		$ext="jpg";
	} elseif($temp["mime"]=="image/gif") {
		$ext="gif";
	} elseif($temp["mime"]=="image/png") {
		$ext="png";
	} else {
		if($filename) $file=$filename;
		$ext=strtolower(substr($file,strrpos($file,".")+1,strlen($file)-strrpos($file,".")-1));
	}
	if($ext) {
		return $ext;
	} else {
		return false;
	}
}

function XXstartElement($parser, $name, $attrs) {
   global $depth;
   global $stack;
   global $tree;

   $element = array();
   $element['name'] = $name;
   foreach ($attrs as $key => $value) {
		//echo $key."=".$value;
		$element[$key]=$value;
	}

   $last = &$stack[count($stack)-1];
   $last[count($last)-1] = &$element;
   $stack[count($stack)] = &$element;

   $depth++;
}

function wt_xmlstartElement($parser, $name, $attrs) {
	global $wt_xmldepth;
	global $wt_xmlstack;
	global $wt_xmltree;
	global $wt_xmlteller;

	$element = array();
	foreach ($attrs as $key => $value) {
		$element[strtolower($key)]=$value;
	}
	end($wt_xmlstack);
	if(is_array($wt_xmlstack[key($wt_xmlstack)][strtolower($name)])) {
		if(!$wt_xmlstack[key($wt_xmlstack)][strtolower($name)][0]) {
			$temp_array=$wt_xmlstack[key($wt_xmlstack)][strtolower($name)];
			unset($wt_xmlstack[key($wt_xmlstack)][strtolower($name)]);
			$wt_xmlstack[key($wt_xmlstack)][strtolower($name)][0]=$temp_array;
		}

#		$wt_xmlstack[strtolower($name)][0] = &$element;

		$wt_xmlstack[key($wt_xmlstack)][strtolower($name)][$wt_xmlteller[$wt_xmldepth]] = &$element;
		$wt_xmlstack[strtolower($name)][$wt_xmlteller[$wt_xmldepth]] = &$element;
	} else {
		$wt_xmlstack[key($wt_xmlstack)][strtolower($name)] = &$element;
		$wt_xmlstack[strtolower($name)] = &$element;
	}
	$wt_xmlteller[$wt_xmldepth]++;
	$wt_xmldepth++;
}

function wt_xmlendElement($parser,$name) {
	global $wt_xmldepth;
	global $wt_xmlstack;
	array_pop($wt_xmlstack);
	$wt_xmldepth--;
}

function wt_getxml($file) {
	global $wt_xmldepth;
	global $wt_xmlstack;
	global $wt_xmltree;
	global $wt_xmlteller;
	$wt_xmlteller="";
	$wt_xmldepth=0;
	$wt_xmltree=array();
	$wt_xmltree['name'] = "root";
	$wt_xmlstack[]=&$wt_xmltree;
	$xml_parser=xml_parser_create();
	xml_set_element_handler($xml_parser,"wt_xmlstartElement","wt_xmlendElement");
	if(!($fp=@fopen($file,"r"))) {
#		trigger_error("could not open XML input ".$file,E_USER_NOTICE);
		return false;
	}
	while($data=@fread($fp,4096)) {
		if(!xml_parse($xml_parser,$data,feof($fp))) {
			trigger_error(sprintf("XML error: %s at line %d",xml_error_string(xml_get_error_code($xml_parser)),xml_get_current_line_number($xml_parser)),E_USER_NOTICE);
			return false;
		}
	}
	xml_parser_free($xml_parser);
#	$wt_xmltree=end(end($wt_xmlstack));
	return($wt_xmltree);
}

function wt_convert2url($text) {
	$text=wt_stripaccents($text);
	$text=ereg_replace("[^A-Za-z0-9_\-]","_",$text);
	$text=ereg_replace("_{2,}","_",$text);
	$text=ereg_replace("_$","",$text);
	return urlencode($text);
}

function wt_convert2url_seo($text) {
	$text=wt_stripaccents($text);
	$text=preg_replace("/[^A-Za-z0-9_\-]/","-",$text);
	$text=preg_replace("/-{2,}/","-",$text);
	$text=preg_replace("/-$/","",$text);
	return urlencode($text);
}

function wt_cur($float,$thousands=true) {
	if($thousands) {
		$thousands=".";
	}
	# Currency: geeft waarde met komma en punten (per duizend)
	$return=@number_format($float,2,",",$thousands);
	if($return or $return=="0,00") {
		return $return;
	}
}

function wt_leeftijd($geboortedatum,$nu="") {
	if(!$nu) $nu=time();

	$year  = wt_adodb_date("Y",$geboortedatum);
	$month = wt_adodb_date("m",$geboortedatum);
	$day   = wt_adodb_date("d",$geboortedatum);

	$year_diff  = wt_adodb_date("Y",$nu) - $year;
	$month_diff = wt_adodb_date("m",$nu) - $month;
	$day_diff   = wt_adodb_date("d",$nu) - $day;
	if ($month_diff < 0) $year_diff--;
	elseif (($month_diff==0) && ($day_diff < 0)) $year_diff--;
	return $year_diff;
}


#function wt_leeftijd($unixtime,$nu) {
#	if(!$nu) $nu=time();
#	list($y1,$m1,$d1)=explode(' ',date('Y m d',$unixtime));
#	list($y2,$m2,$d2)=explode(' ',date('Y m d',$nu));
#	$leeftijd = $y2 - $y1 - ((($m2 < $m1) || ($d2 < $d1)) ? 1 : 0);
#	return $leeftijd;
#}

if (!function_exists("datum")) {
	function datum($a,$time=0,$language='nl') {
		#
		# Gebruik: datum(string,[timestamp],[taal])
		#

		global $vars;

		$a = strtoupper($a);
		if(!$time) $time=time();
		if($language=="nl") {
			// Nederlands
			switch(wt_adodb_date("w",$time)) {
				case 0: $dag="zondag"; break;
				case 1: $dag="maandag"; break;
				case 2: $dag="dinsdag"; break;
				case 3: $dag="woensdag"; break;
				case 4: $dag="donderdag"; break;
				case 5: $dag="vrijdag"; break;
				case 6: $dag="zaterdag"; break;
			}
			switch(wt_adodb_date("w",$time)) {
				case 0: $dg="zo"; break;
				case 1: $dg="ma"; break;
				case 2: $dg="di"; break;
				case 3: $dg="wo"; break;
				case 4: $dg="do"; break;
				case 5: $dg="vr"; break;
				case 6: $dg="za"; break;
			}
			switch(wt_adodb_date("n",$time)) {
				case 1: $maand="januari"; break;
				case 2: $maand="februari"; break;
				case 3: $maand="maart"; break;
				case 4: $maand="april"; break;
				case 5: $maand="mei"; break;
				case 6: $maand="juni"; break;
				case 7: $maand="juli"; break;
				case 8: $maand="augustus"; break;
				case 9: $maand="september"; break;
				case 10: $maand="oktober"; break;
				case 11: $maand="november"; break;
				case 12: $maand="december"; break;
			}
			switch(wt_adodb_date("n",$time)) {
				case 1: $mnd="jan"; break;
				case 2: $mnd="feb"; break;
				case 3: $mnd="maa"; break;
				case 4: $mnd="apr"; break;
				case 5: $mnd="mei"; break;
				case 6: $mnd="jun"; break;
				case 7: $mnd="jul"; break;
				case 8: $mnd="aug"; break;
				case 9: $mnd="sep"; break;
				case 10: $mnd="okt"; break;
				case 11: $mnd="nov"; break;
				case 12: $mnd="dec"; break;
			}
		} elseif($language=="fr") {
			// franÁais
			switch(wt_adodb_date("w",$time)) {
				case 0: $dag="dimanche"; break;
				case 1: $dag="lundi"; break;
				case 2: $dag="mardi"; break;
				case 3: $dag="mercredi"; break;
				case 4: $dag="jeudi"; break;
				case 5: $dag="vendredi"; break;
				case 6: $dag="samedi"; break;
			}
			switch(wt_adodb_date("w",$time)) {
				case 0: $dg="dim."; break;
				case 1: $dg="lun."; break;
				case 2: $dg="mar."; break;
				case 3: $dg="mer."; break;
				case 4: $dg="jeu."; break;
				case 5: $dg="ven."; break;
				case 6: $dg="sam."; break;
			}
			switch(wt_adodb_date("n",$time)) {
				case 1: $maand="janvier"; break;
				case 2: $maand="fÈvrier"; break;
				case 3: $maand="mars"; break;
				case 4: $maand="avril"; break;
				case 5: $maand="mai"; break;
				case 6: $maand="juin"; break;
				case 7: $maand="juillet"; break;
				case 8: $maand="ao˚t"; break;
				case 9: $maand="septembre"; break;
				case 10: $maand="octobre"; break;
				case 11: $maand="novembre"; break;
				case 12: $maand="dÈcembre"; break;
			}
			switch(wt_adodb_date("n",$time)) {
				case 1: $mnd="janv."; break;
				case 2: $mnd="fÈvr."; break;
				case 3: $mnd="mars"; break;
				case 4: $mnd="avril"; break;
				case 5: $mnd="mai"; break;
				case 6: $mnd="juin"; break;
				case 7: $mnd="juil."; break;
				case 8: $mnd="ao˚t"; break;
				case 9: $mnd="sept."; break;
				case 10: $mnd="oct."; break;
				case 11: $mnd="nov."; break;
				case 12: $mnd="dÈc."; break;
			}
		} else {
			// English
			switch(wt_adodb_date("w",$time)) {
				case 0: $dag="Sunday"; break;
				case 1: $dag="Monday"; break;
				case 2: $dag="Tuesday"; break;
				case 3: $dag="Wednesday"; break;
				case 4: $dag="Thursday"; break;
				case 5: $dag="Friday"; break;
				case 6: $dag="Saturday"; break;
			}
			switch(wt_adodb_date("w",$time)) {
				case 0: $dg="Sun"; break;
				case 1: $dg="Mon"; break;
				case 2: $dg="Tue"; break;
				case 3: $dg="Wed"; break;
				case 4: $dg="Thu"; break;
				case 5: $dg="Fri"; break;
				case 6: $dg="Sat"; break;
			}
			switch(wt_adodb_date("n",$time)) {
				case 1: $maand="January"; break;
				case 2: $maand="February"; break;
				case 3: $maand="March"; break;
				case 4: $maand="April"; break;
				case 5: $maand="May"; break;
				case 6: $maand="June"; break;
				case 7: $maand="July"; break;
				case 8: $maand="August"; break;
				case 9: $maand="September"; break;
				case 10: $maand="October"; break;
				case 11: $maand="November"; break;
				case 12: $maand="December"; break;
			}
			switch(wt_adodb_date("n",$time)) {
				case 1: $mnd="Jan"; break;
				case 2: $mnd="Feb"; break;
				case 3: $mnd="Mar"; break;
				case 4: $mnd="Apr"; break;
				case 5: $mnd="May"; break;
				case 6: $mnd="Jun"; break;
				case 7: $mnd="Jul"; break;
				case 8: $mnd="Aug"; break;
				case 9: $mnd="Sep"; break;
				case 10: $mnd="Oct"; break;
				case 11: $mnd="Nov"; break;
				case 12: $mnd="Dec"; break;
			}
		}
		$a = ereg_replace("(^|[^a-zA-Z0-9])DAG($|[^a-zA-Z0-9])","\\1".$dag."\\2",$a);
		$a = ereg_replace("(^|[^a-zA-Z0-9])DG($|[^a-zA-Z0-9])","\\1".$dg."\\2",$a);
		$a = ereg_replace("(^|[^a-zA-Z0-9])MAAND($|[^a-zA-Z0-9])","\\1".$maand."\\2",$a);
		$a = ereg_replace("(^|[^a-zA-Z0-9])MND($|[^a-zA-Z0-9])","\\1".$mnd."\\2",$a);
		$a = ereg_replace("(^|[^a-zA-Z0-9])JJJJ($|[^a-zA-Z0-9])","\\1".wt_adodb_date("Y",$time)."\\2",$a);
		$a = ereg_replace("(^|[^a-zA-Z0-9])JJ($|[^a-zA-Z0-9])","\\1".wt_adodb_date("y",$time)."\\2",$a);
		$a = ereg_replace("(^|[^a-zA-Z0-9])DD($|[^a-zA-Z0-9])","\\1".wt_adodb_date("d",$time)."\\2",$a);
		$a = ereg_replace("(^|[^a-zA-Z0-9])D($|[^a-zA-Z0-9])","\\1".wt_adodb_date("j",$time)."\\2",$a);
		$a = ereg_replace("(^|[^a-zA-Z0-9])MM($|[^a-zA-Z0-9])","\\1".wt_adodb_date("m",$time)."\\2",$a);
		$a = ereg_replace("(^|[^a-zA-Z0-9])M($|[^a-zA-Z0-9])","\\1".wt_adodb_date("n",$time)."\\2",$a);
		$a = ereg_replace("(^|[^a-zA-Z0-9])UU($|[^a-zA-Z0-9])","\\1".wt_adodb_date("H",$time)."\\2",$a);
		$a = ereg_replace("(^|[^a-zA-Z0-9])U($|[^a-zA-Z0-9])","\\1".wt_adodb_date("G",$time)."\\2",$a);
		$a = ereg_replace("(^|[^a-zA-Z0-9])ZZ($|[^a-zA-Z0-9])","\\1".wt_adodb_date("i",$time)."\\2",$a);
		$a = ereg_replace("(^|[^a-zA-Z0-9])SS($|[^a-zA-Z0-9])","\\1".wt_adodb_date("s",$time)."\\2",$a);
		$a = ereg_replace("_","&nbsp;",$a);

		if($vars["wt_htmlentities_utf8"]) {
			$a = iconv("CP1252", "UTF-8", $a);
		}

		return $a;
	}
}

function wt_adodb_date($var,$time) {
	# Proberen om memory-probleem met adodb_date op te lossen
	if($time<0 or $time>2145913200) {
		$return=adodb_date($var,$time);
	} else {
		$return=date($var,$time);
	}
	return $return;
}

if (!function_exists("array_search")) {
	function array_search ($needle, $haystack, $strict = FALSE) {
		if(is_array($haystack)) {
			foreach($haystack as $key => $value) {
				if($strict) {
					if($value === $needle) return $key;
				} else {
					if ($value == $needle) return $key;
				}
			}
		} else {
			echo "Geen array";
		}
	return FALSE;
	}
}
if (!function_exists("printarray")) {
	function printarray($array) {
		if(is_array($array)) {
			echo "<TABLE border=1 cellspacing=0 cellpadding=3 bordercolor=#878481>";
			while(list($key,$value) = each($array)) {
				if(is_array($value)) {
					printarray($value);
					echo "<P>";
				} else {
					echo "<TR><TD>".$key."</TD><TD>".$value."&nbsp;</TD</TR>";
				}
			}
			echo "</TABLE>";
		} else {
			echo "Dit is geen array";
		}
	}
}

if (!function_exists("vtanaam")) {
	# Functie om voornaam, tussenvoegsel, achternaam weer te geven
	function vtanaam($voornaam='',$tussenvoegsel='',$achternaam) {
		$return=$voornaam;
		if($tussenvoegsel) {
			if($return) $return.=" ".$tussenvoegsel; else $return=$tussenvoegsel;
		}
		if($return) $return.=" ".$achternaam; else $return=$achternaam;
		return $return;
	}
}

function wt_create_thumbnail($file,$newfile,$width,$height,$cut=false,$type="jpg",$quality=80,$detect_source_type=false) {
	#
	# Aanmaken jpg-thumbnail / resizen / thumbnails
	#

	# aspectratio bepalen
	$imgsize=getimagesize($file);

	if($detect_source_type) {
		if(function_exists("mime_content_type")) {
			# Kijken welk mime-type de bron heeft
			$mime_content_type=@mime_content_type($file);
		} else {
			$tmpimg = @imagecreatefromgif( $file );
			if($tmpimg) {
				$mime_content_type="image/gif";
			} else {
				$tmpimg = @imagecreatefrompng( $file );
				if($tmpimg) {
					$mime_content_type="image/png";
				}
			}
		}
		if($mime_content_type=="image/gif") {
			$source_type="gif";
		} elseif($mime_content_type=="image/png") {
			$source_type="png";
		} else {
			$source_type=$type;
		}
	} else {
		$source_type=$type;
	}

	$aspectratio=$imgsize[0]/$imgsize[1];

	# Nieuwe afmetingen bepalen
	if($width and $height) {
		$newwidth=$width;
		$newheight=$height;
	} elseif($width) {
		$newwidth=$width;
		$newheight=round($width/$aspectratio);
	} elseif($height) {
		$newwidth=round($height*$aspectratio);
		$newheight=$height;
	}

	# aspectRatio nieuwe afbeeldingen bepalen
	$newaspectratio=$newwidth/$newheight;

	if($aspectratio==$newaspectratio) {
		# Verhouding is hetzelfde
		$tempwidth=$newwidth;
		$tempheight=$newheight;
	} else {
		$testwidth=$newheight*$aspectratio;
		if(($testwidth<$newwidth and $cut) or ($testwidth>$newwidth and !$cut)) {
			$tempwidth=$newwidth;
			$tempheight=ceil($newwidth/$aspectratio);
		} else {
			$tempheight=$newheight;
			$tempwidth=ceil($newheight*$aspectratio);
		}
	}

	# Afbeeldingen resizen
	if($tempwidth==$imgsize[0] and $tempheight==$imgsize[1]) {
		# Afbeelding resizen is niet nodig
		if($source_type=="png") {
			$img=imagecreatefrompng($file);
		} elseif($source_type=="gif") {
			$img=imagecreatefromgif($file);
		} else {
			$img=imagecreatefromjpeg($file);
		}
	} else {
		# Afbeelding resizen naar nieuwe afmetingen (met originele verhouding)
		$img=imagecreatetruecolor($tempwidth,$tempheight);
		if($source_type=="png") {
			$org_img=imagecreatefrompng($file);
		} elseif($source_type=="gif") {
			$org_img=imagecreatefromgif($file);
		} else {
			$org_img=imagecreatefromjpeg($file);
		}
		imagecopyresampled($img,$org_img,0,0,0,0,$tempwidth,$tempheight,$imgsize[0],$imgsize[1]);
	}
	if($aspectratio==$newaspectratio) {
		# Afbeelding alleen vergroten/verkleinen (is al gebeurd)
		if($type=="png") {
			$pngcompression=round((100-$quality)/10);
			imagepng($img,$newfile,$pngcompression);
		} elseif($type=="gif") {
			imagegif($img,$newfile);
		} else {
			imagejpeg($img,$newfile,$quality);
		}
		imagedestroy($img);
	} else {
		if($cut) {
			#
			# Snijden
			#
			if($aspectratio<$newaspectratio) {
				# y-positie bepalen
				$thumbx=0;
				$thumby=ceil(($tempheight-$newheight)/2);
			} elseif($aspectratio>$newaspectratio) {
				# x-positie bepalen
				$thumbx=round(($tempwidth-$newwidth)/2);
				$thumby=0;
			} else {
				$thumbx=0;
				$thumby=0;
			}
			$img2=imagecreatetruecolor($newwidth,$newheight);
			imagecopyresized($img2,$img,0,0,$thumbx,$thumby,$tempwidth,$tempheight,$tempwidth,$tempheight);
		} else {
			#
			# Wit toevoegen
			#
			if($aspectratio<$newaspectratio) {
				# x-positie bepalen
				$thumbx=round(($newwidth-$tempwidth)/2);
				$thumby=0;
			} elseif($aspectratio>$newaspectratio) {
				# y-positie bepalen
				$thumbx=0;
				$thumby=ceil(($newheight-$tempheight)/2);
			} else {
				$thumbx=0;
				$thumby=0;
			}
			$img2=imagecreatetruecolor($newwidth,$newheight);
			$kleur=imagecolorallocate($img2,255,255,255);
			imagefill($img2,0,0,$kleur);
			imagecopyresized($img2,$img,$thumbx,$thumby,0,0,$tempwidth,$tempheight,$tempwidth,$tempheight);
		}

		if($type=="png") {
			$pngcompression=round((100-$quality)/10);
			imagepng($img2,$newfile,$pngcompression);
		} elseif($type=="gif") {
			imagegif($img2,$newfile);
		} else {
			imagejpeg($img2,$newfile,$quality);
		}
		imagedestroy($img2);
	}
}

if (!function_exists("wt_stripurl")) {
	function wt_stripurl($stripget) {
		if(preg_match("/\?/",$_SERVER["REQUEST_URI"])) {
			$return=preg_replace("/\?.*$/","",$_SERVER["REQUEST_URI"]);
			if(is_array($stripget)) {
				$querystring=wt_stripget($_GET,$stripget);
			} else {
				$querystring=$_SERVER["QUERY_STRING"];
			}
			if($querystring) {
				$return.="?".$querystring;
			}
		} else {
			$return=$_SERVER["REQUEST_URI"];
		}
		return $return;
	}
}


if (!function_exists("wt_stripget")) {
	function wt_stripget($get,$strip="") {
		@reset($get);
		while(list($key,$value)=@each($get)) {
			if(is_array($value)) {
				while(list($key2,$value2)=each($value)) {
					if(is_array($value2)) {
						while(list($key3,$value3)=each($value2)) {
							if(is_array($value3)) {
								while(list($key4,$value4)=each($value3)) {
									if(is_array($value4)) {
										trigger_error("too many GET levels in function wt_stripget",E_USER_NOTICE);
									} else {
										$urlkey=$key."[".$key2."][".$key3."][".$key4."]";
										if(!@in_array($urlkey,$strip)) {
											$return.="&".urlencode($urlkey)."=".urlencode($value4);
										}
									}
								}
							} else {
								$urlkey=$key."[".$key2."][".$key3."]";
								if(!@in_array($urlkey,$strip)) {
									$return.="&".urlencode($urlkey)."=".urlencode($value3);
								}
							}
						}
					} else {
						$urlkey=$key."[".$key2."]";
						if(!@in_array($urlkey,$strip)) {
							$return.="&".urlencode($urlkey)."=".urlencode($value2);
						}
					}
				}
			} else {
				if(!@in_array($key,$strip)) {
					$return.="&".urlencode($key)."=".urlencode($value);
				}
			}
		}
		$return=substr($return,1);
		return $return;
	}
}

if (!function_exists("wt_stripaccents")) {
	function wt_stripaccents($text) {
		return( strtr( $text,
		"¿¡¬√ƒ≈‡·‚„‰Â“”‘’÷ÿÚÛÙıˆ¯»… ÀËÈÍÎ«ÁÃÕŒœÏÌÓÔŸ⁄€‹˘˙˚¸ˇ—Òö",
		"AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNns" ) );
	}
}

if (!function_exists("htmlmail")) {
	function htmlmail($aanmail,$aannaam,$vanmail,$vannaam,$onderwerp,$body,$extraheaders="") {
		global $allfunctionsettings;
		if($aannaam) $aanmail=$aannaam." <".$aanmail.">";
		$mailheader="X-Mailer: WebTastic HTML-Mail - www.webtastic.nl\nX-Originating-IP: ".$_SERVER["REMOTE_ADDR"]."\n".($_SERVER["REMOTE_HOST"] ? "X-Originating-Host: ".$_SERVER["REMOTE_HOST"]."\n" : "").($_SERVER["HTTP_USER_AGENT"] ? "X-Originating-User-Agent: ".$_SERVER["HTTP_USER_AGENT"]."\n" : "").($extraheaders ? $extraheaders."\n" : "")."From: \"".$vannaam."\" <".$vanmail.">"."\nContent-Type: text/html; charset=iso-8859-1\n";
		$body=eregi_replace("</TD>","</TD>\n",$body);
		mail($aanmail,$onderwerp,$body,$mailheader);
		if($allfunctionsettings["htmlmail"]["towt"]) mail("track@webtastic.nl",$onderwerp,$body,$mailheader);
	}
}

# Oude webstats-functie. Bij voorkeur "wt_webstats" gebruiken.
if (!function_exists("webstats")) {
	function webstats($siteid,$pageid,$extra='',$frameset=false) {
		global $webstats;
	?><script type="text/javascript" language="javascript"><!--
	an=navigator.appName;d=document;function
	pr(){d.write("<img src='http<?php if($_SERVER["HTTPS"]=="on") echo "s"; ?>://stats.webtastic.nl/count.php",
	"?siteid=<?php echo $siteid ?>&amp;pageid=<?php echo $pageid; ?>&amp;javascript=1&amp;vc=<?php echo $webstats["visitcounter"] ?>&amp;lvt=<?php echo $webstats["lastvisittime"]; if($extra) echo "&amp;e=".urlencode($extra); ?>&amp;size="+srw+"&amp;colors="+srb+"&amp;referer="+escape(<?php if($frameset) echo "parent.document"; else echo "d"; ?>.referrer)+"' height=1 ",
	"width=1>");}srb="uk";srw="uk";//--></script><script type="text/javascript" language="javascript1.2"><!--
	s=screen;srw=s.width;an!="Netscape"?
	srb=s.colorDepth:srb=s.pixelDepth;//-->
	</script><script type="text/javascript" language="javascript"><!--
	pr();//--></script><noscript><img src="http<?php if($_SERVER["HTTPS"]=="on") echo "s"; ?>://stats.webtastic.nl/count.php?siteid=<?php echo $siteid ?>&amp;pageid=<?php echo $pageid; ?>&amp;javascript=0&amp;vc=<?php echo $webstats["visitcounter"] ?>&amp;lvt=<?php echo $webstats["lastvisittime"]; if($extra) echo "&amp;e=".urlencode($extra); ?>" height=1 width=1 alt=""></noscript><?php

	}
}

# Nieuwe webstats-functie (klopt de oude wel??). Bij voorkeur deze gebruiken.
function wt_webstats($siteid,$pageid,$extra='',$frameset=false) {
	global $webstats;
	$pageid=urlencode($pageid);
?><script type="text/javascript" language="javascript"><!--
an=navigator.appName;d=document;function
pr(){d.write("<img src='http<?php if($_SERVER["HTTPS"]=="on") echo "s"; ?>://stats.webtastic.nl/count.php",
"?siteid=<?php echo $siteid ?>&pageid=<?php echo $pageid; ?>&javascript=1&vc=<?php echo $webstats["visitcounter"] ?>&lvt=<?php echo $webstats["lastvisittime"]; if($extra) echo "&e=".urlencode($extra); ?>&size="+srw+"&colors="+srb+"&referer="+escape(<?php if($frameset) echo "parent.document"; else echo "d"; ?>.referrer)+"' height=1 ",
"width=1>");}srb="uk";srw="uk";//--></script><script type="text/javascript" language="javascript1.2"><!--
s=screen;srw=s.width;an!="Netscape"?
srb=s.colorDepth:srb=s.pixelDepth;//-->
</script><script type="text/javascript" language="javascript"><!--
pr();//--></script><noscript><img src="http<?php if($_SERVER["HTTPS"]=="on") echo "s"; ?>://stats.webtastic.nl/count.php?siteid=<?php echo $siteid ?>&pageid=<?php echo $pageid; ?>&javascript=0&vc=<?php echo $webstats["visitcounter"] ?>&lvt=<?php echo $webstats["lastvisittime"]; if($extra) echo "&e=".urlencode($extra); ?>" height=1 width=1 alt=""></noscript><?php

}


if (!function_exists("webstatscookies")) {
	function webstatscookies() {
		global $webstats,$_COOKIE;
		header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
		setcookie("tempcounter[webstats]","on");
		if($_COOKIE["tempcounter"]["webstats"]<>"on") {
			$webstats["lastvisittime"]=$_COOKIE["visit"]["webstats"];
			$webstats["visitcounter"]=$_COOKIE["counter"]["webstats"]+1;
			setcookie("counter[webstats]",($_COOKIE["counter"]["webstats"]+1),time()+126144000,"/");
			setcookie("visit[webstats]",time(),time()+126144000,"/");
		} else $webstats["visitcounter"]=$_COOKIE["counter"]["webstats"];
	}
}

if(!function_exists("searchengine")) {
	function searchengine() {
		global $HTTP_USER_AGENT;
		$searchenginenames = Array("spider","crawler","robot","lycos","infoseek","jeeves","scooter","googlebot","diibot","arachnia","linkwalker","eidetica","webcraft","phpdig","aspseek");
		while(list($keys,$values) = each($searchenginenames)) {
			if(eregi($values,$HTTP_USER_AGENT)) {
				return true;
				break;
			}
		}
	}
}

if(!function_exists("checkbox2db")) {
	function checkbox2db($array) {
		while(list($key,$value)=each($array)) {
			if($return) $return.=",".$key; else $return=$key;
		}
		return $return;
	}
}

#if(!function_exists("d2checkbox")) {
#	function d2checkbox($all,$one) {
#		$all_split=split(",",$all);
#		if(in_array($one,$all_split)) {
#			return true;
#		} else {
#			return false;
#		}
#	}
#}

function wt_htmlentities($text,$clicklinks=false,$li=false) {
	$text=wt_he($text);
	$text=ereg_replace("&euro; ","&euro;&nbsp;",$text);

	# Zorgen dat regel met beginspaties correct inspringt
	$text=preg_replace("/\n     /","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$text);
	$text=preg_replace("/\n    /","&nbsp;&nbsp;&nbsp;&nbsp;",$text);
	$text=preg_replace("/\n   /","&nbsp;&nbsp;&nbsp;",$text);
	$text=preg_replace("/\n  /","&nbsp;&nbsp;",$text);

	if($clicklinks) {
		if($li) {
			$text=ereg_replace("^- ","<li>",$text);
			$text=ereg_replace(chr(10)."- ","<li>",$text);
		}
		$text=ereg_replace("&amp;","&",$text);
		# http klikbaar maken
		$text=eregi_replace("(https?://[a-z0-9\./?&%=_\-]+)","<a href=\"\\1\" target=\"_blank\" rel=\"nofollow\">\\1</a>",$text);
		# E-mail klikbaar maken
		$text=eregi_replace("([^a-z0-9]|^)([0-9a-z][-_0-9a-z.]*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,4})([^a-z]|$)","\\1<a href=\"mailto:\\2\">\\2</a>\\4",$text);
		# www klikbaar maken
		$text=ereg_replace("([^/])(www\.[a-z0-9-]+\.[a-z]{1,4})([^a-z0-9])","\\1<a href=\"http://\\2/\" target=\"_blank\" rel=\"nofollow\">\\2</a>\\3",$text);
	}
	return $text;
}

function wt_he_forumpost($text,$settings="") {
	//
	// comment tonen met klikbare links
	//

	$text=preg_replace("/&#[0-9]+;/"," ",$text);

	$text=wt_he($text);
	$text=ereg_replace("&euro; ","&euro;&nbsp;",$text);

	if($settings["list"]) {
		$text=ereg_replace("^- ","<li>",$text);
		$text=ereg_replace(chr(10)."- ","<li>",$text);
	}
	$text=ereg_replace("&amp;","&",$text);

# http://www.nieuwsblad.be/sportwereld/cnt/DMF20130423_046
	# http klikbaar maken
#	$text=eregi_replace("^(https?://[a-z0-9\./?&%=\-]+)","<a href=\"\\1\" target=\"_blank\" rel=\"nofollow\">\\1</a>",$text);
#	$text=eregi_replace("([^=>\"]|^)(https?://[a-z0-9\./?&%=\-_\(\)]+)","\\1<a href=\"\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>",$text);
	$text=preg_replace("@([^=>\"]|^)(https?://[a-zA-Z0-9\./?&%=\-_\(\)]+)@","\\1<a href=\"\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>",$text);

	if($settings["twitter"]) {
		# Twitter-handles klikbaar maken
		$text=ereg_replace("([^=>\"a-zA-Z0-9]|^)@([a-zA-Z0-9_]+)([^a-z0-9_]|$)","\\1<a href=\"https://twitter.com/\\2\" target=\"_blank\" rel=\"nofollow\">@\\2</a>\\3",$text);
	}

	# E-mail klikbaar maken
	$text=eregi_replace("([^a-z0-9]|^)([0-9a-z][-_0-9a-z.+]*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,4})([^a-z]|$)","\\1<a href=\"mailto:\\2\">\\2</a>\\4",$text);

	# www klikbaar maken
	$text=ereg_replace("([^/]|^)(www\.[a-z0-9-]+\.[a-z]{1,4})([^a-z0-9]|$)","\\1<a href=\"http://\\2/\" target=\"_blank\" rel=\"nofollow\">\\2</a>\\3",$text);

	if($settings["opmaakcodes"]) {

		# interne [link=http://url/]tekst[/link] omzetten
		// $text=ereg_replace("\[link=(https://movieyell\.com/[^]]+)\]([^[]+)\[/link\]","<a href=\"\\1\">\\2</a>",$text);

		# externe [link=http://url/]tekst[/link] omzetten
		$text=ereg_replace("\[link=(http[^]]+)\]([^[]+)\[/link\]","<a href=\"\\1\" target=\"_blank\" rel=\"nofollow\">\\2</a>",$text);

		# [b] bold maken
		$text=ereg_replace("\[b\]([^[]+)\[/b\]","<b>\\1</b>",$text);

		# [i] italics maken
		$text=ereg_replace("\[i\]([^[]+)\[/i\]","<i>\\1</i>",$text);
	}

	$text=nl2br($text);

	return $text;
}

function wt_has_value($value) {
	if($value<>"" or $value=="0") {
		return true;
	} else {
		return false;
	}
}

function wt_he($text) {
	global $vars;
	if($vars["wt_htmlentities_cp1252"]) {
		$text=htmlentities($text,ENT_COMPAT,'cp1252');
	} elseif($vars["wt_htmlentities_utf8"]) {
		$text=htmlentities($text,ENT_COMPAT,'UTF-8');
	} else {
		$text=htmlentities($text);
	}
	return $text;
}

function wt_url_zonder_http($url) {

	// zet een volledige URL om in een mooi toonbare URL
	// bijvoorbeeld: http://www.webtastic.nl/ wordt www.webtastic.nl

	// http(s) verwijderen
	$url = preg_replace("@^https?://@","",$url);

	// laatste slash verwijderen (als er maar 1 slash in de url voorkomt)
	if(preg_match("@/$@",$url) and substr_count($url, "/")==1) {
		$url = substr($url, 0, -1);
	}

	return $url;
}

function wt_hernoem_classname($classname) {

	// functie om te gebruiken bij function __autoload($classname)
	// zorg ervoor dat classnames naar de juiste files linken

	$hernoem["form2"]="form";
	$hernoem["Login"]="login";
	$hernoem["cms_layout"]="cms.layout";

	if($hernoem[$classname]) {
		$classname=$hernoem[$classname];
	}

	return $classname;

}

function wt_complex_password_hash($password,$salt,$password_already_md5=false) {
	if(!$password_already_md5) $password=md5($password);
	$return="_".sha1($password."WTSALT_wtsalt".$salt);
	return $return;
}

class wt_table {
	function wt_table() {
		return true;
	}

	function header($title,$cols=2) {
		$this->cols=$cols;
		if(!$this->starttable) $return.=$this->starttable();
		$return.="<TR><TD colspan=\"".$cols."\" bgcolor=\"".$this->bordercolor."\"><FONT color=\"#FFFFFF\"><B>".wt_he($title)."</B></FONT></TD></TR>";
		return $return;
	}

	function starttable() {
		if(!isset($this->cellspacing)) $this->cellspacing="0";
		if(!isset($this->cellpadding)) $this->cellpadding="4";
		if(!isset($this->bordercolor)) $this->bordercolor="";
		if(!isset($this->border)) $this->border="1";
		if(!isset($this->width)) $this->width="600";
		if(!isset($this->columnwidth)) $this->columnwidth="200";
		$this->starttable=true;
		return "<TABLE width=\"".$this->width."\" border=\"".$this->border."\" cellspacing=\"".$this->cellspacing."\" cellpadding=\"".$this->cellpadding."\" bordercolor=\"".$this->bordercolor."\">";
	}

	function rowfunction($key,$value,$html=false,$type="row") {
		if(!$this->starttable) $return.=$this->starttable();
		$return.="<TR><TD valign=\"top\"";
		if($type=="single_row") {
			if($this->cols==2) $return.=" colspan=\"2\"";
			$return.=">";
			if($html) {
				if($key) $return.=$key; else $return.="&nbsp;";
			} else {
				if($key) $return.=nl2br(wt_he($key)); else $return.="&nbsp;";
			}
		} elseif($type=="row") {
			$return.=" width=\"".$this->columnwidth."\" nowrap";
			$return.=">".wt_he($key)."</TD>";
			$return.="<TD valign=\"top\" width=\"".($this->width-$this->columnwidth)."\">";
			if($html) {
				if($value) $return.=$value; else $return.="&nbsp;";
			} else {
				if($value) $return.=nl2br(wt_he($value)); else $return.="&nbsp;";
			}
		}
		$return.="</TD></TR>";
		return $return;
	}

	function row($key,$value,$html=false) {
		return $this->rowfunction($key,$value,$html);
	}

	function single_row($key,$html=false) {
		return $this->rowfunction($key,$value,$html,"single_row");
	}

	function footer() {
		$this->starttable=false;
		return "</TABLE>";
	}

}

function wt_random($low,$high,$srand) {



}

function wt_dump($array,$html=true) {
	ob_start();
#	if($html) echo "</td></tr></table></td></tr></table></td></tr></table><hr><PRE>";
	if(is_array($array)) {
		echo "Aantal array-items: ".count($array)."\n";
		print_r($array);
	} else {
		echo "Geen array: ".$array;
	}

	$return=ob_get_contents();
	ob_end_clean();
	if($html) {
		$return="<hr><pre>".nl2br(wt_he($return))."</pre><hr>";
	}
	return $return;
}

function wt_mail($to,$subject,$body,$from="",$fromname="",$returnpath="") {
	if(!$fromname) {
		if(defined("wt_mail_fromname")) {
			$fromname=wt_mail_fromname;
		} else {
			$fromname="WebTastic wt_mail";
		}
	}
	if(!$from) {
		if(defined("wt_mail_from")) {
			$from=wt_mail_from;
		} else {
			$from="wt_mail@webtastic.nl";
		}
	}
	$mail=new wt_mail;
	$mail->fromname=$fromname;
	$mail->from=$from;
	if($returnpath) $mail->returnpath=$returnpath;
	$mail->to=$to;
	$mail->subject=$subject;
	$mail->plaintext=$body;
	$mail->send();
}

function wt_mailphpinfo($address,$presubject="") {
	$phpinfo="http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"].($_SERVER["QUERY_STRING"] ? "?".$_SERVER["QUERY_STRING"] : "")."<P>";
	ob_start();
	phpinfo();
	$phpinfo.=ob_get_contents();
	ob_end_clean();
	mail($address,($presubject ? $presubject." - " : "")."PHPInfo - ".date("r"),$phpinfo,"From: WebTastic Automail <automail@webtastic.nl>\nContent-Type: text/html;");
}

function wt_link($url,$targetblank=true) {
	$return="<A HREF=\"clicklink.php?url=".urlencode($url)."\"";
	if($targetblank) $return.=" target=\"_blank\"";
	$return.=">";
	return $return;
}

function wt_test() {
	$ip=array("192.168.0.10","192.168.0.20","192.168.0.30","131.211.227.225","80.126.96.7","212.238.232.22");
	if(in_array($_SERVER["REMOTE_ADDR"],$ip)) return true; else return false;
}

function wt_sort($array,$desc=false) {
	while(list($key,$value)=each($array)) {
		if(ereg("^[0-9]{1,20}$",$value)) {
			$sortvalue=substr("00000000000000000000000".$value,-20);
		} else {
			$sortvalue=strtolower(wt_stripaccents($value));
		}
		$sort[$key]=$sortvalue;
	}
	if($desc) {
		arsort($sort);
	} else {
		asort($sort);
	}
	while(list($key,$value)=each($sort)) {
		$return[$key]=$array[$key];
	}
	return $return;
}

function wt_debugtimer($pointer="") {
	if($_SERVER["REMOTE_ADDR"]=="82.173.186.80" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
		global $wt_debugtimer;
		$wt_debugtimer["counter"]++;
		if(!$pointer) $pointer=$wt_debugtimer["counter"];
		$pointer=ereg_replace(" ","&nbsp;",substr($pointer."           ",0,8));
		list($usec,$sec)=explode(" ",microtime());
		$timenow=((float)$usec+(float)$sec);
	#	$timenow=$usec+$sec;
		if($wt_debugtimer["time"]==0) {
			$wt_debugtimer["time"]=$timenow;
			$wt_debugtimer["start"]=$timenow;
		}
		$timedif=round($timenow-$wt_debugtimer["time"],4);
		if($timedif>0.2) echo "<B>";
		echo "<FONT FACE=\"Courier\">".$pointer." === ".sprintf("%02.4f\n",round($timenow-$wt_debugtimer["start"],4))." === ".$timedif."</FONT><BR>";
		if($timedif>0.2) echo "</B>";
		$wt_debugtimer["time"]=$timenow;
	}
}

function wt_microtime_float() {
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

function wt_naam($voornaam='',$tussenvoegsel='',$achternaam,$achternaameerst=false,$voorletters=false) {
        global $vars, $isMobile;
        
	if($voornaam) $voornaam=trim($voornaam);
	if($tussenvoegsel) $tussenvoegsel=trim($tussenvoegsel);
	$achternaam=trim($achternaam);

	if($voorletters and $voornaam) {
		if(substr($voornaam,-1)<>".") $voornaam.=".";
	}
	if($achternaameerst) {
		$return=ucfirst($achternaam);
		if($voornaam or $tussenvoegsel) $return.=", ".($voornaam ? ucfirst($voornaam)." " : "").($tussenvoegsel ? $tussenvoegsel : "");
	} else {
		$return=ucfirst($voornaam);
		if($tussenvoegsel) {
			if($return) $return.=" ".$tussenvoegsel; else $return=$tussenvoegsel;
		}
		if($return) $return.=" ".ucfirst($achternaam); else $return=ucfirst($achternaam);
	}

	$return = preg_replace("@ {2,}@"," ",$return);
        if($isMobile){
                $nameInTussenvoegsel = explode(" ", $return);

                foreach($nameInTussenvoegsel as $name){
                    if(in_array(strtolower($name), $vars["availableTussenvoegsel"])){
                        $names[] = lcfirst($name);
                    }else{
                        $names[] = $name;
                    }
                }
                $finalName = implode(" ", $names);

                return $finalName;
        }else {
                return $return;
        }
}

function mm_connect($action,$field,$value='') {
	if($_GET["vid"]) {
		$file="http://www.mailingmanager.nl/connect.php?action=".$action."&field=".urlencode($field)."&value=".urlencode($value)."&vid=".urlencode($_GET["vid"]);
		$data=@implode("",@file($file));
		if($data=="MM_OKAY") {
			return true;
		} elseif($data) {
			return $data;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function mm_newmember($email,$listcode,$values) {
	$mail=new wt_mail;
	$mail->fromname="MM_MAIL";
	$mail->from="mm_mail@webtastic.nl";
	$mail->toname="MM_MAIL";
	$mail->to="subscribe-".$listcode."@mailingmanager.nl";
	$mail->subject=md5("server_addr".$_SERVER["SERVER_ADDR"]."MM")."-".md5("email".$email."MM");
	$mail->plaintext="MM_MAIL";
	$mail->html="MM_MAIL\n";
	$mail->html.="<div style=\"display:none\">WT_MM_MAIL_BEGIN";

	$values["email"]=$email;
	@reset($values);
	while(list($key,$value)=@each($values)) {
		$counter++;
		$mail->html.="_WT_MM_KEY".$counter."_OPEN_".$key."_WT_MM_KEY".$counter."_CLOSE_\n";
		$mail->html.="_WT_MM_VALUE".$counter."_OPEN_".$value."_WT_MM_VALUE".$counter."_CLOSE_\n";
	}
	$mail->html.="WT_MM_MAIL_EIND</div>";
	$mail->send();
}

function mm_connect1($action,$field,$value='') {
	if($_GET["mml"] or $_GET["mmu"] or $_GET["mid"]) {
		$file="http://www.mailingmanager.nl/connect.php?action=".$action."&field=".urlencode($field)."&value=".urlencode($value)."&mid=".urlencode($_GET["mid"])."&mmu=".urlencode($_GET["mmu"])."&mml=".urlencode($_GET["mml"]);
		$data=@implode("",@file($file));
		if($data=="MM_OKAY") {
			return true;
		} elseif($data) {
			return $data;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function wt_create_id($table,$field,$length=8) {
	global $db0;
	$chars="abcdefghijklmnopqrstuvwxyz0123456789";
	while(!$okay) {
		$teller++;
#		$id=substr(md5(uniqid(rand(),true)),0,$length);
		while(strlen($id)<$length) {
			$num=mt_rand(0,strlen($chars)-1);
			$id=$id.substr($chars,$num,1);
		}
		$db0->query("SELECT ".$field." FROM ".$table." WHERE ".$field."='".$id."';");
		if(!$db0->num_rows()) $okay=true;
		if($teller>500) {
			$id="";
			$okay=true;
		}
	}
	return $id;
}

function wt_generate_password($length=6,$complex_password=true) {
	while(!$password_okay) {
		# geen letter-l <-> cijfer-1 en letter-o <-> cijfer-0 (kunnen verward worden)
		if($complex_password) {
			$chars = "abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789";
			$chars=$chars.$chars."!#$%&*";
		} else {
			$chars = "abcdefghijkmnpqrstuvwxyz23456789";
		}
		$i=0;
		$pass='';
		while($i<$length) {
			$num = mt_rand(0,strlen($chars)-1);
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		if($complex_password) {
			if(ereg("[a-z]",$pass) and ereg("[A-Z]",$pass) and ereg("[0-9]",$pass) and ereg("^[a-zA-Z0-9]",$pass) and ereg("[a-zA-Z0-9]$",$pass)) {
				$password_okay=true;
			}
		} else {
			if(ereg("[a-z]",$pass) and ereg("[0-9]",$pass)) {
				$password_okay=true;
			}
		}
	}
	return $pass;
}

function wt_oras($text) {
	# Oracle Addslashes
	$return=ereg_replace("\'","''",$text);
	return $return;
}

function wt_img_width_height($pic) {
	list($width,$height,$type,$attr)=@getimagesize($pic);
	if(!$width) $width=100;
	if(!$height) $height=100;
	$return="src=\"".$pic."\" width=\"".$width."\" height=\"".$height."\"";
	return $return;
}

function wt_addaccents($value) {
	$value=eregi_replace("a","[a|‰|Â|‡|·|‚|„]",$value);
	$value=eregi_replace("e","[e|Î|Ë|È|Í]",$value);
	$value=eregi_replace("i","[i|Ô|Ï|Ì|Ó]",$value);
	$value=eregi_replace("o","[o|ˆ|Û|Ú|Ù|ı|¯]",$value);
	$value=eregi_replace("u","[u|¸|˘|˙|˚]",$value);
	$value=eregi_replace("n","[n|Ò]",$value);
	$value=eregi_replace("c","[c|Á]",$value);
	return $value;
}

function wt_baseurl() {
	return "http://".($_SERVER["HTTP_HOST"]=="on" ? "s" : "").$_SERVER["HTTP_HOST"].ereg_replace("/[^/]+$","/",$_SERVER["PHP_SELF"]);
}

function maketime ($hour = false, $minute = false, $second = false, $month = false, $date = false, $year = false){

	   // This function can undo the Win32 error to calculate datas before 1-1-1970 (by TOTH = igtoth@netsite.com.br)
	   // For centuries, the Egyptians used a (12 * 30 + 5)-day calendar
	   // The Greek began using leap-years in around 400 BC
	   // Ceasar adjusted the Roman calendar to start with Januari rather than March
	   // All knowledge was passed on by the Arabians, who showed an error in leaping
	   // In 1232 Sacrobosco (Eng.) calculated the error at 1 day per 288 years
	   //    In 1582, Pope Gregory XIII removed 10 days (Oct 15-24) to partially undo the
	   // error, and he instituted the 400-year-exception in the 100-year-exception,
	   // (notice 400 rather than 288 years) to undo the rest of the error
	   // From about 2044, spring will again coincide with the tropic of Cancer
	   // Around 4100, the calendar will need some adjusting again

	   if ($hour === false)  $hour  = Date ("G");
	   if ($minute === false) $minute = Date ("i");
	   if ($second === false) $second = Date ("s");
	   if ($month === false)  $month  = Date ("n");
	   if ($date === false)  $date  = Date ("j");
	   if ($year === false)  $year  = Date ("Y");

	   if ($year >= 1970) return mktime ($hour, $minute, $second, $month, $date, $year);

	   //    date before 1-1-1970 (Win32 Fix)
	   $m_days = Array (31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	   if ($year % 4 == 0 && ($year % 100 > 0 || $year % 400 == 0))
	   {
		   $m_days[1] = 29; // non leap-years can be: 1700, 1800, 1900, 2100, etc.
	   }

	   //    go backward (-), based on $year
	   $d_year = 1970 - $year;
	   $days = 0 - $d_year * 365;
	   $days -= floor ($d_year / 4);          // compensate for leap-years
	   $days += floor (($d_year - 70) / 100);  // compensate for non-leap-years
	   $days -= floor (($d_year - 370) / 400); // compensate again for giant leap-years

	   //    go forward (+), based on $month and $date
	   for ($i = 1; $i < $month; $i++)
	   {
		   $days += $m_days [$i - 1];
	   }
	   $days += $date - 1;

	   //    go forward (+) based on $hour, $minute and $second
	   $stamp = $days * 86400;
	   $stamp += $hour * 3600;
	   $stamp += $minute * 60;
	   $stamp += $second;

	   return $stamp;
}

function wt_csvconvert($string,$delimiter=",",$isstring=false) {
	if(defined("wt_csvconvert_delimiter")) {
		$delimiter=wt_csvconvert_delimiter;
	}
	$string=ereg_replace("\r\n","\n",$string);
	if(ereg("\n",$string)) $isstring=true;
	if(ereg($delimiter,$string) or $isstring) {
		$return="\"".ereg_replace("\"","\"\"",$string)."\"";
	} else {
		$return=$string;
	}
	return $return;
}

function wt_csvrevert($string,$delimiter=",") {
	$elements = explode($delimiter, $string);
	for ($i = 0; $i < count($elements); $i++) {
		$nquotes = substr_count($elements[$i], '"');
		if ($nquotes %2 == 1) {
			for ($j = $i+1; $j < count($elements); $j++) {
				if (substr_count($elements[$j], '"') %2 == 1) { // Look for an odd-number of quotes
					// Put the quoted string's pieces back together again
					array_splice($elements, $i, $j-$i+1,
					implode($delimiter, array_slice($elements, $i, $j-$i+1)));
					break;
				}
			}
		}
		if ($nquotes > 0) {
			// Remove first and last quotes, then merge pairs of quotes
			$qstr =& $elements[$i];
			$qstr = substr_replace($qstr, '', strpos($qstr, '"'), 1);
			$qstr = substr_replace($qstr, '', strrpos($qstr, '"'), 1);
			$qstr = str_replace('""', '"', $qstr);
		}
	}
	return $elements;
}

function wt_csv_to_array($filename='', $delimiter=';') {
	// zet csv om naar array: functie werkt niet goed?? (21-01-2013)
	if(!file_exists($filename) || !is_readable($filename)) return FALSE;

	$header = NULL;
	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE) {
#		$datateller=0;
		while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
			if(!$header) $header = $row;
			$data["names"][] = array_combine($header, $row);
			$data["numbers"][] = $row;
#			$datateller++;
		}
		fclose($handle);
	}
	return $data;
}

function wt_dirsize($dirName = '.') {
	$dir  = dir($dirName);
	$size = 0;
	while($file = $dir->read()) {
		if ($file != '.' && $file != '..') {
			if (is_dir($file)) {
				$size += wt_dirsize($dirName . '/' . $file);
			} else {
				$size += filesize($dirName . '/' . $file);
			}
		}
	}
	$dir->close();
	return $size;
}

function wt_encrypt($secret,$text) {
	$secret=md5($secret);
	$crypt=new pcrypt(MODE_ECB,"BLOWFISH",$secret);
	return $crypt->encrypt($text);
}

function wt_decrypt($secret,$text) {
	$secret=md5($secret);
	$crypt=new pcrypt(MODE_ECB,"BLOWFISH",$secret);
	return $crypt->decrypt($text);
}

// 1:        |----------|
// 2: |----------|
// 2:           |-----|
// 2:      |---------------|
// 2:              |----------|

function wt_datumoverlap($begin1,$eind1,$begin2,$eind2) {
	$return=false;
	// 2 begint eerder en eindigt later dan 1
	if($begin2<=$begin1 and $begin2<=$eind1 and $eind2>=$eind1 and $eind2>=$begin1) {
		$return=true;
	// 2 begint eerder (of gelijk) dan 1 begint en eindigt eerder (of gelijk) dan 1	eindigt
	} elseif($begin2<=$begin1 and $begin2<=$eind1 and $eind2<=$eind1 and $eind2>=$begin1) {
		$return=true;
	// 2 begint later en eindigt eerder dan 1
	} elseif($begin2>=$begin1 and $begin2<=$eind1 and $eind2<=$eind1 and $eind2>=$begin1) {
		$return=true;
	// 2 begint later (of gelijk) dan 1 begint en eindigt later (of gelijk) dan 1
		} elseif($begin2>=$begin1 and $begin2<=$eind1 and $eind2>=$eind1 and $eind2>=$begin1) {
		$return=true;
	}
	return $return;
}

function wt_mssqldate($string) {
	if(ereg("^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$",$string,$regs)) {
		$return=mktime($regs[4],$regs[5],$regs[6],$regs[2],$regs[3],$regs[1]);
		return $return;
	} elseif(ereg("^([0-9]{4})-([0-9]{2})-([0-9]{2})$",$string,$regs)) {
		$return=mktime(0,0,0,$regs[2],$regs[1],$regs[3]);
		return $return;
	} else {
		return false;
	}
}

function wt_convert_database_to_utf8() {
	$db=new DB_sql;
	$db2=new DB_sql;
	$db3=new DB_sql;

	$db->query("SHOW TABLES;");
	while($db->next_record()) {

		$table=$db->f("Tables_in_".$db->Database);
		$query="ALTER TABLE `".$table."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
		echo $query."<br>";
#		$db2->query($query);

		$db2->query("SHOW FULL COLUMNS FROM `".$table."`;");
		while($db2->next_record()) {
			if($db2->f("Collation")=="latin1_swedish_ci") {
				$query="ALTER TABLE `".$table."` MODIFY `".$db2->f("Field")."` ".$db2->f("Type")." CHARACTER SET utf8 COLLATE utf8_general_ci ".($db2->f("Null")=="YES" ? "NULL" : "NOT NULL")." DEFAULT ".($db2->f("Default")=="NULL" ? "NULL" : "'".addslashes($db2->f("Default"))."'").";";
				echo $query."<br>";
				$db3->query($query);
			}
		}
	}
}

function wt_get_twitter_url($url,$query_string_array,$oauth_consumer_key,$oauth_consumer_secret,$oauth_access_token,$oauth_access_token_secret) {
	//
	// twitter-api (1.1) raadplegen
	//
	if(is_array($query_string_array)) {
		ksort($query_string_array);
		while(list($key,$value)=each($query_string_array)) {
			$oauth[$key]=$value;
			$query_string.="&".$key."=".urlencode($value);
		}
	}

	$oauth["oauth_consumer_key"]=$oauth_consumer_key;
	$oauth["oauth_nonce"]=time();
	$oauth["oauth_signature_method"]='HMAC-SHA1';
	$oauth["oauth_token"]=$oauth_access_token;
	$oauth["oauth_timestamp"]=time();
	$oauth["oauth_version"]="1.0";

	$r = array();
	ksort($oauth);
	foreach($oauth as $key=>$value){
		$r[] = "$key=" . rawurlencode($value);
	}
	$base_info="GET&" . rawurlencode($url) . '&' . rawurlencode(implode('&', $r));



	$composite_key = rawurlencode($oauth_consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
	$oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
	$oauth['oauth_signature'] = $oauth_signature;

	if($query_string) {
		$url.="?".substr($query_string,1);
	}

	// Make Requests
	$r = 'Authorization: OAuth ';
	$values = array();
	foreach($oauth as $key=>$value)
		$values[] = "$key=\"" . rawurlencode($value) . "\"";
	$r .= implode(', ', $values);
	$header = array($r, 'Expect:');

	$options = array( CURLOPT_HTTPHEADER => $header,
					  CURLOPT_HEADER => false,
					  CURLOPT_URL => $url ,
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_SSL_VERIFYPEER => false);


	$feed = curl_init();
	curl_setopt_array($feed, $options);

	$return["output"] = curl_exec($feed);
	$return["info"] = curl_getinfo($feed);

	curl_close($feed);

	return $return;

}

if (!function_exists("imap_8bit")) {
	function imap_8bit($sText,$bEmulate_imap_8bit=true) {
	  // split text into lines
	  $aLines=explode(chr(13).chr(10),$sText);

	  for ($i=0;$i<count($aLines);$i++) {
		$sLine =& $aLines[$i];
		if (strlen($sLine)===0) continue; // do nothing, if empty

		$sRegExp = '/[^\x09\x20\x21-\x3C\x3E-\x7E]/e';

		// imap_8bit encodes x09 everywhere, not only at lineends,
		// for EBCDIC safeness encode !"#$@[\]^`{|}~,
		// for complete safeness encode every character :)
		if ($bEmulate_imap_8bit)
		  $sRegExp = '/[^\x20\x21-\x3C\x3E-\x7E]/e';

		$sReplmt = 'sprintf( "=%02X", ord ( "$0" ) ) ;';
		$sLine = preg_replace( $sRegExp, $sReplmt, $sLine );

		// encode x09,x20 at lineends
		{
		  $iLength = strlen($sLine);
		  $iLastChar = ord($sLine{$iLength-1});

		  //              !!!!!!!!
		  // imap_8_bit does not encode x20 at the very end of a text,
		  // here is, where I don't agree with imap_8_bit,
		  // please correct me, if I'm wrong,
		  // or comment next line for RFC2045 conformance, if you like
		  if (!($bEmulate_imap_8bit && ($i==count($aLines)-1)))

		  if (($iLastChar==0x09)||($iLastChar==0x20)) {
			$sLine{$iLength-1}='=';
			$sLine .= ($iLastChar==0x09)?'09':'20';
		  }
		}    // imap_8bit encodes x20 before chr(13), too
		// although IMHO not requested by RFC2045, why not do it safer :)
		// and why not encode any x20 around chr(10) or chr(13)
		if ($bEmulate_imap_8bit) {
		  $sLine=str_replace(' =0D','=20=0D',$sLine);
		  //$sLine=str_replace(' =0A','=20=0A',$sLine);
		  //$sLine=str_replace('=0D ','=0D=20',$sLine);
		  //$sLine=str_replace('=0A ','=0A=20',$sLine);
		}

		// finally split into softlines no longer than 76 chars,
		// for even more safeness one could encode x09,x20
		// at the very first character of the line
		// and after soft linebreaks, as well,
		// but this wouldn't be caught by such an easy RegExp
		preg_match_all( '/.{1,73}([^=]{0,2})?/', $sLine, $aMatch );
		$sLine = implode( '=' . chr(13).chr(10), $aMatch[0] ); // add soft crlf's
	  }

	  // join lines into text
	  return implode(chr(13).chr(10),$aLines);
	}
}

class wt_pid {

	protected $filename;
	public $already_running = false;

	function __construct($directory) {

		$this->filename = $directory . '/' . basename($_SERVER['PHP_SELF']) . '.pid';

		if(is_writable($this->filename) || is_writable($directory)) {

			if(file_exists($this->filename)) {
				$pid = (int)trim(file_get_contents($this->filename));
				if(posix_kill($pid, 0)) {
					$this->already_running = true;
				}
			}

		}
		else {
			die("Cannot write to pid file '$this->filename'. Program execution halted.\n");
		}

		if(!$this->already_running) {
			$pid = getmypid();
			file_put_contents($this->filename, $pid);
		}

	}

	public function __destruct() {

		if(!$this->already_running && file_exists($this->filename) && is_writeable($this->filename)) {
			unlink($this->filename);
		}
	}
}

class SMTPMAIL {
	var $host="";
	var $port=25;
	var $error;
	var $state;
	var $con=null;
	var $greets="";

	function SMTPMAIL() {
		global $wt_smtpclass_con,$wt_smtpclass_state;
		$this->host="localhost";
		$this->close_after_send=true;
		$this->port=25;
		if(!$wt_smtpclass_state) $wt_smtpclass_state="DISCONNECTED";
	}
	function set_host($host)
	{
		$this->host=$host;
	}
	function set_port($port=25)
	{
		$this->port=$port;
	}
	function error()
	{
		return $this->error;
	}
	function connect($host="",$port=25) {
		global $wt_smtpclass_con,$wt_smtpclass_state;
		if(!empty($host))
			$this->host($host);
		$this->port=$port;
		if($wt_smtpclass_state!="DISCONNECTED")
		{
#				$this->error="Error : connection already open.";
			return true;
		}

		$wt_smtpclass_con=@fsockopen($this->host,$this->port,$errno,$errstr);
		if(!$wt_smtpclass_con)
		{
			$this->error="Error($errno):$errstr";
			return false;
		}
		$wt_smtpclass_state="CONNECTED";
		$this->greets=$this->get_line();
		$this->put_line("HELO srv01.webtastic.nl");
		$this->greets=$this->get_line();
		return true;
	}
	function send_smtp_mail($rcpt_to,$to,$subject,$data,$cc="",$from)
	{
		$ret=$this->connect();
		if(!$ret)
			return $ret;
		$this->put_line("MAIL FROM: $from");
		$response=$this->get_line();
		if(intval(strtok($response," "))!=250)
		{
			$this->error=strtok($response,"\r\n");
			return false;
		}
		$to_err=preg_split("/[,;]/",$rcpt_to);
		foreach($to_err as $mailto)
		{
			$this->put_line("RCPT TO: $mailto");
			$response=$this->get_line();
			if(intval(strtok($response," "))!=250)
			{
				$this->error=strtok($response,"\r\n");
				return false;
			}
		}
		if(!empty($cc))
		{
			$to_err=preg_split("/[,;]/",$cc);
			foreach($to_err as $mailto)
			{
				$this->put_line("RCPT TO: $mailto");
				$response=$this->get_line();
				if(intval(strtok($response," "))!=250)
				{
					$this->error=strtok($response,"\r\n");
					return false;
				}
			}
		}
		$this->put_line("DATA");
		$response=$this->get_line();
		if(intval(strtok($response," "))!=354)
		{
			$this->error=strtok($response,"\r\n");
			return false;
		}
		$this->put_line("To: $to");
		$this->put_line("Subject: $subject");
		$this->put_line($data);
		$this->put_line(".");
		$response=$this->get_line();
		if(intval(strtok($response," "))!=250)
		{
			$this->error=strtok($response,"\r\n");
			return false;
		}
		if($this->close_after_send) {
			$this->close();
		}
		return true;
	}
	// This function is used to get response line from server
	function get_line() {
		global $wt_smtpclass_con,$wt_smtpclass_state;
		while(!feof($wt_smtpclass_con))
		{
			$line.=fgets($wt_smtpclass_con);
			if(strlen($line)>=2 && substr($line,-2)=="\r\n")
				return(substr($line,0,-2));
		}
	}
	////This functiuon is to retrive the full response message from server

	////This functiuon is to send the command to server
	function put_line($msg="") {
		global $wt_smtpclass_con,$wt_smtpclass_state;
		return @fputs($wt_smtpclass_con,"$msg\r\n");
	}

	function close() {
		global $wt_smtpclass_con,$wt_smtpclass_state;
		@fclose($wt_smtpclass_con);
		$wt_smtpclass_con=null;
		$wt_smtpclass_state="DISCONNECTED";
	}
}



/**
ADOdb Date Library, part of the ADOdb abstraction library
	Version Number

	http://phplens.com/phpeverywhere/adodb_date_library

*/
define('ADODB_DATE_VERSION',0.33);

$ADODB_DATETIME_CLASS = (PHP_VERSION >= 5.2);

/*
	This code was originally for windows. But apparently this problem happens
	also with Linux, RH 7.3 and later!

	glibc-2.2.5-34 and greater has been changed to return -1 for dates <
	1970.  This used to work.  The problem exists with RedHat 7.3 and 8.0
	echo (mktime(0, 0, 0, 1, 1, 1960));  // prints -1

	References:
	 http://bugs.php.net/bug.php?id=20048&edit=2
	 http://lists.debian.org/debian-glibc/2002/debian-glibc-200205/msg00010.html
*/

if (!defined('ADODB_ALLOW_NEGATIVE_TS')) define('ADODB_NO_NEGATIVE_TS',1);

function adodb_date_test_date($y1,$m,$d=13)
{
	$h = round(rand()% 24);
	$t = adodb_mktime($h,0,0,$m,$d,$y1);
	$rez = adodb_date('Y-n-j H:i:s',$t);
	if ($h == 0) $h = '00';
	else if ($h < 10) $h = '0'.$h;
	if ("$y1-$m-$d $h:00:00" != $rez) {
		print "<b>$y1 error, expected=$y1-$m-$d $h:00:00, adodb=$rez</b><br>";
		return false;
	}
	return true;
}

function adodb_date_test_strftime($fmt)
{
	$s1 = strftime($fmt);
	$s2 = adodb_strftime($fmt);

	if ($s1 == $s2) return true;

	echo "error for $fmt,  strftime=$s1, adodb=$s2<br>";
	return false;
}

/**
	 Test Suite
*/
function adodb_date_test()
{

	for ($m=-24; $m<=24; $m++)
		echo "$m :",adodb_date('d-m-Y',adodb_mktime(0,0,0,1+$m,20,2040)),"<br>";

	error_reporting(E_ALL);
	print "<h4>Testing adodb_date and adodb_mktime. version=".ADODB_DATE_VERSION.' PHP='.PHP_VERSION."</h4>";
	@set_time_limit(0);
	$fail = false;

	// This flag disables calling of PHP native functions, so we can properly test the code
	if (!defined('ADODB_TEST_DATES')) define('ADODB_TEST_DATES',1);

	$t = time();


	$fmt = 'Y-m-d H:i:s';
	echo '<pre>';
	echo 'adodb: ',adodb_date($fmt,$t),'<br>';
	echo 'php  : ',date($fmt,$t),'<br>';
	echo '</pre>';

	adodb_date_test_strftime('%Y %m %x %X');
	adodb_date_test_strftime("%A %d %B %Y");
	adodb_date_test_strftime("%H %M S");

	$t = adodb_mktime(0,0,0);
	if (!(adodb_date('Y-m-d') == date('Y-m-d'))) print 'Error in '.adodb_mktime(0,0,0).'<br>';

	$t = adodb_mktime(0,0,0,6,1,2102);
	if (!(adodb_date('Y-m-d',$t) == '2102-06-01')) print 'Error in '.adodb_date('Y-m-d',$t).'<br>';

	$t = adodb_mktime(0,0,0,2,1,2102);
	if (!(adodb_date('Y-m-d',$t) == '2102-02-01')) print 'Error in '.adodb_date('Y-m-d',$t).'<br>';


	print "<p>Testing gregorian <=> julian conversion<p>";
	$t = adodb_mktime(0,0,0,10,11,1492);
	//http://www.holidayorigins.com/html/columbus_day.html - Friday check
	if (!(adodb_date('D Y-m-d',$t) == 'Fri 1492-10-11')) print 'Error in Columbus landing<br>';

	$t = adodb_mktime(0,0,0,2,29,1500);
	if (!(adodb_date('Y-m-d',$t) == '1500-02-29')) print 'Error in julian leap years<br>';

	$t = adodb_mktime(0,0,0,2,29,1700);
	if (!(adodb_date('Y-m-d',$t) == '1700-03-01')) print 'Error in gregorian leap years<br>';

	print  adodb_mktime(0,0,0,10,4,1582).' ';
	print adodb_mktime(0,0,0,10,15,1582);
	$diff = (adodb_mktime(0,0,0,10,15,1582) - adodb_mktime(0,0,0,10,4,1582));
	if ($diff != 3600*24) print " <b>Error in gregorian correction = ".($diff/3600/24)." days </b><br>";

	print " 15 Oct 1582, Fri=".(adodb_dow(1582,10,15) == 5 ? 'Fri' : '<b>Error</b>')."<br>";
	print " 4 Oct 1582, Thu=".(adodb_dow(1582,10,4) == 4 ? 'Thu' : '<b>Error</b>')."<br>";

	print "<p>Testing overflow<p>";

	$t = adodb_mktime(0,0,0,3,33,1965);
	if (!(adodb_date('Y-m-d',$t) == '1965-04-02')) print 'Error in day overflow 1 <br>';
	$t = adodb_mktime(0,0,0,4,33,1971);
	if (!(adodb_date('Y-m-d',$t) == '1971-05-03')) print 'Error in day overflow 2 <br>';
	$t = adodb_mktime(0,0,0,1,60,1965);
	if (!(adodb_date('Y-m-d',$t) == '1965-03-01')) print 'Error in day overflow 3 '.adodb_date('Y-m-d',$t).' <br>';
	$t = adodb_mktime(0,0,0,12,32,1965);
	if (!(adodb_date('Y-m-d',$t) == '1966-01-01')) print 'Error in day overflow 4 '.adodb_date('Y-m-d',$t).' <br>';
	$t = adodb_mktime(0,0,0,12,63,1965);
	if (!(adodb_date('Y-m-d',$t) == '1966-02-01')) print 'Error in day overflow 5 '.adodb_date('Y-m-d',$t).' <br>';
	$t = adodb_mktime(0,0,0,13,3,1965);
	if (!(adodb_date('Y-m-d',$t) == '1966-01-03')) print 'Error in mth overflow 1 <br>';

	print "Testing 2-digit => 4-digit year conversion<p>";
	if (adodb_year_digit_check(00) != 2000) print "Err 2-digit 2000<br>";
	if (adodb_year_digit_check(10) != 2010) print "Err 2-digit 2010<br>";
	if (adodb_year_digit_check(20) != 2020) print "Err 2-digit 2020<br>";
	if (adodb_year_digit_check(30) != 2030) print "Err 2-digit 2030<br>";
	if (adodb_year_digit_check(40) != 1940) print "Err 2-digit 1940<br>";
	if (adodb_year_digit_check(50) != 1950) print "Err 2-digit 1950<br>";
	if (adodb_year_digit_check(90) != 1990) print "Err 2-digit 1990<br>";

	// Test string formating
	print "<p>Testing date formating</p>";

	$fmt = '\d\a\t\e T Y-m-d H:i:s a A d D F g G h H i j l L m M n O \R\F\C2822 r s t U w y Y z Z 2003';
	$s1 = date($fmt,0);
	$s2 = adodb_date($fmt,0);
	if ($s1 != $s2) {
		print " date() 0 failed<br>$s1<br>$s2<br>";
	}
	flush();
	for ($i=100; --$i > 0; ) {

		$ts = 3600.0*((rand()%60000)+(rand()%60000))+(rand()%60000);
		$s1 = date($fmt,$ts);
		$s2 = adodb_date($fmt,$ts);
		//print "$s1 <br>$s2 <p>";
		$pos = strcmp($s1,$s2);

		if (($s1) != ($s2)) {
			for ($j=0,$k=strlen($s1); $j < $k; $j++) {
				if ($s1[$j] != $s2[$j]) {
					print substr($s1,$j).' ';
					break;
				}
			}
			print "<b>Error date(): $ts<br><pre>
&nbsp; \"$s1\" (date len=".strlen($s1).")
&nbsp; \"$s2\" (adodb_date len=".strlen($s2).")</b></pre><br>";
			$fail = true;
		}

		$a1 = getdate($ts);
		$a2 = adodb_getdate($ts);
		$rez = array_diff($a1,$a2);
		if (sizeof($rez)>0) {
			print "<b>Error getdate() $ts</b><br>";
				print_r($a1);
			print "<br>";
				print_r($a2);
			print "<p>";
			$fail = true;
		}
	}

	// Test generation of dates outside 1901-2038
	print "<p>Testing random dates between 100 and 4000</p>";
	adodb_date_test_date(100,1);
	for ($i=100; --$i >= 0;) {
		$y1 = 100+rand(0,1970-100);
		$m = rand(1,12);
		adodb_date_test_date($y1,$m);

		$y1 = 3000-rand(0,3000-1970);
		adodb_date_test_date($y1,$m);
	}
	print '<p>';
	$start = 1960+rand(0,10);
	$yrs = 12;
	$i = 365.25*86400*($start-1970);
	$offset = 36000+rand(10000,60000);
	$max = 365*$yrs*86400;
	$lastyear = 0;

	// we generate a timestamp, convert it to a date, and convert it back to a timestamp
	// and check if the roundtrip broke the original timestamp value.
	print "Testing $start to ".($start+$yrs).", or $max seconds, offset=$offset: ";
	$cnt = 0;
	for ($max += $i; $i < $max; $i += $offset) {
		$ret = adodb_date('m,d,Y,H,i,s',$i);
		$arr = explode(',',$ret);
		if ($lastyear != $arr[2]) {
			$lastyear = $arr[2];
			print " $lastyear ";
			flush();
		}
		$newi = adodb_mktime($arr[3],$arr[4],$arr[5],$arr[0],$arr[1],$arr[2]);
		if ($i != $newi) {
			print "Error at $i, adodb_mktime returned $newi ($ret)";
			$fail = true;
			break;
		}
		$cnt += 1;
	}
	echo "Tested $cnt dates<br>";
	if (!$fail) print "<p>Passed !</p>";
	else print "<p><b>Failed</b> :-(</p>";
}

/**
	Returns day of week, 0 = Sunday,... 6=Saturday.
	Algorithm from PEAR::Date_Calc
*/
function adodb_dow($year, $month, $day)
{
/*
Pope Gregory removed 10 days - October 5 to October 14 - from the year 1582 and
proclaimed that from that time onwards 3 days would be dropped from the calendar
every 400 years.

Thursday, October 4, 1582 (Julian) was followed immediately by Friday, October 15, 1582 (Gregorian).
*/
	if ($year <= 1582) {
		if ($year < 1582 ||
			($year == 1582 && ($month < 10 || ($month == 10 && $day < 15)))) $greg_correction = 3;
		 else
			$greg_correction = 0;
	} else
		$greg_correction = 0;

	if($month > 2)
		$month -= 2;
	else {
		$month += 10;
		$year--;
	}

	$day =  floor((13 * $month - 1) / 5) +
			$day + ($year % 100) +
			floor(($year % 100) / 4) +
			floor(($year / 100) / 4) - 2 *
			floor($year / 100) + 77 + $greg_correction;

	return $day - 7 * floor($day / 7);
}


/**
 Checks for leap year, returns true if it is. No 2-digit year check. Also
 handles julian calendar correctly.
*/
function _adodb_is_leap_year($year)
{
	if ($year % 4 != 0) return false;

	if ($year % 400 == 0) {
		return true;
	// if gregorian calendar (>1582), century not-divisible by 400 is not leap
	} else if ($year > 1582 && $year % 100 == 0 ) {
		return false;
	}

	return true;
}


/**
 checks for leap year, returns true if it is. Has 2-digit year check
*/
function adodb_is_leap_year($year)
{
	return  _adodb_is_leap_year(adodb_year_digit_check($year));
}

/**
	Fix 2-digit years. Works for any century.
	Assumes that if 2-digit is more than 30 years in future, then previous century.
*/
function adodb_year_digit_check($y)
{
	if ($y < 100) {

		$yr = (integer) date("Y");
		$century = (integer) ($yr /100);

		if ($yr%100 > 50) {
			$c1 = $century + 1;
			$c0 = $century;
		} else {
			$c1 = $century;
			$c0 = $century - 1;
		}
		$c1 *= 100;
		// if 2-digit year is less than 30 years in future, set it to this century
		// otherwise if more than 30 years in future, then we set 2-digit year to the prev century.
		if (($y + $c1) < $yr+30) $y = $y + $c1;
		else $y = $y + $c0*100;
	}
	return $y;
}

function adodb_get_gmt_diff_ts($ts)
{
	if (0 <= $ts && $ts <= 0x7FFFFFFF) { // check if number in 32-bit signed range) {
		$arr = getdate($ts);
		$y = $arr['year'];
		$m = $arr['mon'];
		$d = $arr['mday'];
		return adodb_get_gmt_diff($y,$m,$d);
	} else {
		return adodb_get_gmt_diff(false,false,false);
	}

}

/**
 get local time zone offset from GMT. Does not handle historical timezones before 1970.
*/
function adodb_get_gmt_diff($y,$m,$d)
{
static $TZ,$tzo;
global $ADODB_DATETIME_CLASS;

	if (!defined('ADODB_TEST_DATES')) $y = false;
	else if ($y < 1970 || $y >= 2038) $y = false;

	if ($ADODB_DATETIME_CLASS && $y !== false) {
		$dt = new DateTime();
		$dt->setISODate($y,$m,$d);
		if (empty($tzo)) {
			$tzo = new DateTimeZone(date_default_timezone_get());
		#	$tzt = timezone_transitions_get( $tzo );
		}
		return -$tzo->getOffset($dt);
	} else {
		if (isset($TZ)) return $TZ;
		$y = date('Y');
		$TZ = mktime(0,0,0,12,2,$y,0) - gmmktime(0,0,0,12,2,$y,0);
	}

	return $TZ;
}

/**
	Returns an array with date info.
*/
function adodb_getdate($d=false,$fast=false)
{
	if ($d === false) return getdate();
	if (!defined('ADODB_TEST_DATES')) {
		if ((abs($d) <= 0x7FFFFFFF)) { // check if number in 32-bit signed range
			if (!defined('ADODB_NO_NEGATIVE_TS') || $d >= 0) // if windows, must be +ve integer
				return @getdate($d);
		}
	}
	return _adodb_getdate($d);
}

/*
// generate $YRS table for _adodb_getdate()
function adodb_date_gentable($out=true)
{

	for ($i=1970; $i >= 1600; $i-=10) {
		$s = adodb_gmmktime(0,0,0,1,1,$i);
		echo "$i => $s,<br>";
	}
}
adodb_date_gentable();

for ($i=1970; $i > 1500; $i--) {

echo "<hr />$i ";
	adodb_date_test_date($i,1,1);
}

*/


$_month_table_normal = array("",31,28,31,30,31,30,31,31,30,31,30,31);
$_month_table_leaf = array("",31,29,31,30,31,30,31,31,30,31,30,31);

function adodb_validdate($y,$m,$d)
{
global $_month_table_normal,$_month_table_leaf;

	if (_adodb_is_leap_year($y)) $marr = $_month_table_leaf;
	else $marr = $_month_table_normal;

	if ($m > 12 || $m < 1) return false;

	if ($d > 31 || $d < 1) return false;

	if ($marr[$m] < $d) return false;

	if ($y < 1000 && $y > 3000) return false;

	return true;
}

/**
	Low-level function that returns the getdate() array. We have a special
	$fast flag, which if set to true, will return fewer array values,
	and is much faster as it does not calculate dow, etc.
*/
function _adodb_getdate($origd=false,$fast=false,$is_gmt=false)
{
static $YRS;
global $_month_table_normal,$_month_table_leaf;

	$d =  $origd - ($is_gmt ? 0 : adodb_get_gmt_diff_ts($origd));
	$_day_power = 86400;
	$_hour_power = 3600;
	$_min_power = 60;

	if ($d < -12219321600) $d -= 86400*10; // if 15 Oct 1582 or earlier, gregorian correction

	$_month_table_normal = array("",31,28,31,30,31,30,31,31,30,31,30,31);
	$_month_table_leaf = array("",31,29,31,30,31,30,31,31,30,31,30,31);

	$d366 = $_day_power * 366;
	$d365 = $_day_power * 365;

	if ($d < 0) {

		if (empty($YRS)) $YRS = array(
			1970 => 0,
			1960 => -315619200,
			1950 => -631152000,
			1940 => -946771200,
			1930 => -1262304000,
			1920 => -1577923200,
			1910 => -1893456000,
			1900 => -2208988800,
			1890 => -2524521600,
			1880 => -2840140800,
			1870 => -3155673600,
			1860 => -3471292800,
			1850 => -3786825600,
			1840 => -4102444800,
			1830 => -4417977600,
			1820 => -4733596800,
			1810 => -5049129600,
			1800 => -5364662400,
			1790 => -5680195200,
			1780 => -5995814400,
			1770 => -6311347200,
			1760 => -6626966400,
			1750 => -6942499200,
			1740 => -7258118400,
			1730 => -7573651200,
			1720 => -7889270400,
			1710 => -8204803200,
			1700 => -8520336000,
			1690 => -8835868800,
			1680 => -9151488000,
			1670 => -9467020800,
			1660 => -9782640000,
			1650 => -10098172800,
			1640 => -10413792000,
			1630 => -10729324800,
			1620 => -11044944000,
			1610 => -11360476800,
			1600 => -11676096000);

		if ($is_gmt) $origd = $d;
		// The valid range of a 32bit signed timestamp is typically from
		// Fri, 13 Dec 1901 20:45:54 GMT to Tue, 19 Jan 2038 03:14:07 GMT
		//

		# old algorithm iterates through all years. new algorithm does it in
		# 10 year blocks

		/*
		# old algo
		for ($a = 1970 ; --$a >= 0;) {
			$lastd = $d;

			if ($leaf = _adodb_is_leap_year($a)) $d += $d366;
			else $d += $d365;

			if ($d >= 0) {
				$year = $a;
				break;
			}
		}
		*/

		$lastsecs = 0;
		$lastyear = 1970;
		foreach($YRS as $year => $secs) {
			if ($d >= $secs) {
				$a = $lastyear;
				break;
			}
			$lastsecs = $secs;
			$lastyear = $year;
		}

		$d -= $lastsecs;
		if (!isset($a)) $a = $lastyear;

		//echo ' yr=',$a,' ', $d,'.';

		for (; --$a >= 0;) {
			$lastd = $d;

			if ($leaf = _adodb_is_leap_year($a)) $d += $d366;
			else $d += $d365;

			if ($d >= 0) {
				$year = $a;
				break;
			}
		}
		/**/

		$secsInYear = 86400 * ($leaf ? 366 : 365) + $lastd;

		$d = $lastd;
		$mtab = ($leaf) ? $_month_table_leaf : $_month_table_normal;
		for ($a = 13 ; --$a > 0;) {
			$lastd = $d;
			$d += $mtab[$a] * $_day_power;
			if ($d >= 0) {
				$month = $a;
				$ndays = $mtab[$a];
				break;
			}
		}

		$d = $lastd;
		$day = $ndays + ceil(($d+1) / ($_day_power));

		$d += ($ndays - $day+1)* $_day_power;
		$hour = floor($d/$_hour_power);

	} else {
		for ($a = 1970 ;; $a++) {
			$lastd = $d;

			if ($leaf = _adodb_is_leap_year($a)) $d -= $d366;
			else $d -= $d365;
			if ($d < 0) {
				$year = $a;
				break;
			}
		}
		$secsInYear = $lastd;
		$d = $lastd;
		$mtab = ($leaf) ? $_month_table_leaf : $_month_table_normal;
		for ($a = 1 ; $a <= 12; $a++) {
			$lastd = $d;
			$d -= $mtab[$a] * $_day_power;
			if ($d < 0) {
				$month = $a;
				$ndays = $mtab[$a];
				break;
			}
		}
		$d = $lastd;
		$day = ceil(($d+1) / $_day_power);
		$d = $d - ($day-1) * $_day_power;
		$hour = floor($d /$_hour_power);
	}

	$d -= $hour * $_hour_power;
	$min = floor($d/$_min_power);
	$secs = $d - $min * $_min_power;
	if ($fast) {
		return array(
		'seconds' => $secs,
		'minutes' => $min,
		'hours' => $hour,
		'mday' => $day,
		'mon' => $month,
		'year' => $year,
		'yday' => floor($secsInYear/$_day_power),
		'leap' => $leaf,
		'ndays' => $ndays
		);
	}


	$dow = adodb_dow($year,$month,$day);

	return array(
		'seconds' => $secs,
		'minutes' => $min,
		'hours' => $hour,
		'mday' => $day,
		'wday' => $dow,
		'mon' => $month,
		'year' => $year,
		'yday' => floor($secsInYear/$_day_power),
		'weekday' => gmdate('l',$_day_power*(3+$dow)),
		'month' => gmdate('F',mktime(0,0,0,$month,2,1971)),
		0 => $origd
	);
}
/*
		if ($isphp5)
				$dates .= sprintf('%s%04d',($gmt<=0)?'+':'-',abs($gmt)/36);
			else
				$dates .= sprintf('%s%04d',($gmt<0)?'+':'-',abs($gmt)/36);
			break;*/
function adodb_tz_offset($gmt,$isphp5)
{
	$zhrs = abs($gmt)/3600;
	$hrs = floor($zhrs);
	if ($isphp5)
		return sprintf('%s%02d%02d',($gmt<=0)?'+':'-',floor($zhrs),($zhrs-$hrs)*60);
	else
		return sprintf('%s%02d%02d',($gmt<0)?'+':'-',floor($zhrs),($zhrs-$hrs)*60);
	break;
}


function adodb_gmdate($fmt,$d=false)
{
	return adodb_date($fmt,$d,true);
}

// accepts unix timestamp and iso date format in $d
function adodb_date2($fmt, $d=false, $is_gmt=false)
{
	if ($d !== false) {
		if (!preg_match(
			"|^([0-9]{4})[-/\.]?([0-9]{1,2})[-/\.]?([0-9]{1,2})[ -]?(([0-9]{1,2}):?([0-9]{1,2}):?([0-9\.]{1,4}))?|",
			($d), $rr)) return adodb_date($fmt,false,$is_gmt);

		if ($rr[1] <= 100 && $rr[2]<= 1) return adodb_date($fmt,false,$is_gmt);

		// h-m-s-MM-DD-YY
		if (!isset($rr[5])) $d = adodb_mktime(0,0,0,$rr[2],$rr[3],$rr[1],false,$is_gmt);
		else $d = @adodb_mktime($rr[5],$rr[6],$rr[7],$rr[2],$rr[3],$rr[1],false,$is_gmt);
	}

	return adodb_date($fmt,$d,$is_gmt);
}


/**
	Return formatted date based on timestamp $d
*/
function adodb_date($fmt,$d=false,$is_gmt=false)
{
static $daylight;
global $ADODB_DATETIME_CLASS;

	if ($d === false) return ($is_gmt)? @gmdate($fmt): @date($fmt);
	if (!defined('ADODB_TEST_DATES')) {
		if ((abs($d) <= 0x7FFFFFFF)) { // check if number in 32-bit signed range
			if (!defined('ADODB_NO_NEGATIVE_TS') || $d >= 0) // if windows, must be +ve integer
				return ($is_gmt)? @gmdate($fmt,$d): @date($fmt,$d);

		}
	}
	$_day_power = 86400;

	$arr = _adodb_getdate($d,true,$is_gmt);

	if (!isset($daylight)) $daylight = function_exists('adodb_daylight_sv');
	if ($daylight) adodb_daylight_sv($arr, $is_gmt);

	$year = $arr['year'];
	$month = $arr['mon'];
	$day = $arr['mday'];
	$hour = $arr['hours'];
	$min = $arr['minutes'];
	$secs = $arr['seconds'];

	$max = strlen($fmt);
	$dates = '';

	$isphp5 = PHP_VERSION >= 5;

	/*
		at this point, we have the following integer vars to manipulate:
		$year, $month, $day, $hour, $min, $secs
	*/
	for ($i=0; $i < $max; $i++) {
		switch($fmt[$i]) {
		case 'T':
			if ($ADODB_DATETIME_CLASS) {
				$dt = new DateTime();
				$dt->SetDate($year,$month,$day);
				$dates .= $dt->Format('T');
			} else
				$dates .= date('T');
			break;
		// YEAR
		case 'L': $dates .= $arr['leap'] ? '1' : '0'; break;
		case 'r': // Thu, 21 Dec 2000 16:01:07 +0200

			// 4.3.11 uses '04 Jun 2004'
			// 4.3.8 uses  ' 4 Jun 2004'
			$dates .= gmdate('D',$_day_power*(3+adodb_dow($year,$month,$day))).', '
				. ($day<10?'0'.$day:$day) . ' '.date('M',mktime(0,0,0,$month,2,1971)).' '.$year.' ';

			if ($hour < 10) $dates .= '0'.$hour; else $dates .= $hour;

			if ($min < 10) $dates .= ':0'.$min; else $dates .= ':'.$min;

			if ($secs < 10) $dates .= ':0'.$secs; else $dates .= ':'.$secs;

			$gmt = adodb_get_gmt_diff($year,$month,$day);

			$dates .= ' '.adodb_tz_offset($gmt,$isphp5);
			break;

		case 'Y': $dates .= $year; break;
		case 'y': $dates .= substr($year,strlen($year)-2,2); break;
		// MONTH
		case 'm': if ($month<10) $dates .= '0'.$month; else $dates .= $month; break;
		case 'Q': $dates .= ($month+3)>>2; break;
		case 'n': $dates .= $month; break;
		case 'M': $dates .= date('M',mktime(0,0,0,$month,2,1971)); break;
		case 'F': $dates .= date('F',mktime(0,0,0,$month,2,1971)); break;
		// DAY
		case 't': $dates .= $arr['ndays']; break;
		case 'z': $dates .= $arr['yday']; break;
		case 'w': $dates .= adodb_dow($year,$month,$day); break;
		case 'l': $dates .= gmdate('l',$_day_power*(3+adodb_dow($year,$month,$day))); break;
		case 'D': $dates .= gmdate('D',$_day_power*(3+adodb_dow($year,$month,$day))); break;
		case 'j': $dates .= $day; break;
		case 'd': if ($day<10) $dates .= '0'.$day; else $dates .= $day; break;
		case 'S':
			$d10 = $day % 10;
			if ($d10 == 1) $dates .= 'st';
			else if ($d10 == 2 && $day != 12) $dates .= 'nd';
			else if ($d10 == 3) $dates .= 'rd';
			else $dates .= 'th';
			break;

		// HOUR
		case 'Z':
			$dates .= ($is_gmt) ? 0 : -adodb_get_gmt_diff($year,$month,$day); break;
		case 'O':
			$gmt = ($is_gmt) ? 0 : adodb_get_gmt_diff($year,$month,$day);

			$dates .= adodb_tz_offset($gmt,$isphp5);
			break;

		case 'H':
			if ($hour < 10) $dates .= '0'.$hour;
			else $dates .= $hour;
			break;
		case 'h':
			if ($hour > 12) $hh = $hour - 12;
			else {
				if ($hour == 0) $hh = '12';
				else $hh = $hour;
			}

			if ($hh < 10) $dates .= '0'.$hh;
			else $dates .= $hh;
			break;

		case 'G':
			$dates .= $hour;
			break;

		case 'g':
			if ($hour > 12) $hh = $hour - 12;
			else {
				if ($hour == 0) $hh = '12';
				else $hh = $hour;
			}
			$dates .= $hh;
			break;
		// MINUTES
		case 'i': if ($min < 10) $dates .= '0'.$min; else $dates .= $min; break;
		// SECONDS
		case 'U': $dates .= $d; break;
		case 's': if ($secs < 10) $dates .= '0'.$secs; else $dates .= $secs; break;
		// AM/PM
		// Note 00:00 to 11:59 is AM, while 12:00 to 23:59 is PM
		case 'a':
			if ($hour>=12) $dates .= 'pm';
			else $dates .= 'am';
			break;
		case 'A':
			if ($hour>=12) $dates .= 'PM';
			else $dates .= 'AM';
			break;
		default:
			$dates .= $fmt[$i]; break;
		// ESCAPE
		case "\\":
			$i++;
			if ($i < $max) $dates .= $fmt[$i];
			break;
		}
	}
	return $dates;
}

/**
	Returns a timestamp given a GMT/UTC time.
	Note that $is_dst is not implemented and is ignored.
*/
function adodb_gmmktime($hr,$min,$sec,$mon=false,$day=false,$year=false,$is_dst=false)
{
	return adodb_mktime($hr,$min,$sec,$mon,$day,$year,$is_dst,true);
}

/**
	Return a timestamp given a local time. Originally by jackbbs.
	Note that $is_dst is not implemented and is ignored.

	Not a very fast algorithm - O(n) operation. Could be optimized to O(1).
*/
function adodb_mktime($hr,$min,$sec,$mon=false,$day=false,$year=false,$is_dst=false,$is_gmt=false)
{
	if (!defined('ADODB_TEST_DATES')) {

		if ($mon === false) {
			return $is_gmt? @gmmktime($hr,$min,$sec): @mktime($hr,$min,$sec);
		}

		// for windows, we don't check 1970 because with timezone differences,
		// 1 Jan 1970 could generate negative timestamp, which is illegal
		$usephpfns = (1971 < $year && $year < 2038
			|| !defined('ADODB_NO_NEGATIVE_TS') && (1901 < $year && $year < 2038)
			);


		if ($usephpfns && ($year + $mon/12+$day/365.25+$hr/(24*365.25) >= 2038)) $usephpfns = false;

		if ($usephpfns) {
				return $is_gmt ?
					@gmmktime($hr,$min,$sec,$mon,$day,$year):
					@mktime($hr,$min,$sec,$mon,$day,$year);
		}
	}

	$gmt_different = ($is_gmt) ? 0 : adodb_get_gmt_diff($year,$mon,$day);

	/*
	# disabled because some people place large values in $sec.
	# however we need it for $mon because we use an array...
	$hr = intval($hr);
	$min = intval($min);
	$sec = intval($sec);
	*/
	$mon = intval($mon);
	$day = intval($day);
	$year = intval($year);


	$year = adodb_year_digit_check($year);

	if ($mon > 12) {
		$y = floor(($mon-1)/ 12);
		$year += $y;
		$mon -= $y*12;
	} else if ($mon < 1) {
		$y = ceil((1-$mon) / 12);
		$year -= $y;
		$mon += $y*12;
	}

	$_day_power = 86400;
	$_hour_power = 3600;
	$_min_power = 60;

	$_month_table_normal = array("",31,28,31,30,31,30,31,31,30,31,30,31);
	$_month_table_leaf = array("",31,29,31,30,31,30,31,31,30,31,30,31);

	$_total_date = 0;
	if ($year >= 1970) {
		for ($a = 1970 ; $a <= $year; $a++) {
			$leaf = _adodb_is_leap_year($a);
			if ($leaf == true) {
				$loop_table = $_month_table_leaf;
				$_add_date = 366;
			} else {
				$loop_table = $_month_table_normal;
				$_add_date = 365;
			}
			if ($a < $year) {
				$_total_date += $_add_date;
			} else {
				for($b=1;$b<$mon;$b++) {
					$_total_date += $loop_table[$b];
				}
			}
		}
		$_total_date +=$day-1;
		$ret = $_total_date * $_day_power + $hr * $_hour_power + $min * $_min_power + $sec + $gmt_different;

	} else {
		for ($a = 1969 ; $a >= $year; $a--) {
			$leaf = _adodb_is_leap_year($a);
			if ($leaf == true) {
				$loop_table = $_month_table_leaf;
				$_add_date = 366;
			} else {
				$loop_table = $_month_table_normal;
				$_add_date = 365;
			}
			if ($a > $year) { $_total_date += $_add_date;
			} else {
				for($b=12;$b>$mon;$b--) {
					$_total_date += $loop_table[$b];
				}
			}
		}
		$_total_date += $loop_table[$mon] - $day;

		$_day_time = $hr * $_hour_power + $min * $_min_power + $sec;
		$_day_time = $_day_power - $_day_time;
		$ret = -( $_total_date * $_day_power + $_day_time - $gmt_different);
		if ($ret < -12220185600) $ret += 10*86400; // if earlier than 5 Oct 1582 - gregorian correction
		else if ($ret < -12219321600) $ret = -12219321600; // if in limbo, reset to 15 Oct 1582.
	}
	//print " dmy=$day/$mon/$year $hr:$min:$sec => " .$ret;
	return $ret;
}

function adodb_gmstrftime($fmt, $ts=false)
{
	return adodb_strftime($fmt,$ts,true);
}

// hack - convert to adodb_date
function adodb_strftime($fmt, $ts=false,$is_gmt=false)
{
global $ADODB_DATE_LOCALE;

	if (!defined('ADODB_TEST_DATES')) {
		if ((abs($ts) <= 0x7FFFFFFF)) { // check if number in 32-bit signed range
			if (!defined('ADODB_NO_NEGATIVE_TS') || $ts >= 0) // if windows, must be +ve integer
				return ($is_gmt)? @gmstrftime($fmt,$ts): @strftime($fmt,$ts);

		}
	}

	if (empty($ADODB_DATE_LOCALE)) {
	/*
		$tstr = strtoupper(gmstrftime('%c',31366800)); // 30 Dec 1970, 1 am
		$sep = substr($tstr,2,1);
		$hasAM = strrpos($tstr,'M') !== false;
	*/
		# see http://phplens.com/lens/lensforum/msgs.php?id=14865 for reasoning, and changelog for version 0.24
		$dstr = gmstrftime('%x',31366800); // 30 Dec 1970, 1 am
		$sep = substr($dstr,2,1);
		$tstr = strtoupper(gmstrftime('%X',31366800)); // 30 Dec 1970, 1 am
		$hasAM = strrpos($tstr,'M') !== false;

		$ADODB_DATE_LOCALE = array();
		$ADODB_DATE_LOCALE[] =  strncmp($tstr,'30',2) == 0 ? 'd'.$sep.'m'.$sep.'y' : 'm'.$sep.'d'.$sep.'y';
		$ADODB_DATE_LOCALE[]  = ($hasAM) ? 'h:i:s a' : 'H:i:s';

	}
	$inpct = false;
	$fmtdate = '';
	for ($i=0,$max = strlen($fmt); $i < $max; $i++) {
		$ch = $fmt[$i];
		if ($ch == '%') {
			if ($inpct) {
				$fmtdate .= '%';
				$inpct = false;
			} else
				$inpct = true;
		} else if ($inpct) {

			$inpct = false;
			switch($ch) {
			case '0':
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
			case 'E':
			case 'O':
				/* ignore format modifiers */
				$inpct = true;
				break;

			case 'a': $fmtdate .= 'D'; break;
			case 'A': $fmtdate .= 'l'; break;
			case 'h':
			case 'b': $fmtdate .= 'M'; break;
			case 'B': $fmtdate .= 'F'; break;
			case 'c': $fmtdate .= $ADODB_DATE_LOCALE[0].$ADODB_DATE_LOCALE[1]; break;
			case 'C': $fmtdate .= '\C?'; break; // century
			case 'd': $fmtdate .= 'd'; break;
			case 'D': $fmtdate .= 'm/d/y'; break;
			case 'e': $fmtdate .= 'j'; break;
			case 'g': $fmtdate .= '\g?'; break; //?
			case 'G': $fmtdate .= '\G?'; break; //?
			case 'H': $fmtdate .= 'H'; break;
			case 'I': $fmtdate .= 'h'; break;
			case 'j': $fmtdate .= '?z'; $parsej = true; break; // wrong as j=1-based, z=0-basd
			case 'm': $fmtdate .= 'm'; break;
			case 'M': $fmtdate .= 'i'; break;
			case 'n': $fmtdate .= "\n"; break;
			case 'p': $fmtdate .= 'a'; break;
			case 'r': $fmtdate .= 'h:i:s a'; break;
			case 'R': $fmtdate .= 'H:i:s'; break;
			case 'S': $fmtdate .= 's'; break;
			case 't': $fmtdate .= "\t"; break;
			case 'T': $fmtdate .= 'H:i:s'; break;
			case 'u': $fmtdate .= '?u'; $parseu = true; break; // wrong strftime=1-based, date=0-based
			case 'U': $fmtdate .= '?U'; $parseU = true; break;// wrong strftime=1-based, date=0-based
			case 'x': $fmtdate .= $ADODB_DATE_LOCALE[0]; break;
			case 'X': $fmtdate .= $ADODB_DATE_LOCALE[1]; break;
			case 'w': $fmtdate .= '?w'; $parseu = true; break; // wrong strftime=1-based, date=0-based
			case 'W': $fmtdate .= '?W'; $parseU = true; break;// wrong strftime=1-based, date=0-based
			case 'y': $fmtdate .= 'y'; break;
			case 'Y': $fmtdate .= 'Y'; break;
			case 'Z': $fmtdate .= 'T'; break;
			}
		} else if (('A' <= ($ch) && ($ch) <= 'Z' ) || ('a' <= ($ch) && ($ch) <= 'z' ))
			$fmtdate .= "\\".$ch;
		else
			$fmtdate .= $ch;
	}
	//echo "fmt=",$fmtdate,"<br>";
	if ($ts === false) $ts = time();
	$ret = adodb_date($fmtdate, $ts, $is_gmt);
	return $ret;
}

// Constants
if(!defined("WT_trackmailaddress")) define("WT_trackmailaddress","track@webtastic.nl");


?>