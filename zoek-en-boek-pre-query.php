<?php


//
//
// show zoek-en-boek with slightly different content and specific search query
//
//

$_GET["filled"] = 1;


if($_GET["pagetype"]=="landing-italissima") {

	if($_GET["pagecontent"]=="agriturismo-italie") {

		$landing_title = "Agriturismo Italë";
		$landing_photo = "agriturismo_italie.jpg";
		$landing_content_html="Een 'agriturismo' is een landelijk gelegen domein in Itali&euml; dat enerzijds wordt gebruikt als boerenbedrijf en anderzijds als toeristische accommodatie. In de meeste gevallen woont de eigenaar ook op de agriturismo zelf. De agriturismo als vakantieverblijf is momenteel erg in trek bij vakantiegangers. Wat hierbij aanspreekt is het genieten van de Italiaanse, landelijke levensstijl, de kleinschaligheid, de gastvrijheid en de lokale keuken met de producten van het landgoed zelf. Een vakantie in een agriturismo is een ideale combinatie van cultuur, sfeer en ontspanning. Kortom, al het goede van het Italiaanse leven.";

		$landing_searchtext = "Bekijk en doorzoek onze agriturismo in Italië";

		if($_GET["prequery"]) {
			$_GET["vf_kenm56"]=1;
		}
	} elseif($_GET["pagecontent"]=="agriturismo-toscane") {
		$landing_title = "Agriturismo Toscane";
		$landing_photo = "agriturismo_toscane.jpg";
		$landing_content_html="Toscane wordt gezien als &eacute;&eacute;n van de mooiste gebieden van Itali&euml;. Dit komt door het rijke cultuurhistorische erfgoed &eacute;n de schitterende natuur met de glooiende heuvels en de zo kenmerkende cipressen. Een agriturismo in Toscane is een ideale uitvalsbasis om bekende plaatsen als Florence, Siena, Pisa, Lucca en San Gimignano te verkennen. Vergeet hierbij ook absoluut de Chianti wijnstreek niet! Dus wil je wegblijven van het massa-toerisme, maar toch op rij-afstand zijn van de culturele en historische trekpleisters van Toscane, dan is een verblijf op een agriturismo ideaal!</p><p>De agriturismo vind je in principe door heel Itali&euml; maar vooral de regio Toscane staat er bekend om. Hier hebben wij dan ook een uitgebreid aanbod aan agriturismi. Onze agriturismi zijn over de hele regio Toscane verdeeld. Vrijwel iedere agritursimo beschikt over een zwembad en je kunt in veel gevallen de ter plekke verbouwde producten, meestal wijn en olijfolie, van de agriturismo proeven en/of kopen.";

		$landing_searchtext = "Bekijk en doorzoek onze agriturismo in Toscane";

		if($_GET["prequery"]) {
			$_GET["vf_kenm56"]=1;
			$_GET["fsg"]="5-124";
		}
	}


	if($landing_photo and $landing_title and $landing_content_html) {

		$pre_query_breadcrumb = $landing_title;

		$pre_query_content .= "<div style=\"width:690px;padding-right:5px;\"><div style=\"background-color:#ffd38f;padding:10px;margin-bottom:20px;\">";
		$pre_query_content .= "<h2 style=\"margin-bottom:10px;\">".wt_he($landing_title)."</h2><img src=\"".$vars["path"]."pic/".$landing_photo."\" width=\"250\" style=\"float:left;margin-bottom:20px;margin-right:10px;\"><p>".$landing_content_html."</p><div style=\"clear: both;\"></div></div>";

		$pre_query_content .= "<h2>".wt_he($landing_searchtext).":</h2><br/>";

		$title["zoek-en-boek"] = $landing_title;

	}

}

?>