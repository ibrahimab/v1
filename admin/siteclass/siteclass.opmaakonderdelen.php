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
                global $isMobile;
		// toon de body-tag (en bijbehorende elementen)

		global $id, $onload, $vars, $data_onload;

		$return.="<body";
                if($isMobile) {
                    if($id<>"index") $return.=" onscroll=\"document.getElementById('terugnaarboven').style.display='block'\"";
                } else {
                    if($id<>"index") $return.=" onscroll=\"document.getElementById('terugnaarboven').style.visibility='visible'\"";
                }
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
		return $return;
	}

	function google_tag_manager() {

		global $voorkant_cms;

		if(!defined("wt_test") and !$voorkant_cms) {
			$return = <<<EOT

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-5CPQNN"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5CPQNN');</script>
<!-- End Google Tag Manager -->

EOT;

			return $return;
		}

	}
}


?>