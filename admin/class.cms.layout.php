<?php

class cms_layout {

	function cms_layout() {
		$this->settings["language"]="nl";
		$this->settings["favicon"]="favicon.ico";
		$this->settings["logo"]="pic/cms_logo.gif";
		$this->settings["jquery_google_api"]=false;
		$this->settings["jqueryui_google_api"]=false;
		$this->settings["jqueryui_google_api_theme"]="";

		$this->settings["message"]["gebruikersnaamuitloggen"]["nl"]="_VALgebruikersnaam_ uitloggen";
		$this->settings["message"]["gebruikersnaamuitloggen"]["en"]="log out _VALgebruikersnaam_";
		$this->settings["message"]["gebruikersnaamuitloggen"]["fr"]="deconnexion _VALgebruikersnaam_";

		$this->settings["message"]["terugnaarboven"]["nl"]="Terug naar boven";
		$this->settings["message"]["terugnaarboven"]["en"]="Back to top";
		$this->settings["message"]["terugnaarboven"]["fr"]="Retour au dessus";

		$this->settings["message"]["voorditonderdeelmoetuingelogdzijn"]["nl"]="Voor dit onderdeel moet u _HTMLlink1_ingelogd_HTMLlink2_ zijn.";
		$this->settings["message"]["voorditonderdeelmoetuingelogdzijn"]["en"]="This component requires you to be _HTMLlink1_logged in_HTMLlink2_.";
		$this->settings["message"]["voorditonderdeelmoetuingelogdzijn"]["fr"]="";

		$this->settings["message"][""]["nl"]="";
		$this->settings["message"][""]["en"]="";

		$this->settings["render_as_ie7"]=false;

		$bestandsnaam=split('/',$_SERVER["PHP_SELF"]);
		list($this->pageid)=split('[/.]',$bestandsnaam[count($bestandsnaam)-1]);
		return true;
	}

	function message($title,$html=true,$value_array="") {
		global $vars;
		$return=$this->settings["message"][$title][$this->settings["language"]];
		while(list($key,$value)=@each($value_array)) {
			$return=ereg_replace("_VAL".$key."_",$value,$return);
		}
		if($html) {
			if($vars["wt_htmlentities_cp1252"] or $vars["wt_htmlentities_utf8"]) {
				$return=wt_he($return);
			} else {
				$return=htmlentities($return,ENT_QUOTES,"iso-8859-15");
			}
		}
		@reset($value_array);
		while(list($key,$value)=@each($value_array)) {
			$return=ereg_replace("_HTML".$key."_",$value,$return);
		}
		return $return;
	}

	function menu_item($page,$title,$querystring,$login,$clickable=true,$options="") {
		$this->key_name=$this->convert_url($page,$querystring);
		$this->menu_item[$this->key_name]["page"]=$page;
		$this->menu_item[$this->key_name]["querystring"]=$querystring;
		$this->menu_item[$this->key_name]["title"]=$title;
		$this->menu_item[$this->key_name]["login"]=$login;
		$this->menu_item[$this->key_name]["clickable"]=$clickable;
		$this->menu_item[$this->key_name]["options"]=$options;
		$this->menu_title[$page]=$title;
	}

	function submenu_item($parentpage,$parentquerystring,$page,$title,$querystring,$login,$clickable=true,$options="") {
		$this->key_name=$this->convert_url($page,$querystring);
		$this->key_name_parent=$this->convert_url($parentpage,$parentquerystring);
		if($this->menu_item[$this->key_name_parent]["page"]) {
			$this->menu_item[$this->key_name_parent]["number_of_submenu_items"]++;
			$this->menu_item[$this->key_name]["submenu_item_counter"]=$this->menu_item[$this->key_name_parent]["number_of_submenu_items"];

			$this->menu_item[$this->key_name]["page"]=$page;
			$this->menu_item[$this->key_name]["querystring"]=$querystring;
			$this->menu_item[$this->key_name]["title"]=$title;
			$this->menu_item[$this->key_name]["login"]=$login;
			$this->menu_item[$this->key_name]["clickable"]=$clickable;
			$this->menu_item[$this->key_name]["options"]=$options;
#			$this->menu_title[$page]=$title;
			$this->menu_item[$this->key_name]["parent"]=$this->key_name_parent;
			$this->menu_item[$this->key_name_parent]["child"][$this->key_name]=$page;
			$this->menu_item[$this->key_name_parent]["child_qs"][$this->key_name]=$querystring;
		} else {
			trigger_error("WT-Error: Unknown parent '".$this->key_name_parent."' called",E_USER_ERROR);
		}
	}

	function convert_url($page,$querystring_array) {
		$return=$page;
		@ksort($querystring_array);
		while(list($key,$value)=@each($querystring_array)) {
			if($key<>"reloaded") $return.="--".$key."__WTIS__".$value;
		}
		return $return;
	}

