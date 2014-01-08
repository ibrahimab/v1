<?php


//
//
// show zoek-en-boek with slightly different content and specific search query
//
//

$_GET["filled"] = 1;


if($_GET["pagetype"]=="landing-italissima") {

	if($_GET["pagecontent"]=="agriturismo-italie") {

		$landing_title = "Agriturismo Italië";
		$landing_photo = "agriturismo_italie.jpg";
		$landing_content_html="Een 'agriturismo' is een landelijk gelegen domein in Itali&euml; dat enerzijds wordt gebruikt als boerenbedrijf en anderzijds als toeristische accommodatie. In de meeste gevallen woont de eigenaar ook op de agriturismo zelf. De agriturismo als vakantieverblijf is momenteel erg in trek bij vakantiegangers. Wat hierbij aanspreekt is het genieten van de Italiaanse, landelijke levensstijl, de kleinschaligheid, de gastvrijheid en de lokale keuken met de producten van het landgoed zelf. Een vakantie in een agriturismo is een ideale combinatie van cultuur, sfeer en ontspanning. Kortom, al het goede van het Italiaanse leven.";

		$landing_moretext = "meer over onze agriturismo in Italië";
		$landing_content_slide_html="<h2>Onze agriturismi in Itali&euml;</h2><p>De agriturismo is er in vele gedaanten, van eenvoudig tot super-de-luxe. Italissima beschikt dan ook over een gevarieerd aanbod van vakantiehuizen op agriturismi door heel Itali&euml;. Let hierbij ook op de uitstekende prijs/kwaliteitsverhouding van een agriturismo! Een weekverblijf op een agriturismo begint bij zo&rsquo;n 300 euro in het laagseizoen en vaak kun je tegen schappelijke prijzen met de eigenaren van de agriturismo mee-eten. Vrijwel iedere agriturismo beschikt over een zwembad en je kunt in veel gevallen de ter plekke verbouwde producten, meestal wijn en olijfolie, van de agriturismo ook proeven en/of kopen.</p><h3>Kortom: wat maakt een agriturismo in Itali&euml; zo bijzonder?</h3><p>Een vakantie op een agriturismo in Itali&euml; heeft het volgende te bieden:<ul><li>Vakantie in een rustige omgeving, weg van de drukte</li><li>Comfortabele vakantiehuizen in landelijke stijl</li><li>Agriturismo omgeven door veel ruimte</li><li>De gemoedelijke sfeer van het platteland van Itali&euml;</li><li>De gastvrijheid waar Itali&euml; bekend om staat</li><li>Genieten van de streekproducten die geproduceerd zijn op de eigen agriturismo</li><li>Panoramisch uitzicht vanaf de agriturismo over het glooiende landschap van Itali&euml;</li><li>Een agriturismo in Itali&euml; is een perfecte uitvalsbasis om de omgeving te verkennen.</li></ul></p><p>Veel succes met de zoektocht naar je ideale agriturismo in Itali&euml;! En mocht je vragen hebben: aarzel dan niet om <a href=\"".$vars["path"]."contact.php\">contact</a> met ons op te nemen.</p>";

		$landing_searchtext = "Bekijk en doorzoek onze agriturismo in Italië";

		$landing_canonical = "agriturismo-italie";

		if($_GET["prequery"]) {
			$_GET["vf_kenm56"]=1;
		}
	} elseif($_GET["pagecontent"]=="agriturismo-toscane") {
		$landing_title = "Agriturismo Toscane";
		$landing_photo = "agriturismo_toscane.jpg";
		$landing_content_html="Toscane wordt gezien als &eacute;&eacute;n van de mooiste gebieden van Itali&euml;. Dit komt door het rijke cultuurhistorische erfgoed &eacute;n de schitterende natuur met de glooiende heuvels en de zo kenmerkende cipressen. Een agriturismo in Toscane is een ideale uitvalsbasis om bekende plaatsen als Florence, Siena, Pisa, Lucca en San Gimignano te verkennen. Vergeet hierbij ook absoluut de Chianti wijnstreek niet! Dus wil je wegblijven van het massa-toerisme, maar toch op rij-afstand zijn van de culturele en historische trekpleisters van Toscane, dan is een verblijf op een agriturismo ideaal!</p><p>De agriturismo vind je in principe door heel Itali&euml; maar vooral de regio Toscane staat er bekend om. Hier hebben wij dan ook een uitgebreid aanbod aan agriturismi. Onze agriturismi zijn over de hele regio Toscane verdeeld. Vrijwel iedere agritursimo beschikt over een zwembad en je kunt in veel gevallen de ter plekke verbouwde producten, meestal wijn en olijfolie, van de agriturismo proeven en/of kopen.";

		$landing_moretext = "meer over onze agriturismo in Toscane";
		$landing_content_slide_html="<h2>Wat is een agriturismo?</h2><p>Een 'agriturismo' is een landelijk gelegen domein dat enerzijds wordt gebruikt als boerenbedrijf en anderzijds als toeristische accommodatie. Wat hierbij aanspreekt is het genieten van de Toscaanse, landelijke levensstijl, de kleinschaligheid, de gastvrijheid en de lokale keuken met de producten van het landgoed zelf. Een vakantie in een agriturismo is een ideale combinatie van cultuur, sfeer en ontspanning: al het goede van Toscane. Let hierbij ook op de uitstekende prijs/kwaliteitsverhouding van de agriturismo! Een weekverblijf op een agriturismo begint bij zo&rsquo;n 300 euro in het laagseizoen en vaak kun je tegen schappelijke prijzen met de eigenaren van de agriturismo mee-eten. De agriturismo is er dan ook in vele gedaanten, van eenvoudig tot super-de-luxe.</p><h3>Kortom: wat maakt een agriturismo in Toscane zo bijzonder?</h3><p>Een vakantie op een agriturismo in Itali&euml; heeft het volgende te bieden:<ul><li>Vakantie in een rustige omgeving, weg van de drukte</li><li>Comfortabele vakantiehuizen in landelijke stijl</li><li>Agriturismo omgeven door veel ruimte</li><li>De gemoedelijke sfeer van het platteland van Toscane</li><li>De gastvrijheid waar Toscane bekend om staat</li><li>Genieten van de streekproducten die geproduceerd zijn op de eigen agriturismo</li><li>Panoramisch uitzicht vanaf de agriturismo over het glooiende landschap van Toscane</li><li>Een agriturismo in Toscane is een perfecte uitvalsbasis om de regio te verkennen.</li></ul></p><p>Veel succes met de zoektocht naar je ideale agriturismo in Toscane! En mocht je vragen hebben: aarzel dan niet om <a href=\"".$vars["path"]."contact.php\">contact</a> met ons op te nemen.</p>";

		$landing_searchtext = "Bekijk en doorzoek onze agriturismo in Toscane";

		$landing_canonical = "agriturismo-toscane";

		if($_GET["prequery"]) {
			$_GET["vf_kenm56"]=1;
			$_GET["fsg"]="5-124";
		}
	}


	if($landing_photo and $landing_title and $landing_content_html) {

		$pre_query_breadcrumb = $landing_title;

		$pre_query_content .= "<div style=\"width:690px;padding-right:5px;\"><div style=\"background-color:#ffd38f;padding:10px;margin-bottom:20px;\">";
		$pre_query_content .= "<h2 style=\"margin-bottom:10px;\">".wt_he($landing_title)."</h2><img src=\"".$vars["path"]."pic/".$landing_photo."\" width=\"250\" style=\"float:left;margin-bottom:20px;margin-right:10px;\"><p>".$landing_content_html."<br/><br/><div style=\"text-align:right;\"><a href=\"#\" class=\"slide_more\" data-open=\"".wt_he($landing_moretext)." &raquo;\" data-close=\"&laquo; inklappen\">".wt_he($landing_moretext)." &raquo;</a></div></p><div style=\"clear: both;\"></div></div>";

		$pre_query_content .= "<div class=\"slide_more_div\">".$landing_content_slide_html."<br/></div>";

		$pre_query_content .= "<h2>".wt_he($landing_searchtext).":</h2><br/>";

		$title["zoek-en-boek"] = $landing_title;

	}

}

?>