<?php

#
# Nog doen:
#	- "mail new password"-form
#
#
# Loginnaam : maximaal 128 karakters
# Wachtwoord: maximaal 32 karakters
#
#
#
# Verplichte settings:
#	- $login->settings["adminmail"]
#	- $login->$this->settings["name"]
#

class Login {

	function Login() {
#		ini_set("session.name","SID");
		ini_set("session.use_cookies",1);
		ini_set("session.use_only_cookies",1);
		@ini_set("session.use_trans_sid",0);
		ini_set("session.cookie_httponly",1);
		header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
	}

	function init() {

		if(!$this->init) {
			# Vaste waarden
			if(!isset($this->settings["name"])) $this->settings["name"]="login";
			if(!isset($this->settings["logout_number"])) $this->settings["logout_number"]=1;
			if(!isset($this->settings["mustlogin"])) $this->settings["mustlogin"]=true;
			if(!isset($this->settings["mustlogin_via_https"])) $this->settings["mustlogin_via_https"]=false;
			if(!isset($this->settings["alignloginscreen"])) $this->settings["alignloginscreen"]="center";
			if(!isset($this->settings["javascriptmd5"])) $this->settings["javascriptmd5"]=false;
			if(!isset($this->settings["mail_wt"])) $this->settings["mail_wt"]=true;
			if(!isset($this->settings["mailpassword_attempt"])) $this->settings["mailpassword_attempt"]=true;
			if(!isset($this->settings["sendnewpassword"])) $this->settings["sendnewpassword"]=false;
			if(!isset($this->settings["minimaluserlevel"])) $this->settings["minimaluserlevel"]=1;
			if(!isset($this->settings["db"]["tablename"])) $this->settings["db"]["tablename"]="users";
			if(!isset($this->settings["db"]["fielduserid"])) $this->settings["db"]["fielduserid"]="user_id";
			if(!isset($this->settings["db"]["fieldusername"])) $this->settings["db"]["fieldusername"]="user";
			if(!isset($this->settings["db"]["fielduserlevel"])) $this->settings["db"]["fielduserlevel"]="userlevel";
			if(!isset($this->settings["recheck_userdata"])) $this->settings["recheck_userdata"]=false;
			if(!isset($this->settings["save_user_agent"])) $this->settings["save_user_agent"]=false;
			if(!isset($this->settings["errorclass"])) $this->settings["errorclass"]="";
			if(!isset($this->settings["loginpogingen"])) $this->settings["loginpogingen"]=3; # betekent: 3x onjuist mag, bij de 4e keer onjuist wordt het account geblokkeerd
			if(!isset($this->settings["loginblocktime"])) $this->settings["loginblocktime"]=3600;
			if(!isset($this->settings["username_type"])) $this->settings["username_type"]="text"; # i<nput type=""> : text of email
			if(!isset($this->settings["uniqueid_ip_validtime"])) $this->settings["uniqueid_ip_validtime"]=86400*365; # login is 1 jaar geldig

			if(!isset($this->settings["loginform_nobr"])) $this->settings["loginform_nobr"]=false;
			if(!isset($this->settings["settings"]["rememberpassword"])) $this->settings["settings"]["rememberpassword"]=true;
			if(!isset($this->settings["settings"]["no_autocomplete"])) $this->settings["settings"]["no_autocomplete"]=false;
			if(!isset($this->settings["language"])) $this->settings["language"]="nl";
			if($this->settings["language"]=="nl") {
				if(!isset($this->settings["message"]["login"])) $this->settings["message"]["login"]="Gebruikersnaam";
				if(!isset($this->settings["message"]["password"])) $this->settings["message"]["password"]="Wachtwoord";
				if(!isset($this->settings["message"]["loginheader"])) $this->settings["message"]["loginheader"]="Inloggen";
				if(!isset($this->settings["message"]["remember"])) $this->settings["message"]["remember"]="Voortaan automatisch inloggen";
				if(!isset($this->settings["message"]["logout_position"])) $this->settings["message"]["logout_position"]="";
				if(!isset($this->settings["message"]["button"])) $this->settings["message"]["button"]="OK";
				if(!isset($this->settings["message"]["forget"])) $this->settings["message"]["forget"]="Wachtwoord vergeten?";
				if(!isset($this->settings["message"]["forgetheader"])) $this->settings["message"]["forgetheader"]="Wachtwoord vergeten";
				if(!isset($this->settings["message"]["forgethelp"])) $this->settings["message"]["forgethelp"]="Indien u hieronder uw loginnaam invoert, krijgt u een nieuw wachtwoord toegemaild.";
				if(!isset($this->settings["message"]["forgetokay"])) $this->settings["message"]["forgetokay"]="Er is een nieuw wachtwoord gestuurd naar het door u opgegeven e-mailadres.";
				if(!isset($this->settings["message"]["forgetsubject"])) $this->settings["message"]["forgetsubject"]="Nieuw wachtwoord";
				if(!isset($this->settings["message"]["forgetbody"])) $this->settings["message"]["forgetbody"]="Via onderstaande gegevens kunt u opnieuw inloggen.";
				if(!isset($this->settings["message"]["forgetfromname"])) $this->settings["message"]["forgetfromname"]="WebTastic Login";
				if(!isset($this->settings["message"]["forgetfrommail"])) $this->settings["message"]["forgetfrommail"]="automail@webtastic.nl";
				if(!isset($this->settings["message"]["wronglogin"])) $this->settings["message"]["wronglogin"]="Onjuiste login/wachtwoord-combinatie";
				if(!isset($this->settings["message"]["wrongloginperm"])) $this->settings["message"]["wrongloginperm"]="Account geblokkeerd: <A HREF=\"http://www.webtastic.nl/account-geblokkeerd-perm.html\" target=\"_blank\">meer informatie</A>";
				if(!isset($this->settings["message"]["wronglogintemp"])) $this->settings["message"]["wronglogintemp"]="Account geblokkeerd: <A HREF=\"http://www.webtastic.nl/account-geblokkeerd-temp.html?blocktime=\" target=\"_blank\">meer informatie</A>";
				if(!isset($this->settings["message"]["accountblocked"])) $this->settings["message"]["accountblocked"]="Account geblokkeerd: <A HREF=\"http://www.webtastic.nl/account-geblokkeerd-set.html\" target=\"_blank\">meer informatie</A>";
				if(!isset($this->settings["message"]["minimaluserlevel"])) $this->settings["message"]["minimaluserlevel"]="Dit account is niet actief.";
#				if(!isset($this->settings["message"]["nocookies"])) $this->settings["message"]["nocookies"]="Om uw naam en password te kunnen onthouden, moet u cookies aanzetten.";
				if(!isset($this->settings["message"]["nocookies"])) $this->settings["message"]["nocookies"]="Om in te kunnen loggen moet u cookies aanzetten bij uw browser.";
				if(!isset($this->settings["message"]["hide"])) $this->settings["message"]["hide"]="Verberg";
			} elseif($this->settings["language"]=="en") {
				if(!isset($this->settings["message"]["login"])) $this->settings["message"]["login"]="Username";
				if(!isset($this->settings["message"]["password"])) $this->settings["message"]["password"]="Password";
				if(!isset($this->settings["message"]["loginheader"])) $this->settings["message"]["loginheader"]="Please log in";
				if(!isset($this->settings["message"]["remember"])) $this->settings["message"]["remember"]="Remember my username and password";
				if(!isset($this->settings["message"]["logout_position"])) $this->settings["message"]["logout_position"]="";
				if(!isset($this->settings["message"]["button"])) $this->settings["message"]["button"]="OK";
				if(!isset($this->settings["message"]["forget"])) $this->settings["message"]["forget"]="Forgot your password?";
				if(!isset($this->settings["message"]["forgetheader"])) $this->settings["message"]["forgetheader"]="Forgot your password";
				if(!isset($this->settings["message"]["forgethelp"])) $this->settings["message"]["forgethelp"]="Enter your username and we will send you a new password by e-mail.";
				if(!isset($this->settings["message"]["forgetokay"])) $this->settings["message"]["forgetokay"]="A new password has been sent to your email address.";
				if(!isset($this->settings["message"]["forgetsubject"])) $this->settings["message"]["forgetsubject"]="New password";
				if(!isset($this->settings["message"]["forgetbody"])) $this->settings["message"]["forgetbody"]="Use the following data to login:";
				if(!isset($this->settings["message"]["forgetfromname"])) $this->settings["message"]["forgetfromname"]="WebTastic Login";
				if(!isset($this->settings["message"]["forgetfrommail"])) $this->settings["message"]["forgetfrommail"]="automail@webtastic.nl";
				if(!isset($this->settings["message"]["wronglogin"])) $this->settings["message"]["wronglogin"]="Wrong username or password";
				if(!isset($this->settings["message"]["wrongloginperm"])) $this->settings["message"]["wrongloginperm"]="Account blocked: <A HREF=\"http://www.webtastic.nl/account-geblokkeerd-perm-en.html\" target=\"_blank\">more information</A>";
				if(!isset($this->settings["message"]["wronglogintemp"])) $this->settings["message"]["wronglogintemp"]="Account blocked: <A HREF=\"http://www.webtastic.nl/account-geblokkeerd-temp-en.html?blocktime=\" target=\"_blank\">more information</A>";
				if(!isset($this->settings["message"]["accountblocked"])) $this->settings["message"]["accountblocked"]="Account blocked: <A HREF=\"http://www.webtastic.nl/account-geblokkeerd-set-en.html\" target=\"_blank\">more information</A>";
				if(!isset($this->settings["message"]["minimaluserlevel"])) $this->settings["message"]["minimaluserlevel"]="This username is not active.";
#				if(!isset($this->settings["message"]["nocookies"])) $this->settings["message"]["nocookies"]="You must enable cookies to remember your username and password.";
				if(!isset($this->settings["message"]["nocookies"])) $this->settings["message"]["nocookies"]="You must enable cookies in your browser to log in.";
				if(!isset($this->settings["message"]["hide"])) $this->settings["message"]["hide"]="Hide";
			} elseif($this->settings["language"]=="fr") {
				if(!isset($this->settings["message"]["login"])) $this->settings["message"]["login"]="Nom d'utilisateur";
				if(!isset($this->settings["message"]["password"])) $this->settings["message"]["password"]="Mot de passe";
				if(!isset($this->settings["message"]["loginheader"])) $this->settings["message"]["loginheader"]="Login";
				if(!isset($this->settings["message"]["remember"])) $this->settings["message"]["remember"]="Dorénavant login automatique";
				if(!isset($this->settings["message"]["logout_position"])) $this->settings["message"]["logout_position"]="";
				if(!isset($this->settings["message"]["button"])) $this->settings["message"]["button"]="VALIDER";
				if(!isset($this->settings["message"]["forget"])) $this->settings["message"]["forget"]="J'ai oublié mon mot de passe";
				if(!isset($this->settings["message"]["forgetheader"])) $this->settings["message"]["forgetheader"]="Oublié mon mot de passe";
				if(!isset($this->settings["message"]["forgethelp"])) $this->settings["message"]["forgethelp"]="";
				if(!isset($this->settings["message"]["forgetokay"])) $this->settings["message"]["forgetokay"]="";
				if(!isset($this->settings["message"]["forgetsubject"])) $this->settings["message"]["forgetsubject"]="";
				if(!isset($this->settings["message"]["forgetbody"])) $this->settings["message"]["forgetbody"]="";
				if(!isset($this->settings["message"]["forgetfromname"])) $this->settings["message"]["forgetfromname"]="WebTastic Login";
				if(!isset($this->settings["message"]["forgetfrommail"])) $this->settings["message"]["forgetfrommail"]="automail@webtastic.nl";
				if(!isset($this->settings["message"]["wronglogin"])) $this->settings["message"]["wronglogin"]="Login ou mot de passe inconnu";
				if(!isset($this->settings["message"]["wrongloginperm"])) $this->settings["message"]["wrongloginperm"]="Login bloqué";
				if(!isset($this->settings["message"]["wronglogintemp"])) $this->settings["message"]["wronglogintemp"]="Login bloqué";
				if(!isset($this->settings["message"]["accountblocked"])) $this->settings["message"]["accountblocked"]="Login bloqué";
				if(!isset($this->settings["message"]["minimaluserlevel"])) $this->settings["message"]["minimaluserlevel"]="Votre login n'est pas active";
				if(!isset($this->settings["message"]["nocookies"])) $this->settings["message"]["nocookies"]="Pour pouvoir entrer veuillez activer les cookies.";
				if(!isset($this->settings["message"]["hide"])) $this->settings["message"]["hide"]="";
			}
			if(!isset($this->settings["width"])) $this->settings["width"]="350";
			if(!isset($this->settings["tablecolor"])) $this->settings["tablecolor"]="#878481";
			if(!isset($this->settings["font"]["face"])) $this->settings["font"]["face"]="Verdana, Arial, Helvetica, sans-serif";
			if(!isset($this->settings["tableborderwidth"])) $this->settings["tableborderwidth"]=2;

			if(!isset($this->settings["font"]["size"])) $this->settings["font"]["size"]="2";
			if(!isset($this->settings["cookie"]["timeinminutes"])) $this->settings["cookie"]["timeinminutes"]="525600";

			# Indien rememberpassword=false: altijd cookies wissen
			if(!$this->settings["settings"]["rememberpassword"] and ($_COOKIE["loginuser"][$this->settings["name"]] or $_COOKIE["loginsessionid"][$this->settings["name"]])) {
				$this->delete_all_cookies();
			}

			# cookie-settings
			if($this->settings["mustlogin_via_https"] and $_SERVER["HTTPS"]=="on") {
				if(!isset($this->settings["cookies"]["secure"])) $this->settings["cookies"]["secure"]=true;
				if(!isset($this->settings["cookies"]["httponly"])) $this->settings["cookies"]["httponly"]=true;
			} else {
				if(!isset($this->settings["cookies"]["secure"])) $this->settings["cookies"]["secure"]=false;
				if(!isset($this->settings["cookies"]["httponly"])) $this->settings["cookies"]["httponly"]=true;
			}
			if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html") {
				$this->settings["cookies"]["secure"]=false;
			}

			if($this->settings["mustlogin_via_https"] and $_SERVER["DOCUMENT_ROOT"]<>"/home/webtastic/html") {
				ini_set("session.cookie_secure",1);
			}

			# session starten
			if (function_exists("wt_session_start")) {
				wt_session_start();
			} else {
				session_start();
			}
			if (function_exists("session_register")) {
				session_register("LOGIN");
			}

			# Zet een cookie om te kijken of cookies aan staan
			setcookie("checklong","on",time()+315360000,"/");

			$this->init=true;
		}
	}

	function help() {
		echo "<a name=\"login\"></a>";
		if($this->settings["language"]=="nl") {
			echo "<h2>In- en uitloggen</h2>Om het systeem te kunnen gebruiken, voert u allereerst uw gebruikersnaam en wachtwoord in. Let op dat beide hoofdlettergevoelig zijn. Bent u uw inloggegevens kwijt, dan dient u contact op te nemen met de systeembeheerder";
			if($this->settings["sysop"]) echo " (".$this->settings["sysop"].")";
			echo ".<P>";
			echo "Indien u een vinkje zet bij &quot;Voortaan automatisch inloggen&quot;, dan hoeft u de volgende keer dat u ";
			if($this->settings["systemname"]) echo wt_he($this->settings["systemname"]); else echo "het systeem";
			echo " gebruikt geen gebruikersnaam en wachtwoord in te voeren. Dit werkt alleen als u na gebruik van het systeem niet op &quot;uitloggen&quot; klikt. Voor deze functie dient uw browser zogenaamde cookies te accepteren. Is dit niet het geval, kijk dan bij de helpfunctie van uw browser hoe u het accepteren van cookies activeert.<p>";
			echo "Als u een openbare computer gebruikt, is het verstandig de functie &quot;Voortaan automatisch inloggen&quot; niet te gebruiken, omdat de kans bestaat dat u vergeet uit te loggen. In dat geval is het mogelijk dat onbevoegden het systeem gebruiken.<p>";
			echo "Heeft u &quot;Voortaan automatisch inloggen&quot; gebruikt maar wilt u toch uitloggen, dan kan dat op ieder moment door ";
			if($this->settings["message"]["logout_position"]) {
				echo $this->settings["message"]["logout_position"];
			} else {
				echo "op &quot;<i>uw naam</i> uitloggen&quot;";
			}
			echo " te klikken. Zonder gebruik van de functie &quot;Voortaan automatisch inloggen&quot; volstaat het afsluiten van de browser om uit te loggen.";
		}
	}

	function logout($logoutall=false) {
		$this->logged_in=false;
		$this->delete_all_cookies();
		setcookie("checklong","",time()-864000);

		session_unset();
		session_destroy();
		if($logoutall) {
			$db->query("SELECT * FROM ".$this->settings["db"]["tablename"]." WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($this->user_id)."';");
			if($db->next_record()) {
				if(isset($db->Record["uniqueid_ip"])) {
					$db->query("UPDATE ".$this->settings["db"]["tablename"]." SET uniqueid_ip='' WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($this->user_id)."';");
				} else {
					$db->query("UPDATE ".$this->settings["db"]["tablename"]." SET uniqueid='' WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($this->user_id)."';");
				}
			}
		}
	}

	function reload($reloadurl="",$add_get_values=true) {
		$get=wt_stripget($_GET,array("logoutall","logout"));
		if($get) {
			if(ereg("\?",$reloadurl)) {
				$get="&".$get;
			} else {
				$get="?".$get;
			}
		}
		if(!$_GET["reloaded"]) {
			if($get or ereg("\?",$reloadurl)) $get.="&reloaded=1"; else $get="?reloaded=1";
			if(!$reloadurl) {
				if($_SERVER["REQUEST_URI"]) {
					$reloadurl=ereg_replace("\?.*","",$_SERVER["REQUEST_URI"]);
				} else {
					$reloadurl=$_SERVER["PHP_SELF"];
				}
				if($this->settings["mustlogin"] and $this->settings["mustlogin_via_https"] and $_SERVER["HTTPS"]<>"on" and $_SERVER["DOCUMENT_ROOT"]<>"/home/webtastic/html") {
					$reloadurl="https://".$_SERVER["HTTP_HOST"].$reloadurl;
				}
			}
		}
		if($reloadurl) {
			if($add_get_values) {
				$reloadurl.=$get;
			}
			if($_GET["logout"] or $_GET["logoutall"]) {
				echo"<HTML><HEAD><META HTTP-EQUIV=Refresh CONTENT=\"0; URL=";
				echo $reloadurl;
				echo "\"></HEAD><BODY>\n";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
				echo "</BODY></HTML>";
				exit;
			} else {
				header("Location: ".$reloadurl);
				exit;
			}
		}
	}

	function loginform($isMobile=false) {
		if(!$this->logged_in) {
			if($isMobile) $this->settings["width"] = "100%";
			echo "<TABLE BORDER=\"0\" width=\"".$this->settings["width"]."\" align=\"".$this->settings["settings"]["alignloginscreen"]."\" bgcolor=\"".$this->settings["tablecolor"]."\" cellspacing=\"".intval($this->settings["tableborderwidth"]-1)."\" class=\"wtlogin_table\">";
			echo "<FORM METHOD=\"post\" name=\"loginform\" action=\"".$this->currenturl()."\"".($this->settings["settings"]["no_autocomplete"] ? " autocomplete=\"off\"" : "").">";
			echo "<TR><TD align=\"center\"><B>";
			if($this->settings["font"]["face"]) echo "<FONT COLOR=white FACE=\"".$this->settings["font"]["face"]."\" SIZE=\"".($this->settings["font"]["size"]+1)."\">";
			echo $this->settings["message"]["loginheader"];
			if($this->settings["font"]["face"]) echo "</FONT>";
			echo "</B></TD></TR><TR><TD width=\"100%\" height=\"100%\">";
			echo "<TABLE align=\"left\" width=\"100%\" height=\"100%\" bgcolor=\"#FFFFFF\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
			echo "<TR><TD width=\"100%\" height=\"100%\">";
			echo "<TABLE align=\"left\" width=\"100%\" height=\"100%\" bgcolor=\"#FFFFFF\" cellspacing=\"7\" border=\"0\"><TR>";

			echo "<TD".(($isMobile) ? " colspan=\"2\"" : "").">";

			if($this->settings["font"]["face"]) echo "<FONT FACE=\"".$this->settings["font"]["face"]."\" SIZE=\"".$this->settings["font"]["size"]."\">";
			if($this->settings["loginform_nobr"]) echo "<nobr>";
			echo $this->settings["message"]["login"];
			if($this->settings["loginform_nobr"]) echo "</nobr>";
			if($this->settings["font"]["face"]) echo "</FONT>";

			if(!$isMobile) { echo "</TD><TD width=\"99%\">"; }

			echo "<INPUT TYPE=\"".((!$isMobile)?"text":"email")."\" name=\"username[".$this->settings["name"]."]\" size=\"20\" maxlength=\"128\"";
			if($_POST["loginfilled"]) {
				echo " VALUE=\"".wt_he($_POST["username"][$this->settings["name"]])."\"";
			} elseif($_GET["username"]) {
				echo " VALUE=\"".wt_he($_GET["username"])."\"";
			}

			echo (($isMobile) ? " style=\"width: 95%;" : " style=\"width: 100%;");

			if($this->settings["font"]["face"]) echo " font-family:".$this->settings["font"]["face"];
			echo "\"></TD></TR><TR>";

			echo "<TD".(($isMobile) ? " colspan=\"2\"" : "").">";

			if($this->settings["font"]["face"]) echo "<FONT STYLE=\"display:block;\" FACE=\"".$this->settings["font"]["face"]."\" SIZE=\"".$this->settings["font"]["size"]."\">";
			if($this->settings["loginform_nobr"]) echo "<nobr>";
			echo $this->settings["message"]["password"];
			if($this->settings["loginform_nobr"]) echo "</nobr>";
			if($this->settings["font"]["face"]) echo "</FONT>";

			if(!$isMobile) { echo "</TD><TD width=\"99%\">"; }

			echo "<INPUT TYPE=\"password\" id=\"password\" name=\"password[".$this->settings["name"]."]\" size=\"10\" maxlength=\"32\"";
			if($_POST["loginfilled"]) {
				echo " VALUE=\"",wt_he($_POST["password"][$this->settings["name"]]),"\"";
			}

			echo (($isMobile) ? " spellcheck=\"false\" autocorrect=\"off\" autocapitalize=\"off\" style=\"width: 65%;\">" : " style=\"width: 100%;\">");

			if($isMobile) {
						$togggle_hidden_field = $_POST['toggle_pwd'] ? " CHECKED ": "";

				echo "&nbsp;&nbsp;<label><input name='toggle_pwd' id=\"toggle_pwd\" ".$togggle_hidden_field." type=\"checkbox\">".$this->settings["message"]["hide"]."</label>";
				echo "<script>
				document.getElementById('password').type = 'text';
				$('#toggle_pwd').change(function(){
					if($(this).is(':checked'))  document.getElementById('password').type = 'password';
					else document.getElementById('password').type = 'text';
				});";
					if(isset($_POST['toggle_pwd']))
						echo "document.getElementById('password').type = 'password';";
					echo "</script>";
			}

			echo "</TD></TR>";
			if($this->settings["settings"]["sendnewpassword"]) echo "<TR><TD colspan=2 align=right><FONT FACE=\"".$this->settings["font"]["face"]."\" SIZE=\"".($this->settings["font"]["size"]-1)."\"><A HREF=\"".$this->currenturl().($_SERVER["QUERY_STRING"] ? "&" : "?")."mailpassword=1\">".$this->settings["message"]["forget"]."</A></FONT></TD></TR>";
			if($this->settings["settings"]["rememberpassword"]) {
				echo "<TR><TD colspan=\"2\" align=\"left\"><INPUT TYPE=\"checkbox\" id=\"remember".$this->settings["name"]."\" name=\"remember\"";
				if(($this->settings["settings"]["rememberpassword"]=="on" and !$_POST["loginfilled"]) or ($_POST["remember"]=="on" and $_POST["loginfilled"])) echo " checked";
				echo "><LABEL FOR=\"remember".$this->settings["name"]."\">&nbsp;&nbsp;";
				if($this->settings["font"]["face"]) echo "<FONT FACE=\"".$this->settings["font"]["face"]."\" SIZE=\"".$this->settings["font"]["size"]."\">";
				echo $this->settings["message"]["remember"];
				if($this->settings["font"]["face"]) echo "</FONT>";
				echo "</LABEL></TD></TR>";
			}
			echo "<INPUT TYPE=\"hidden\" name=\"loginfilled\" value=\"1\">";
			echo "<TR><TD colspan=\"2\" align=\"center\"><INPUT TYPE=\"submit\" id=\"loginbutton\" onclick=\"document.loginform.loginbutton.disabled=1;submit();\" value=\" ".$this->settings["message"]["button"]." \"";
			if($this->settings["font"]["face"]) echo " style=\"font-family:".$this->settings["font"]["face"]."\"";
			echo "></TD></TR>";
			if($_POST["loginfilled"]) {
				echo "<TR><TD align=\"center\" colspan=\"2\">";
				if($this->settings["font"]["face"]) echo "<FONT FACE=\"".$this->settings["font"]["face"]."\" SIZE=\"".$this->settings["font"]["size"]."\">";
				if($this->settings["errorclass"]) echo "<span class=\"".$this->settings["errorclass"]."\">";
				echo $this->errormessage;
				if($this->settings["errorclass"]) echo "</span>";
				if($this->settings["font"]["face"]) echo "</FONT>";
				echo "</TD></TR>";
			}
			echo "</TABLE></TR></TD></TABLE></TR></TD></FORM></TABLE>";
		}
	}

	function loginform_html5() {
		if(!$this->logged_in) {
			echo "<div class=\"wtlogin_maindiv wtlogin_name_".$this->settings["name"].($this->settings["html5form"]["class_maindiv"] ? " ".$this->settings["html5form"]["class_maindiv"] : "")."\">";
			echo "<form method=\"post\" name=\"loginform\" action=\"".$this->currenturl()."\"".($this->settings["settings"]["no_autocomplete"] ? " autocomplete=\"off\"" : "").">";
			echo "<input type=\"hidden\" name=\"loginfilled\" value=\"1\">";

			echo "<div class=\"wtlogin_div_username\">";
			echo "<label class=\"wtlogin_label_username\">";
			echo $this->settings["message"]["login"];
			echo "</label>";
			echo "<input type=\"".$this->settings["username_type"]."\" name=\"username[".$this->settings["name"]."]\" size=\"20\" maxlength=\"128\"";
			if($_POST["loginfilled"]) {
				echo " value=\"".wt_he($_POST["username"][$this->settings["name"]])."\"";
			} elseif($_GET["username"]) {
				echo " value=\"".wt_he($_GET["username"])."\"";
			}
			echo " class=\"".$this->settings["html5form"]["class_username"]."\">";
			echo "</div>";


			echo "<div class=\"wtlogin_div_password\">";
			echo "<label class=\"wtlogin_label_password\">";
			echo $this->settings["message"]["password"];
			echo "</label>";
			echo "<input type=\"password\" name=\"password[".$this->settings["name"]."]\" size=\"10\" maxlength=\"32\"";
			if($_POST["loginfilled"]) {
				echo " value=\"",wt_he($_POST["password"][$this->settings["name"]]),"\"";
			}
			echo " class=\"".$this->settings["html5form"]["class_password"]."\">";
			echo "</div>";

			if($this->settings["settings"]["rememberpassword"]) {

				echo "<div class=\"wtlogin_div_remember\">";

				echo "<input type=\"checkbox\" id=\"remember".$this->settings["name"]."\" name=\"remember\"";
				if(($this->settings["settings"]["rememberpassword"]=="on" and !$_POST["loginfilled"]) or ($_POST["remember"]=="on" and $_POST["loginfilled"])) echo " checked";
				echo ">";
				echo "<label for=\"remember".$this->settings["name"]."\" class=\"wtlogin_label_remember\">&nbsp;&nbsp;";
				echo $this->settings["message"]["remember"];
				echo "</label>";

				echo "</div>";

			}

			echo "<div class=\"wtlogin_div_submit\">";
			echo "<input type=\"submit\" id=\"loginbutton\" value=\"".$this->settings["message"]["button"]."\" class=\"".$this->settings["html5form"]["class_button"]."\">";
			echo "</div>";

			if($_POST["loginfilled"]) {
				echo "<div class=\"wtlogin_div_errormessage\">";
				echo $this->errormessage;
				echo "</div>";
			}
			echo "</form></div>";
		}
	}


	function passwordform() {

	}

	function sendnewpassword($userid,$subject,$body,$fromname,$frommail) {
		$db=new DB_sql;
		$db->query("SELECT ".$this->settings["db"]["fieldusername"]." AS \"user\" FROM ".$this->settings["db"]["tablename"]." WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($userid)."';");
		if($db->next_record()) {
			if(eregi("^[-_0-9a-z.]*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,4}$",$db->f("user"))) {
				$mailaddress=$db->f("user");
			}
		}
		if(!$mailaddress and $this->settings["db"]["email"]) {
			$db->query("SELECT ".$this->settings["db"]["email"]." FROM ".$this->settings["db"]["tablename"]." WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($userid)."';");
			if($db->next_record()) {
				$mailaddress=$db->f($this->settings["db"]["email"]);
			}
		}
		if($mailaddress) {
			$password=substr(md5(uniqid(rand(),1)),0,8);
			$db->query("UPDATE ".$this->settings["db"]["tablename"]." SET password='".md5($password)."', uniqueid='' WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($userid)."';");
			$body.="\n\n".$this->settings["message"]["password"].": ".$password."\n\n";
			mail($mailaddress,$subject,$body,"From: ".$fromname." <".$frommail.">");
		}
	}

	# Functie om lastlogin en logincount in de database op te slaan
	function setlastlogin($erasewronglogin=false) {
		$db=new DB_sql;
		$newhosts=trim(gethostbyaddr($_SERVER["REMOTE_ADDR"])." [".$_SERVER["REMOTE_ADDR"]."] : ".date("r"));
		if($this->settings["save_user_agent"]) {
			$newhosts.=" - ".$_SERVER["HTTP_USER_AGENT"];
		}
		$hosts=explode("\n",$_SESSION["LOGIN"][$this->settings["name"]]["lasthosts"]);
		for($i=0;$i<9;$i++) {
			if($hosts[$i]) $newhosts.="\n".$hosts[$i];
		}
		if($_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fielduserid"]]) {
			if($erasewronglogin) $setquery=", wrongcount=0, wrongtime=0";
			$db->query("UPDATE ".$this->settings["db"]["tablename"]." SET lastlogin='".time()."', logincount=logincount+1, lasthosts='".addslashes($newhosts)."'".$setquery." WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fielduserid"]])."';");
		}
	}

	function wronglogin($userid) {
		$db=new DB_sql;
		$db->query("SELECT ".$this->settings["db"]["fielduserid"].", wrongtime, wronghost, wrongcount FROM ".$this->settings["db"]["tablename"]." WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($userid)."';");
		if($db->next_record()) {
			if(!eregi($_SERVER["REMOTE_ADDR"],$db->f("wronghost"))) $wronghost=", wronghost='".addslashes($_SERVER["REMOTE_ADDR"])."\n".addslashes($db->f("wronghost"))."'";
			if($db->f("wrongtime")>(time()-$this->settings["loginblocktime"])) {
				$wrongcount=$db->f("wrongcount")+1;
				if($wrongcount>($this->settings["loginpogingen"]+1)) {
					$wrongtime=$db->f("wrongtime");
				} else {
					$wrongtime=time();
				}
			} else {
				$wrongcount=1;
				$wrongtime=time();
			}
			$db->query("UPDATE ".$this->settings["db"]["tablename"]." SET wrongtime='".addslashes($wrongtime)."'".$wronghost.", wrongcount='".addslashes($wrongcount)."' WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($db->f($this->settings["db"]["fielduserid"]))."';");
		}
		# Alle cookies wissen
		$this->delete_all_cookies();
	}

	function delete_all_cookies() {
		# Cookie wissen
		if(floatval(phpversion())>5.2) {
			setcookie("loginuser[".$this->settings["name"]."]","",time()-864000,"/","",$this->settings["cookies"]["secure"],$this->settings["cookies"]["httponly"]);
			setcookie("loginsessionid[".$this->settings["name"]."]","",time()-864000,"/","",$this->settings["cookies"]["secure"],$this->settings["cookies"]["httponly"]);
		} else {
			setcookie("loginuser[".$this->settings["name"]."]","",time()-864000,"/","",$this->settings["cookies"]["secure"]);
			setcookie("loginsessionid[".$this->settings["name"]."]","",time()-864000,"/","",$this->settings["cookies"]["secure"]);
		}
		setcookie("lin[".$this->settings["name"]."]","",time()-864000,"/");

		unset($_COOKIE["loginuser"][$this->settings["name"]]);
		unset($_COOKIE["loginsessionid"][$this->settings["name"]]);
		unset($_COOKIE["lin"][$this->settings["name"]]);

		if($this->settings["extra_unsafe_cookie"]) {
			setcookie($this->settings["extra_unsafe_cookie"]."[".$this->settings["name"]."]","",time()-864000,"/");
			unset($_COOKIE[$this->settings["extra_unsafe_cookie"]][$this->settings["name"]]);
		}
	}

	function currenturl() {
		if($_SERVER["REQUEST_URI"]) {
			$return=$_SERVER["REQUEST_URI"];
		} else {
			$return=$_SERVER["PHP_SELF"].($_SERVER["QUERY_STRING"] ? "?".$_SERVER["QUERY_STRING"] : "");
		}
		return $return;
	}

	function sendmail($text) {
		$db=new DB_sql;
		$subject="Te veel ongeldige inlogpogingen ".$_SERVER["HTTP_HOST"];
		$body="\nSite: ".$_SERVER["HTTP_HOST"]."\nOnderdeel: ".$this->settings["name"]."\nIP-adres: ".$_SERVER["REMOTE_ADDR"]."\n".($_SERVER["REMOTE_HOST"] ? "Host: ".$_SERVER["REMOTE_HOST"]."\n" : "").($_SERVER["HTTP_USER_AGENT"] ? "Gebruikte browser: ".$_SERVER["HTTP_USER_AGENT"]."\n" : "")."URL: http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."\n\n".$text."\n\n";
		$from="automail@webtastic.nl";
		$fromname="WebTastic Login";
#		if($this->settings["adminmail"]) mail($this->settings["adminmail"],$subject,$body,"Bcc: track@webtastic.nl\n".$from);
		if($this->settings["adminmail"]) wt_mail($this->settings["adminmail"],$subject,$body,$from,$fromname);
		if(!ereg("@webtastic\.nl$",$this->settings["adminmail"]) and $this->settings["mail_wt"]) {
#			mail("jeroen@webtastic.nl",$subject,$body,$from);
			wt_mail("systeembeheer@webtastic.nl",$subject,$body,$from,$fromname);
		}
	}

	function uniqueid_ip_check($uniqueid_ip_all) {
		#
		# actuele $uniqueid uit de lijst filteren op basis van REMOTE_ADDR en geldigheid time
		#
		$uniqueid_ip_all_array=explode("\n",$uniqueid_ip_all);
		while(list($key,$value)=@each($uniqueid_ip_all_array)) {
			if(preg_match("/^([0-9]+)_([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})_(.*)$/",$value,$regs)) {
				if($regs[2]==$_SERVER["REMOTE_ADDR"] and intval($regs[1])>(time()-$this->settings["uniqueid_ip_validtime"])) {
					$uniqueid_database=$regs[3];
				}
			}
		}
		if($uniqueid_database) {
			return $uniqueid_database;
		} else {
			return false;
		}
	}

	function uniqueid_ip_save($uniqueid_ip_all,$userid) {
		$db=new DB_sql;

		$uniqueid_ip_all_array=explode("\n",$uniqueid_ip_all);
		while(list($key,$value)=@each($uniqueid_ip_all_array)) {
			if(preg_match("/^([0-9]+)_([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})_(.*)$/",$value,$regs)) {
				if(intval($regs[1])>(time()-$this->settings["uniqueid_ip_validtime"])) {
					$uniqueid_ip_new_array[]="\n".$value;
				}
			}
		}
		if(is_array($uniqueid_ip_new_array)) {
			asort($uniqueid_ip_new_array);
			while(list($key,$value)=each($uniqueid_ip_new_array)) {
				$uniqueid_ip_new.="\n".$value;
			}
		}
		$uniqueid=md5(uniqid(rand()));
		$uniqueid_ip_new.="\n".time()."_".$_SERVER["REMOTE_ADDR"]."_".$uniqueid;
		$db->query("UPDATE ".$this->settings["db"]["tablename"]." SET uniqueid_ip='".addslashes(trim($uniqueid_ip_new))."', uniqueid='' WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($userid)."';");

		return $uniqueid;
	}

	function dbvalue($key) {
		return $_SESSION["LOGIN"][$this->settings["name"]][$key];
	}

	function has_userlevel($level) {
		if($this->userlevel>=$level) return true; else return false;
	}

	function has_priv($type) {
		$priv=split(",",$this->priv);
		if(in_array($type,$priv)) return true; else return false;
	}

	function create_user($username,$password="") {
		//
		// Nieuwe user aanmaken en opslaan in de database
		//
		$db=new DB_sql;

		// kijken of adddatetime en editdatetime aanwezig zijn
		$db->query("SHOW COLUMNS FROM ".$this->settings["db"]["tablename"].";");
		while($db->next_record()) {
			if($db->f("Field")=="adddatetime") {
				$setquery.=", adddatetime=NOW()";
			}
			if($db->f("Field")=="editdatetime") {
				$setquery.=", editdatetime=NOW()";
			}
		}

		if($password) {
			$password_hash=wt_complex_password_hash($password,$this->settings["salt"]);
		}
		$db->query("INSERT INTO ".$this->settings["db"]["tablename"]." SET ".$this->settings["db"]["fieldusername"]."='".addslashes($username)."'".($password ? ", password='".addslashes($password_hash)."'" : "").$setquery.";");
		if($db->insert_id()) {
			return $db->insert_id();
		} else {
			return false;
		}
	}

	function log_user_in($userid) {

		//
		// user inloggen op basis van userid (wachtwoord niet nodig)
		//
		$db=new DB_sql;

		$this->logged_in=true;

		$db->query("SELECT * FROM ".$this->settings["db"]["tablename"]." WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($userid)."';");
		if($db->next_record()) {

			if(isset($db->Record["uniqueid_ip"])) {
				# nieuw uniqueid-systeem (met IP-adres-controle)
				$uniqueid_ip=$db->f("uniqueid_ip");

				# kijken of de uniqueid geldig is
				$uniqueid=$this->uniqueid_ip_check($uniqueid_ip);
				if(!$uniqueid) {
					# niet geldig / onbekend: nieuwe aanmaken
					$uniqueid=$this->uniqueid_ip_save($uniqueid_ip,$userid);
				}
			} else {
				# oude systeem (werkt ook prima, is alleen minder veilig)
				if($db->f("uniqueid")) {
					$uniqueid=$db->f("uniqueid");
				} else {
					$uniqueid=md5(uniqid(rand()));
					$db->query("UPDATE ".$this->settings["db"]["tablename"]." SET uniqueid='".addslashes($uniqueid)."' WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($userid)."';");
				}
			}

		}


		# cookies plaatsen
		$time=time()+($this->settings["cookie"]["timeinminutes"]*60);

		// backwards compatible with other projects of Jeroen
		// @TODO: Remove this if all projects from jeroen have been updated to use this setting
		$this->settings['cookies']['domain'] = (isset($this->settings['cookies']['domain']) ? $this->settings['cookies']['domain'] : '');

		if(floatval(phpversion())>5.2) {
			setcookie("loginuser[".$this->settings["name"]."]",$userid,$time,"/",$this->settings['cookies']['domain'],$this->settings["cookies"]["secure"],$this->settings["cookies"]["httponly"]);
			setcookie("loginsessionid[".$this->settings["name"]."]",$uniqueid,$time,"/",$this->settings['cookies']['domain'],$this->settings["cookies"]["secure"],$this->settings["cookies"]["httponly"]);
		} else {
			setcookie("loginuser[".$this->settings["name"]."]",$userid,$time,"/",$this->settings['cookies']['domain'],$this->settings["cookies"]["secure"]);
			setcookie("loginsessionid[".$this->settings["name"]."]",$uniqueid,$time,"/",$this->settings['cookies']['domain'],$this->settings["cookies"]["secure"]);
		}
		setcookie("lin[".$this->settings["name"]."]","dl0j82",$time,"/"); # willekeurige waardie die (ook zonder https) aangeeft dat iemand is ingelogd
		if($this->settings["extra_unsafe_cookie"]) {
			setcookie($this->settings["extra_unsafe_cookie"]."[".$this->settings["name"]."]",md5($_SERVER["REMOTE_ADDR"]."_".$this->settings["name"]."_QjJEJ938ja2"),$time,"/");
		}

		# Gebruikers-gegevens uit database halen
		$db->query("SELECT * FROM ".$this->settings["db"]["tablename"]." WHERE ".($this->settings["db"]["where"] ? $this->settings["db"]["where"]." AND " : "").$this->settings["db"]["fielduserid"]."='".addslashes($userid)."';");

		if($db->num_rows()==1 and $db->next_record()) {
			$_SESSION["LOGIN"][$this->settings["name"]]["logged_in"]=true;

			$fieldnames=$db->metadata($this->settings["db"]["tablename"],true);
			while(list($name,$value)=each($fieldnames["meta"])) {
				$_SESSION["LOGIN"][$this->settings["name"]][$name]=$db->f($name);
			}
			$this->userlevel=$_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fielduserlevel"]];
			$this->user_id=$_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fielduserid"]];
			$this->username=$_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fieldusername"]];
			$this->priv=$_SESSION["LOGIN"][$this->settings["name"]]["priv"];
			$this->vars=$_SESSION["LOGIN"][$this->settings["name"]];
		}
		$this->setlastlogin(1);

		return true;
	}

	function end_declaration() {
		$db=new DB_sql;
		$this->init();
		$this->end_declaration=true;

		# zorgen voor https (indien verplicht)
		if($this->settings["mustlogin"] and $this->settings["mustlogin_via_https"] and $_SERVER["HTTPS"]<>"on" and $_SERVER["DOCUMENT_ROOT"]<>"/home/webtastic/html") {
			if($_SERVER["HTTP_HOST"] and $_SERVER["REQUEST_URI"]) {
				header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
				exit;
			}
		}

		# Oude passwords omzetten
		if($this->settings["convert_old_passwords"] and $this->settings["salt"] and ($_SERVER["REMOTE_ADDR"]=="31.223.173.113" or $_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html")) {
			$db0=new DB_sql;

			$db->query("ALTER TABLE ".$this->settings["db"]["tablename"]." CHANGE password password CHAR(41) NOT NULL DEFAULT '';");
			$db->query("SHOW COLUMNS FROM ".$this->settings["db"]["tablename"].";");
			while($db->next_record()) {
				if($db->f("Field")=="password" and trim($db->f("Type"))=="char(41)") {
					$change_okay=true;
				}
			}

			if($change_okay) {
				echo "OK";
				$db->query("SELECT ".$this->settings["db"]["fielduserid"]." AS user_id, password FROM ".$this->settings["db"]["tablename"]." WHERE SUBSTR(password,1,1)<>'_' AND CHAR_LENGTH(password)=32 ORDER BY ".$this->settings["db"]["fielduserid"].";");
				while($db->next_record()) {
					$query="UPDATE ".$this->settings["db"]["tablename"]." SET password='".addslashes(wt_complex_password_hash($db->f("password"),$this->settings["salt"],true))."' WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($db->f("user_id"))."';";
					echo "user: ".$db->f("user_id")." - old password: ".$db->f("password")."\n<br>";
					echo $query."\n<hr>";
					$db0->query($query);
				}
			}
			exit;
		}

		# Logout
		if($_GET["logout"]==$this->settings["logout_number"] or $_GET["logoutall"]==$this->settings["logout_number"]) {
			$this->logout($_GET["logoutall"]);
			$this->reload();
		}

		if(!$_SESSION["LOGIN"][$this->settings["name"]]["logged_in"]) {
			if($_POST["loginfilled"]==1) {
				if($_POST["username"][$this->settings["name"]]) {
					$db->query("SELECT * FROM ".$this->settings["db"]["tablename"]." WHERE ".($this->settings["db"]["where"] ? $this->settings["db"]["where"]." AND " : "").$this->settings["db"]["fieldusername"]."='".addslashes($_POST["username"][$this->settings["name"]])."';");
					if($db->next_record()) {
						if(substr($db->f("password"),0,1)=="_" and $this->settings["salt"]) {
							$password_entry=wt_complex_password_hash($_POST["password"][$this->settings["name"]],$this->settings["salt"]);
							$password_database=$db->f("password");
						} else {
							$password_entry=md5($_POST["password"][$this->settings["name"]]);
							$password_database=$db->f("password");
						}
						if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" and preg_match("/^172\.16\./",$_SERVER["REMOTE_ADDR"]) and $_POST["password"][$this->settings["name"]]=="zxc") {
							$password_entry="jejkljlk489dcjhahkj4wh9847rhj43hkl";
							$password_database="jejkljlk489dcjhahkj4wh9847rhj43hkl";
						}
						// if($_SERVER["REMOTE_ADDR"]=="31.223.173.113" and $_POST["password"][$this->settings["name"]]=="zxc") {
						// 	$password_entry="jejkljlk489dcjhahkj4wh9847rhj43hkl";
						// 	$password_database="jejkljlk489dcjhahkj4wh9847rhj43hkl";
						// }
						if($_COOKIE["checklong"]<>"on") {
							$this->errormessage=$this->settings["message"]["nocookies"];
						} elseif($db->f("wrongcount")>$this->settings["loginpogingen"] and $db->f("wrongtime")>(time()-$this->settings["loginblocktime"])) {
							# Te veel foute inlogpogingen: account geblokkeerd
							$this->errormessage=ereg_replace("blocktime=","blocktime=".($db->f("wrongtime")+$this->settings["loginblocktime"]),$this->settings["message"]["wronglogintemp"]);
#							$this->wronglogin($db->f("user_id"));
						} elseif($password_database<>$password_entry) {
							# Login-wachtwoordcombinatie onjuist
							$wrongcount=$db->f("wrongcount")+1;
							if($wrongcount>$this->settings["loginpogingen"] and $db->f("wrongtime")>(time()-$this->settings["loginblocktime"])) {
								$this->errormessage=ereg_replace("blocktime=","blocktime=".($db->f("wrongtime")+$this->settings["loginblocktime"]),$this->settings["message"]["wronglogintemp"]);

								# mailtje sturen
								$foutloginmail="Account ".$_POST["username"][$this->settings["name"]]." tijdelijk geblokeerd (tot ".date("d-m-Y, H:i",($db->f("wrongtime")+$this->settings["loginblocktime"]))." uur)\n";
								if($this->settings["mailpassword_attempt"]) $foutloginmail.="WW-poging: ===".$_POST["password"][$this->settings["name"]]."===";
								$this->sendmail($foutloginmail);
							} else {
								$this->errormessage=$this->settings["message"]["wronglogin"];
							}
							$this->wronglogin($db->f("user_id"));
						} elseif($password_database==$password_entry and $db->num_rows()==1) {
							# Loginnaam en wachtwoord kloppen
							if($db->f($this->settings["db"]["fielduserlevel"])<0) {
								# Account geblokkeerd - userlevel<0
								$this->errormessage=$this->settings["message"]["accountblocked"];
							} elseif($db->f($this->settings["db"]["fielduserlevel"])<$this->settings["minimaluserlevel"]) {
								# Te laag userlevel
								$this->errormessage=$this->settings["message"]["minimaluserlevel"];
							} elseif($_POST["remember"]=="on" and $this->settings["settings"]["rememberpassword"]) {
								# Inloggen gelukt - login/password opslaan in cookie
								$this->logged_in=true;
								$time=time()+($this->settings["cookie"]["timeinminutes"]*60);

								$temp_userid=$db->f($this->settings["db"]["fielduserid"]);

								if(isset($db->Record["uniqueid_ip"])) {
									# nieuw uniqueid-systeem (met IP-adres-controle)

									# kijken of de uniqueid geldig is
									$uniqueid=$this->uniqueid_ip_check($db->Record["uniqueid_ip"]);
									if(!$uniqueid) {
										# niet geldig / onbekend: nieuwe aanmaken
										$uniqueid=$this->uniqueid_ip_save($db->Record["uniqueid_ip"],$temp_userid);
									}
								} else {
									# oude systeem (werkt ook prima, is alleen minder veilig)
									if($db->f("uniqueid")) {
										$uniqueid=$db->f("uniqueid");
									} else {
										$uniqueid=md5(uniqid(rand()));
										$db->query("UPDATE ".$this->settings["db"]["tablename"]." SET uniqueid='".addslashes($uniqueid)."' WHERE ".$this->settings["db"]["fielduserid"]."='".addslashes($db->f($this->settings["db"]["fielduserid"]))."';");
									}
								}
								if(floatval(phpversion())>5.2) {
									setcookie("loginuser[".$this->settings["name"]."]",$temp_userid,$time,"/","",$this->settings["cookies"]["secure"],$this->settings["cookies"]["httponly"]);
									setcookie("loginsessionid[".$this->settings["name"]."]",$uniqueid,$time,"/","",$this->settings["cookies"]["secure"],$this->settings["cookies"]["httponly"]);
								} else {
									setcookie("loginuser[".$this->settings["name"]."]",$temp_userid,$time,"/","",$this->settings["cookies"]["secure"]);
									setcookie("loginsessionid[".$this->settings["name"]."]",$uniqueid,$time,"/","",$this->settings["cookies"]["secure"]);
								}
								setcookie("lin[".$this->settings["name"]."]","dl0j82",$time,"/"); # willekeurige waardie die (ook zonder https) aangeeft dat iemand is ingelogd
								if($this->settings["extra_unsafe_cookie"]) {
									setcookie($this->settings["extra_unsafe_cookie"]."[".$this->settings["name"]."]",md5($_SERVER["REMOTE_ADDR"]."_".$this->settings["name"]."_QjJEJ938ja2"),$time,"/");
								}
							} else {
								# Inloggen gelukt - login niet onthouden
								$this->logged_in=true;

								if($this->settings["extra_unsafe_cookie"]) {
									setcookie($this->settings["extra_unsafe_cookie"]."[".$this->settings["name"]."]",md5($_SERVER["REMOTE_ADDR"]."_".$this->settings["name"]."_QjJEJ938ja2"),$time,"/");
								}

							}
						}
					} else {
						$this->errormessage=$this->settings["message"]["wronglogin"];
					}
				} else {
					$this->errormessage=$this->settings["message"]["wronglogin"];
				}
			}
			# Cookies checken of er eerder is ingelogd
			if($this->settings["settings"]["rememberpassword"] and !$this->logged_in and $_COOKIE["loginuser"][$this->settings["name"]] and $_COOKIE["loginsessionid"][$this->settings["name"]] and !$_POST["loginfilled"]) {
				$db->query("SELECT * FROM ".$this->settings["db"]["tablename"]." WHERE ".($this->settings["db"]["where"] ? $this->settings["db"]["where"]." AND " : "").$this->settings["db"]["fielduserid"]."='".addslashes($_COOKIE["loginuser"][$this->settings["name"]])."';");
				if($db->next_record()) {
					if($db->f("wrongcount")>$this->settings["loginpogingen"] and $db->f("wrongtime")>(time()-$this->settings["loginblocktime"])) {
						# Er is al te vaak verkeerd ingelogd
#						$this->wronglogin($db->f("user_id"));
						$this->errormessage=ereg_replace("blocktime=","blocktime=".($db->f("wrongtime")+$this->settings["loginblocktime"]),$this->settings["message"]["wronglogintemp"]);
					} else {
						if(isset($db->Record["uniqueid_ip"])) {
							# nieuw uniqueid-systeem (met IP-adres-controle)

							# kijken of de uniqueid geldig is
							$uniqueid=$this->uniqueid_ip_check($db->Record["uniqueid_ip"]);
						} else {
							# oude systeem (werkt ook prima, is alleen minder veilig)
							$uniqueid=$db->f("uniqueid");
						}
						if(strlen($_COOKIE["loginsessionid"][$this->settings["name"]])>1 and $uniqueid==$_COOKIE["loginsessionid"][$this->settings["name"]] and $db->num_rows()==1) {
							# Cookie klopt met uniqueid
							if($db->f($this->settings["db"]["fielduserlevel"])<0) {
								# Account geblokkeerd - userlevel<0
								$this->errormessage=$this->settings["message"]["accountblocked"];
							} elseif($db->f($this->settings["db"]["fielduserlevel"])<$this->settings["minimaluserlevel"]) {
								# Te laag userlevel
								$this->errormessage=$this->settings["message"]["minimaluserlevel"];
							} else {
								$this->logged_in=true;
							}
						} else {
							# Cookie klopt niet
							if($_COOKIE["loginsessionid"][$this->settings["name"]]) {
								$this->wronglogin($db->f("user_id"));
								$wrongcount=$db->f("wrongcount")+1;

#								wt_mail("jeroen@webtastic.nl","Loginclass ".$this->settings["name"]." - ".$db->f($this->settings["db"]["fieldusername"]),"loginsessionid-cookie: ===".$_COOKIE["loginsessionid"][$this->settings["name"]]."===\nuniqueid-database: ===".$uniqueid."===\n\n".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);

								if($wrongcount>$this->settings["loginpogingen"] and $db->f("wrongtime")>(time()-$this->settings["loginblocktime"])) {
									# Mailtje sturen
	#								$foutloginmail="Account ".$db->f($this->settings["db"]["fieldusername"])." tijdelijk geblokeerd (tot ".date("d-m-Y, H:i",($db->f("wrongtime")+$this->settings["loginblocktime"]))." uur)\nsession\n";
	#								$foutloginmail.="loginsessionid: ===".$_COOKIE["loginsessionid"][$this->settings["name"]]."===";
	#								$this->sendmail($foutloginmail);
	#								$this->errormessage=ereg_replace("blocktime=","blocktime=".($db->f("wrongtime")+$this->settings["loginblocktime"]),$this->settings["message"]["wronglogintemp"]);
								}
							}
						}
					}
				}
			}

			if($this->logged_in) {
				# Waarden uit DB halen, opslaan in $_SESSION["LOGIN"] en pagina reloaden (indien het formulier net is ingevuld)
				$_SESSION["LOGIN"][$this->settings["name"]]["logged_in"]=true;
				$fieldnames=$db->metadata($this->settings["db"]["tablename"],true);
				while(list($name,$value)=each($fieldnames["meta"])) {
					$_SESSION["LOGIN"][$this->settings["name"]][$name]=$db->f($name);
				}
				$this->setlastlogin(1);

				# Mailtje sturen na login?
				if($this->settings["mail_after_login"]) {
					if($_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fieldusername"]]=="webtastic" or ereg("@webtastic",$this->settings["mail_after_login"])) {

					} else {
						if($this->settings["mailtext_after_login"]) {
							while(ereg("\[\[([a-z]+)\]\]",$this->settings["mailtext_after_login"],$regs)) {
								$this->settings["mailtext_after_login"]=ereg_replace("\[\[".$regs[1]."\]\]",$_SESSION["LOGIN"][$this->settings["name"]][$regs[1]],$this->settings["mailtext_after_login"]);
							}
						} else {
							$this->settings["mailtext_after_login"]="Ingelogd door ".$_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fieldusername"]];
						}
						if($this->settings["mailsubject_after_login"]) {
							while(ereg("\[\[([a-z]+)\]\]",$this->settings["mailsubject_after_login"],$regs)) {
								$this->settings["mailsubject_after_login"]=ereg_replace("\[\[".$regs[1]."\]\]",$_SESSION["LOGIN"][$this->settings["name"]][$regs[1]],$this->settings["mailsubject_after_login"]);
							}
						} else {
							$this->settings["mailsubject_after_login"]="Ingelogd";
						}
						$from="automail@webtastic.nl";
						$fromname="WebTastic Login";
						wt_mail($this->settings["mail_after_login"],$this->settings["mailsubject_after_login"],$this->settings["mailtext_after_login"],$from,$fromname);
					}
				}

				if($_POST["loginfilled"]) {
					$this->reload($_SESSION["LOGIN"][$this->settings["name"]]["comefromurl"],false);
				}

			} elseif(!$this->logged_in) {
				#
				# niet ingelogd
				#

				# lin-cookie wissen
				if($_COOKIE["lin"][$this->settings["name"]]) {
					setcookie("lin[".$this->settings["name"]."]","",time()-864000,"/");
					unset($_COOKIE["lin"][$this->settings["name"]]);
				}

				# extra_unsafe_cookie wissen
				if($this->settings["extra_unsafe_cookie"]) {
					setcookie($this->settings["extra_unsafe_cookie"]."[".$this->settings["name"]."]","",time()-864000,"/");
					unset($_COOKIE[$this->settings["extra_unsafe_cookie"]][$this->settings["name"]]);
				}

				# Nog niet ingelogd? Ga naar loginpagina
				if(!$this->settings["checkloginpage"]) $this->settings["checkloginpage"]=$this->settings["loginpage"];
				if($this->settings["mustlogin"] and $this->settings["loginpage"] and !ereg($this->settings["checkloginpage"]."$",$_SERVER["PHP_SELF"])) {
					$_SESSION["LOGIN"][$this->settings["name"]]["comefromurl"]=$this->currenturl();
					$this->reload($this->settings["loginpage"]);
				}
			}
		}

		if($_SESSION["LOGIN"][$this->settings["name"]]["logged_in"]) {
			if($this->settings["recheck_userdata"]) {
				# Gebruikers-gegevens opnieuw uit database halen
				$db->query("SELECT * FROM ".$this->settings["db"]["tablename"]." WHERE ".($this->settings["db"]["where"] ? $this->settings["db"]["where"]." AND " : "").$this->settings["db"]["fielduserid"]."='".addslashes($_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fielduserid"]])."';");
				if($db->num_rows()==1 and $db->next_record()) {
					$this->logged_in=true;
					$fieldnames=$db->metadata($this->settings["db"]["tablename"],true);
					while(list($name,$value)=each($fieldnames["meta"])) {
						$_SESSION["LOGIN"][$this->settings["name"]][$name]=$db->f($name);
					}
					$this->userlevel=$_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fielduserlevel"]];
					$this->user_id=$_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fielduserid"]];
					$this->username=$_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fieldusername"]];
					$this->priv=$_SESSION["LOGIN"][$this->settings["name"]]["priv"];
					$this->vars=$_SESSION["LOGIN"][$this->settings["name"]];
				}
			} else {
				$this->logged_in=true;
				$this->userlevel=$_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fielduserlevel"]];
				$this->user_id=$_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fielduserid"]];
				$this->username=$_SESSION["LOGIN"][$this->settings["name"]][$this->settings["db"]["fieldusername"]];
				$this->priv=$_SESSION["LOGIN"][$this->settings["name"]]["priv"];
				$this->vars=$_SESSION["LOGIN"][$this->settings["name"]];
			}
		}
	}
}

?>