	function display_all($current_title="") {
		global $cms,$db,$db2,$db3,$db4,$form,$login,$vars,$cms_form,$gegevens,$mustlogin,$_GLOBAL,$txt,$txta;
		if($current_title) {
			if($this->cmslog_pagina_id) {
				$db->query("UPDATE cmslog_pagina SET title='".addslashes($current_title)."' WHERE cmslog_pagina_id='".addslashes($this->cmslog_pagina_id)."';");
			}
		} else {
			if($this->title[$this->pageid]) {
				$current_title=$this->title[$this->pageid];
			} else {
				$current_title=$this->menu_title[$this->pageid];
			}
		}
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n";
		echo "  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
		echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
		echo "<head>\n";
		echo "<title>".wt_he($this->settings["system_name"]);


		if($current_title and $this->pageid<>"index" and $current_title<>$this->settings["system_name"]) echo " - ".wt_he($current_title);
		echo "</title>\n";
		if($vars["wt_htmlentities_utf8"]) {
			echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
		} else {
			echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
		}
		echo "<meta http-equiv=\"content-language\" content=\"".$this->settings["language"]."\" />\n";
		if($this->settings["render_as_ie7"]) {
			if($this->settings["chromeframe"]) {
				echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=EmulateIE7,chrome=1\" >\n";
			} else {
				echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=EmulateIE7\" >\n";
			}
		} elseif($this->settings["chromeframe"]) {
			echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" />\n";
		}
		echo "<meta name=\"robots\" content=\"noindex,nofollow\" />\n";
		echo "<link href=\"".($this->settings["css_folder"] ? $this->settings["css_folder"] : "")."cms_layout.css".($this->settings["css_cacheversion"] ? "?cache=".$this->settings["css_cacheversion"] : "")."\" rel=\"stylesheet\" type=\"text/css\" />\n";
		if(is_array($this->settings["extra_cssfiles"])) {
			while(list($key,$value)=each($this->settings["extra_cssfiles"])) {
				echo "<link href=\"".$value."\" rel=\"stylesheet\" type=\"text/css\" />\n";
			}
		}

		if(file_exists($this->settings["favicon"])) {
			echo "<link REL=\"shortcut icon\" href=\"".$this->settings["favicon"]."\" />";
		}


		# Speciaal voor alle niet-IE6 browsers
		if(!eregi("MSIE",$_SERVER["HTTP_USER_AGENT"]) or ereg("MSIE [789]",$_SERVER["HTTP_USER_AGENT"])) echo "<link href=\"".($this->settings["css_folder"] ? $this->settings["css_folder"] : "")."opmaak_moz.css\" rel=\"stylesheet\" type=\"text/css\" />\n";

		# jQuery via Google
		if($this->settings["jquery_google_api"]) {
			echo "<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js\"></script>\n";
		}

		# jQueryUI via Google
		if($this->settings["jqueryui_google_api"]) {
			if($this->settings["jqueryui_google_api_theme"]) {
				echo "<link rel=\"stylesheet\" href=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/".$this->settings["jqueryui_google_api_theme"]."/jquery-ui.css\" type=\"text/css\" />\n";
			}
			echo "<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js\"></script>\n";
		}

		# Google Chromeframe
		if($this->settings["chromeframe"]) {
			echo "<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js\"></script>\n";
		}

		# allfunctions.js
		if(file_exists("scripts/allfunctions.js")) {
			echo "<script type=\"text/javascript\" src=\"scripts/allfunctions.js?cache=".filemtime("scripts/allfunctions.js")."\"></script>\n";
		}

		if(is_array($this->settings["extra_javascriptfiles"])) {
			while(list($key,$value)=each($this->settings["extra_javascriptfiles"])) {
				echo "<script type=\"text/javascript\" src=\"".wt_he($value)."\"></script>\n";
			}
		}
		echo "<script type=\"text/javascript\" src=\"".($this->settings["scripts_folder"] ? $this->settings["scripts_folder"] : "")."cms_functions.js?t=1".($this->settings["javascript_cacheversion"] ? "&cache=".$this->settings["javascript_cacheversion"] : "")."\"></script>\n";


		if($this->settings["extra_header_code"]) {
			echo $this->settings["extra_header_code"];
		}

		echo "</head>\n";
		if($_SESSION["wt_popupmsg"]) {
			if($this->bodyonload and substr($this->bodyonload,-1)<>";") {
				$this->bodyonload.=";";
			}
			$this->bodyonload.="wt_popupmsg('".$_SESSION["wt_popupmsg"]."');";
			unset($_SESSION["wt_popupmsg"]);
		}
		if($this->bodyonload) {
			if($this->bodyonload and substr($this->bodyonload,-1)<>";") {
				$this->bodyonload.=";";
			}
			$this->bodyonload.="setHgt();";
		}
		echo "<body".($this->bodyonload ? " onload=\"".$this->bodyonload."\"" : "")."><div style=\"height:1px;\"><a name=\"top\"></a></div>";
		echo "<div id=\"wrapper\">\n";
		echo "<div id=\"top\">\n";
		echo "<div id=\"logo\">\n";
		if($this->pageid<>$this->settings["mainpage"]) {
			echo "<a href=\"";
			if($this->settings["mainpage"]=="index" and $_SERVER["DOCUMENT_ROOT"]!="/home/webtastic/html") {
				echo dirname($_SERVER["PHP_SELF"])."/";
			} else {
				echo $this->settings["mainpage"].".php";
			}
			echo "\">";
		}
		echo "<img src=\"";
		if(file_exists($this->settings["logo"])) {
			$size=getimagesize($this->settings["logo"]);
			echo $this->settings["logo"]."\" ".$size[3];
		} else {
			echo "http://www.webtastic.nl/pic/logo3.gif\" width=\"299\" height=\"39\"";
		}
		echo " border=\"0\" alt=\"".wt_he($this->settings["system_name"])."\">";
		if($this->pageid<>$this->settings["mainpage"]) echo "</a>";
		echo "</div>\n";
		echo "<div id=\"logout\">";
		if($login->logged_in) {
			echo "<a href=\"".$login->settings["loginpage"]."?logout=1\">";
			if($this->settings["loginname_field"]) {
				echo $this->message("gebruikersnaamuitloggen",true,array("gebruikersnaam"=>$login->vars[$this->settings["loginname_field"]]));
			} else {
				echo $this->message("gebruikersnaamuitloggen",true,array("gebruikersnaam"=>$login->username));
			}
			echo "</a>";
		}
		if($lists[$_GET["listid"]]) echo "<br><h1>".wt_he($lists[$_GET["listid"]])."</h1>";
		if($this->settings["logout_extra"]) echo $this->settings["logout_extra"];
		echo "</div></div><div id=\"menu\"><div id=\"meet_hoogte_menu\"><ul>";
		while(list($key,$value)=each($this->menu_item)) {
			unset($active_item);
			if(!$this->menu_item[$key]["login"] or $login->logged_in) {
				if($value["parent"]) echo "<span class=\"sub\">";
				echo "<li><div>";
				if($this->convert_url($this->pageid,$_GET)==$key or ($this->menu_item[$key]["child"][$this->convert_url($this->pageid,$_GET)] and !$this->menu_item[$key]["clickable"])) {
					$currentpage=true;
				} else {
					$currentpage=false;
				}

				if(!$this->menu_item[$key]["clickable"]) {
					if($this->menu_item[$key]["child"] and $this->menu_item[$key]["options"]["slide"]) {
						echo "<a href=\"javascript:togglesubmenu('submenudiv_".ereg_replace("-","_",$key)."',".$this->menu_item[$key]["number_of_submenu_items"].");\">";
						$slide_href=true;
					} else {
						echo "<span class=\"not_clickable\">";
					}
				} elseif($currentpage) {
					echo "<span class=\"selected\">";
				} else {
					echo "<a href=\"";
					if($this->menu_item[$key]["page"]=="index" and $_SERVER["DOCUMENT_ROOT"]!="/home/webtastic/html") {
						echo dirname($_SERVER["PHP_SELF"])."/";
					} else {
						echo $this->menu_item[$key]["page"].".php";
					}
					if($this->menu_item[$key]["querystring"]) {
						echo "?";
						reset($this->menu_item[$key]["querystring"]);
						unset($qsteller);
						while(list($key2,$value2)=each($this->menu_item[$key]["querystring"])) {
							if($qsteller) echo "&";
							echo $key2."=".urlencode($value2);
							$qsteller++;
						}
					}
					echo "\"";
					if($this->menu_item[$key]["options"]["target"]) {
						echo " target=\"".$this->menu_item[$key]["options"]["target"]."\"";
					}
					echo ">";
				}

				@reset($this->menu_item[$key]["querystring"]);
				unset($item_querystring_counter);
				while(list($key2,$value2)=@each($this->menu_item[$key]["querystring"])) {
					if($_GET[$key2]==$value2) {
						$item_querystring_counter++;
					}
				}
				if($item_querystring_counter==count($this->menu_item[$key]["querystring"])) $this->menu_item[$key]["correct_qs"]=true;
				@reset($this->menu_item[$key]["child"]);
				while(list($key2,$value2)=@each($this->menu_item[$key]["child"])) {
					if($value2==$this->pageid) {
						if(!$this->menu_item[$key]["child_qs"][$key2]) {
							$this->menu_item[$key]["correct_qs_child"]=true;
							break;
						} else {
							unset($item_querystring_counter);
							while(list($key3,$value3)=@each($this->menu_item[$key]["child_qs"][$key2])) {
								if($_GET[$key3]==$value3) {
									$item_querystring_counter++;
								}
							}
							if($item_querystring_counter==count($this->menu_item[$key]["child_qs"][$key2])) $this->menu_item[$key]["correct_qs_child"]=true;
						}
					}
				}

				if($currentpage
				or $this->menu_item[$key]["child"][$this->convert_url($this->pageid,$_GET)]
				or 	($this->menu_item[$key]["page"]==$this->pageid
					and 	(!$this->menu_item[$key]["querystring"]
						or (
							$this->menu_item[$key]["querystring"]
							and $this->menu_item[$key]["correct_qs"]
							and count($_GET)>count($this->menu_item[$key]["querystring"]
					))))
				or ($this->menu_item[$key]["correct_qs_child"])) {
					echo "<span class=\"selected_clicked\">";
					$active_item=true;
				} else {
					echo "<span>";
				}
				if($value["parent"]) echo "&nbsp;-&nbsp;";

				echo wt_he($value["title"]);
				echo "</span>";
				if($currentpage or !$this->menu_item[$key]["clickable"]) {
					if($slide_href) {
						echo "</a>";
					} else {
						echo "</span>";
					}
				} else {
					echo "</a>";
				}
				echo "</div></li>";
				if($value["parent"]) {
					echo "</span>";
					if($submenu_divhide and $this->menu_item[$value["parent"]]["number_of_submenu_items"]==$value["submenu_item_counter"]) {
						echo "</ul></div><ul>";
						unset($submenu_divhide);
					}
				}
				if($slide_href) {
					echo "</ul><div style=\"".($active_item||$_SESSION["cmslayout_openmenu"]["submenudiv_".ereg_replace("-","_",$key)] ? "" : "display:none;")."\" id=\"submenudiv_".ereg_replace("-","_",$key)."\"".($active_item||$_SESSION["cmslayout_openmenu"]["submenudiv_".ereg_replace("-","_",$key)] ? "" : " class=\"submenuclass\"")."><ul>";
					$slide_href=false;
					$submenu_divhide=true;
				}
			}
		}
		echo "</ul></div></div>\n";
		echo "<div id=\"content\" class=\"".$this->settings["content_class"]."\">";
		echo "<h1>";
		if($current_title) {
			echo wt_he($current_title);
		} else {
			echo wt_he($this->settings["system_name"]);
		}
		echo "</h1>";

		if(!$this->menu_item[$this->pageid]["login"] or $login->logged_in) {
			if(file_exists("content/".$this->pageid.".html")) {
				# Pagina tonen
				include "content/".$this->pageid.".html";
			} else {
				# Pagina niet aanwezig
				if($this->cmslog_pagina_id) {
					# Log wissen
					$db->query("DELETE FROM cmslog_pagina WHERE cmslog_pagina_id='".addslashes($this->cmslog_pagina_id)."';");
				}
			}
		} else {
			echo $this->message("voorditonderdeelmoetuingelogdzijn",true,array("link1"=>"<a href=\"".$login->settings["loginpage"]."\">","link2"=>"</a>"));
		}
		echo "<div id=\"terug\"><a href=\"#top\">".$this->message("terugnaarboven")."</a></div>\n";
		echo "</div><div id=\"clear\"></div>";
		echo "<div id=\"footer\">&copy; <a href=\"http://www.webtastic.nl/\" target=\"_blank\">WebTastic Webdesign</a>";
		if($this->settings["partner"]) echo " &<br>".$this->settings["partner"];
		echo "</div>";
		echo "</div>";
		if($this->settings["cms_via_verkeerde_site"]) {
			echo "<script type=\"text/javascript\">\nvar cms_via_verkeerde_site=1;\n</script>";
		}
		if($this->settings["html_bottom"]) {
			echo $this->settings["html_bottom"];
		}
		echo "</body></html>";
	}

	function end_declaration() {
#		echo wt_dump($_SESSION["cmslayout_openmenu"]);
		if($_GET["cmslayout_json"]==1) {
			if($_GET["cmslayout_openmenu"]) {
				$_SESSION["cmslayout_openmenu"][$_GET["cmslayout_openmenu"]]=true;
			}
			if($_GET["cmslayout_closemenu"]) {
				$_SESSION["cmslayout_openmenu"][$_GET["cmslayout_closemenu"]]=false;
			}
			echo json_encode(array("OK"=>1));
			exit;
		}

		return true;
	}
}

?>