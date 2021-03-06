<?php


/**
* class met onderdelen die in alle opmaak.php's worden aangeroepen
*/

class opmaakonderdelen {

	private $toon_cookiebalk;


	function __construct() {
		# code...

		global $vars, $voorkant_cms, $login;


		if($vars["cookiemelding_tonen"] and $vars["websiteland"]=="nl" and (!$_COOKIE["cookiemelding_gelezen"] or ($voorkant_cms and ($login->user_id==15 or $login->user_id==26)))) {
			$this->toon_cookiebalk=true;
		} else {
			$this->toon_cookiebalk=false;
		}

		if($this->toon_cookiebalk) {
			// cookie plaatsen dat cookiebalk getoond is
			setcookie("cookiemelding_gelezen","1",time()+86400*365*10,"/");
		}
	}

	function toon_cookiebalk() {

		global $vars;

		if($this->toon_cookiebalk) {
			return true;
		} else {
			return false;
		}
	}


	function body_tag() {
		// toon de body-tag (en bijbehorende elementen)

		global $id, $onload, $vars, $data_onload, $voorkant_cms, $login;

		$return.="<body";
		if($onload) $return.=" onload=\"".$onload."\"";
		if($data_onload) $return.=" data-onload=\"".$data_onload."\"";
		$return.=" id=\"body_".$id."\"";

		$class="";

		if(preg_match("@Mac OS X@",$_SERVER["HTTP_USER_AGENT"])) {
			$class.=" mac-osx";
		}
		if(preg_match("@Android@",$_SERVER["HTTP_USER_AGENT"]) or preg_match("@iPad@",$_SERVER["HTTP_USER_AGENT"]) or preg_match("@iPhone@",$_SERVER["HTTP_USER_AGENT"])) {
			$class.=" mobile";
		}

		if($class) {
			$return.=" class=\"".trim($class)."\"";
		}

		$return.=">";

		// rode achtergrondkleur bij acceptatie-server
		if($vars["acceptatie_testserver"]) {

			$return.="
			<style>
			body {
				background-color: #f6adba;
			}
			</style>
			";
		}

		// parse internal user data (email address) for testing purposes
		if($voorkant_cms and is_object($login) and isset($login->vars) and isset($login->vars["email"])) {
			$data_email = $login->vars["email"];
		} elseif($vars["acceptatie_testserver"] and $_COOKIE["acceptance_email"]) {
			$data_email = $_COOKIE["acceptance_email"];
		} elseif($vars["acceptatie_testserver"]) {
			$data_email = "info@chalet.nl";
		} elseif($vars["lokale_testserver"]) {
			$data_email = "noreply@webtastic.nl";
		}
		if($data_email) {
			$return .= "<div class=\"voorkant_cms_datadiv\" data-email=\"".wt_he($data_email)."\">";
		}

		return $return;
	}

	function google_tag_manager() {
		$ie = (preg_match("@MSIE 8@",$_SERVER["HTTP_USER_AGENT"]) && preg_match("@accommodatie@",$_SERVER['SCRIPT_FILENAME']) === 1) ? true : false;
		global $voorkant_cms;

		if(!defined("wt_test") and !$voorkant_cms) {
			$return = google_tagmanager::place_start_script($ie);
			return $return;
		}
	}

	public function header_begin() {
		global $vars, $debugbarRenderer;

		if($vars["lokale_testserver"] and is_object($debugbarRenderer)) {
			$return .=  $debugbarRenderer->renderHead();
		}
		return $return;
	}


	public function header_end() {
		global $vars;

		if($vars["acceptatie_testserver"]) {
			$return .= "<script src=\"https://www.chalet.nl/acceptancetest-cookie.php\"></script>\n";
		}
		return $return;
	}

	public function body_end() {
		global $vars, $debugbarRenderer;

		if($vars["lokale_testserver"] and is_object($debugbarRenderer)) {
			$return .= $debugbarRenderer->render();
		}
		return $return;
	}

	public function html_at_the_bottom($html) {
		$this->html_at_the_bottom .= $html;
	}

	public function lazyLoadJs() {

		global $vars;

		$return[] = "'".$vars["path"]."scripts/allfunctions.js?c=".@filemtime("scripts/allfunctions.js")."'";

		$return[] = "'".$vars["path"]."vendor/jquery-chosen/chosen.jquery.1.3.0.js?cache=".@filemtime("vendor/jquery-chosen/chosen.jquery.1.3.0.js")."'";

		return $return;

	}

	public function link_rel_css() {

		global $vars;

		$return .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$vars["path"]."css/font-awesome.min.css\" />\n";

		$return .= "<link rel=\"stylesheet\" href=\"".$vars["path"]."vendor/jquery-chosen/chosen.1.3.0.css?cache=".@filemtime("vendor/jquery-chosen/chosen.1.3.0.css")."\" type=\"text/css\" />\n";

		return $return;


	}

}


?>