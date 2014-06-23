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
		$landing_photo_style = "margin-bottom:20px;";
		$landing_content_html="Een accommodatie mag een 'agriturismo' worden genoemd als het landelijk gelegen domein in Itali&euml; is, dat enerzijds wordt gebruikt als boerenbedrijf en anderzijds als toeristisch verblijf. In veel gevallen woont de eigenaar ook op de agriturismo zelf. De agriturismo als vakantieverblijf is momenteel erg in trek. Wat gasten hierbij aanspreekt is het genieten van de Italiaanse, landelijke levensstijl, de kleinschaligheid, de gastvrijheid en de lokale keuken met de producten van het landgoed zelf. Een vakantie in een agriturismo is een ideale combinatie van cultuur, sfeer en ontspanning. Kortom, al het goede van het Italiaanse leven.";

		$landing_content2_html="<h3>Wat maakt een agriturismo in Itali&euml; zo bijzonder?</h3><p>Een vakantie op een agriturismo in Itali&euml; heeft het volgende te bieden:<ul>
		<li>Vakantie in een rustige omgeving, weg van de drukte</li>
		<li>Comfortabele vakantiehuizen in landelijke stijl</li>
		<li>Een agriturismo is omgeven door veel ruimte</li>
		<li>De gemoedelijke sfeer van het platteland van Itali&euml;</li>
		<li>Op een agriturismo ervaar je de gastvrijheid waar Itali&euml; bekend om staat</li>
		<li>Genieten van de streekproducten die geproduceerd zijn op de eigen agriturismo</li>
		<li>Panoramisch uitzicht vanaf de agriturismo over het glooiende landschap van Itali&euml;</li>
		<li>Een agriturismo in Itali&euml; is een perfecte uitvalsbasis om de omgeving te verkennen.</li></ul></p>";

		$landing_moretext = "meer over onze agriturismi in Italië";
		$landing_content_slide_html="<h2>Onze agriturismi in Itali&euml;</h2>
		<p>De agriturismo is er in veel verschillende soorten, van eenvoudig tot super-de-luxe. Italissima beschikt dan ook over een gevarieerd aanbod van vakantiehuizen op agriturismi door heel Itali&euml;. Vrijwel iedere agriturismo beschikt over een (soms priv&eacute;-)zwembad en je kunt in veel gevallen de ter plekke verbouwde producten van de agriturismo ook proeven en/of kopen.</p>
		<p>Let ook op de uitstekende prijs/kwaliteitsverhouding van een agriturismo! Een weekverblijf op een agriturismo begint bij zo&rsquo;n 300 euro in het laagseizoen en vaak kun je tegen schappelijke prijzen met de eigenaren van de agriturismo mee-eten, wat natuurlijk ook weer geld scheelt.</p>
		<p>Veel succes met de zoektocht naar je ideale agriturismo in Itali&euml;! En mocht je vragen hebben: aarzel dan niet om <a href=\"".$vars["path"]."contact.php\">contact</a> met ons op te nemen.</p>";

		$landing_searchtext = "Bekijk en doorzoek ons agriturismo-aanbod in Italië";

		$landing_canonical = "agriturismo-italie";

		$landing_meta_description = "Op zoek naar een heerlijke vakantie op een agriturismo in Italië? Bekijk dan hier ons volledige aanbod aan agriturismi en boek eenvoudig online.";

		if($_GET["prequery"]) {
			$_GET["vf_kenm56"]=1;
		}
	} elseif($_GET["pagecontent"]=="agriturismo-toscane") {
		$landing_title = "Agriturismo Toscane";
		$landing_photo = "agriturismo_toscane.jpg";
		$landing_photo_style = "margin-bottom:0px;";
		$landing_content_html="Toscane wordt gezien als &eacute;&eacute;n van de mooiste gebieden van Itali&euml;. Dit komt door het rijke cultuurhistorische erfgoed &eacute;n de schitterende natuur met de glooiende heuvels en de zo kenmerkende cipressen. Een agriturismo in Toscane is een ideale uitvalsbasis om bekende plaatsen als Florence, Siena, Pisa, Lucca en San Gimignano te verkennen. Vergeet hierbij ook absoluut de Chianti wijnstreek niet! Dus wil je wegblijven van het massa-toerisme, maar toch op rij-afstand zijn van de culturele en historische trekpleisters van Toscane, dan is een verblijf op een agriturismo ideaal!</p><p>De agriturismo vind je in principe door heel Itali&euml; maar vooral de regio Toscane staat er bekend om. Hier hebben wij dan ook een uitgebreid aanbod aan agriturismi. Onze agriturismi zijn over de hele regio Toscane verdeeld. Vrijwel iedere agritursimo beschikt over een zwembad en je kunt in veel gevallen de ter plekke verbouwde producten, meestal wijn en olijfolie, van de agriturismo proeven en/of kopen.";

		$landing_content2_html = "<h3>Wat maakt een agriturismo in Toscane zo bijzonder?</h3><p>Een vakantie op een agriturismo in Itali&euml; heeft het volgende te bieden:<ul><li>Vakantie in een rustige omgeving, weg van de drukte</li><li>Comfortabele vakantiehuizen in landelijke stijl</li><li>Agriturismo omgeven door veel ruimte</li><li>De gemoedelijke sfeer van het platteland van Toscane</li><li>De gastvrijheid waar Toscane bekend om staat</li><li>Genieten van de streekproducten die geproduceerd zijn op de eigen agriturismo</li><li>Panoramisch uitzicht vanaf de agriturismo over het glooiende landschap van Toscane</li><li>Een agriturismo in Toscane is een perfecte uitvalsbasis om de regio te verkennen.</li></ul></p>";

		$landing_moretext = "meer over onze agriturismi in Toscane";
		$landing_content_slide_html="<h2>Wat is een agriturismo?</h2><p>Een 'agriturismo' is een landelijk gelegen domein dat enerzijds wordt gebruikt als boerenbedrijf en anderzijds als toeristische accommodatie. Wat hierbij aanspreekt is het genieten van de Toscaanse, landelijke levensstijl, de kleinschaligheid, de gastvrijheid en de lokale keuken met de producten van het landgoed zelf. Een vakantie in een agriturismo is een ideale combinatie van cultuur, sfeer en ontspanning: al het goede van Toscane. Let hierbij ook op de uitstekende prijs/kwaliteitsverhouding van de agriturismo! Een weekverblijf op een agriturismo begint bij zo&rsquo;n 300 euro in het laagseizoen en vaak kun je tegen schappelijke prijzen met de eigenaren van de agriturismo mee-eten. De agriturismo is er dan ook in vele gedaanten, van eenvoudig tot super-de-luxe.</p><p>Veel succes met de zoektocht naar je ideale agriturismo in Toscane! En mocht je vragen hebben: aarzel dan niet om <a href=\"".$vars["path"]."contact.php\">contact</a> met ons op te nemen.</p>";

		$landing_searchtext = "Bekijk en doorzoek onze agriturismi in Toscane";

		$landing_canonical = "agriturismo-toscane";

		$landing_meta_description = "De perfecte agriturismo in Toscane boek je eenvoudig online bij Italissima! Wij zijn specialist in agriturismi verspreid door heel Italië.";

		if($_GET["prequery"]) {
			$_GET["vf_kenm56"]=1;
			$_GET["fsg"]="5-124";
		}
	} elseif($_GET["pagecontent"]=="vakantie-in-italie") {
		$landing_title = "Vakantie in Italië - La bella Italia";
		$landing_photo = "vakantie-in-italie.jpg";
		$landing_photo_style = "margin-bottom:5px;";
		$landing_content_html="Wie op vakantie naar Italië gaat, kiest voor een land met een enorm, misschien wel niet te evenaren aanbod aan cultuur, historie, gastronomie en mode. Daarnaast is Italië ook een vakantieland met een heel gevarieerd landschap. Iedere regio in Italië heeft zo zijn unieke kenmerken en hierdoor is het vaak lastig aan te geven welk stukje van Italië nu echt het mooiste is.<br /><br />Populaire regio's voor een vakantie in Italië zijn Toscane, Umbrië en het merengebied in Lombardije.";

		$landing_content2_html = "<p><a href=\"/regio/Toscane/\">Toscane</a> herbergt 120 natuurreservaten en is met haar glooiende landschap en kenmerkende cipressen absoluut &eacute;&eacute;n van de mooiste gebieden van Itali&euml;. Daarnaast is Toscane de bakermat van de renaissance en herbergt het schitterende historische steden als Florence, Siena, Lucca, San Gimignano, Cortona en Volterra. Als je naar Toscane gaat, dan mag een bezoek aan de schitterende Chianti wijnstreek ook zeker niet ontbreken.</p><p>De streek <a href=\"/regio/Umbrie/\">Umbri&euml;</a> is ook een zeer populaire regio en wordt ook wel het groene hart van Itali&euml; genoemd, vanwege de uitgestrekte groene velden. Daarnaast zijn in Umbri&euml; tal van historische ommuurde heuvelstadjes te vinden.</p><p><a href=\"/regio/Merengebied_Lombardije/\">Het Merengebied</a> is al jaren een geliefd gebied voor Nederlandse vakantiegangers. De bekendste meren zijn het Gardameer, het Comomeer, het Lago Maggiore en het Luganomeer. Het Merengebied ligt op ca. 1000 tot 1200 kilometer van Nederland en biedt een perfecte combinatie van een ontspannen zonvakantie aan het meer en een gevarieerde omgeving met gezellige dorpjes en mooie vergezichten. Deze streek is een ideaal vakantiegebied voor watersporten en  wandel- en fietstochten.</p><p>Een gebied wat sterk in populariteit stijgt voor een vakantie naar Itali&euml; is <a href=\"/regio/Le_Marche/\">Le Marche</a>, in het Nederlands &lsquo;De Marken&rsquo;. Vergeleken met andere regio&rsquo;s herbergt Le Marche een wat meer onontdekte, pure Italiaanse sfeer. Het kustgebied wordt gevormd door robuuste rotspartijen en brede stranden. Het achterland kenmerkt zich door glooiende heuvels met middeleeuwse dorpjes en historische steden.</p><p>Maar ook een vakantie in onze overige regio's <a href=\"/regio/Campanie/\">Campani&euml;</a>, <a href=\"/regio/Ligurie/\">Liguri&euml;</a>, de <a href=\"/regio/Dolomieten/\">Dolomieten</a>, <a href=\"/regio/Lazio/\">Lazio</a>, <a href=\"/regio/Piemonte/\">Piemonte</a>, <a href=\"/regio/Sardinie/\">Sardini&euml;</a> en <a href=\"/regio/Sicilie/\">Sicili&euml;</a> zijn zeker aan te raden: elke regio in Itali&euml; heeft zo zijn eigen charme en aantrekkingskracht en zijn zeker het bezoeken waard.</p>";

		$landing_moretext = "meer over onze vakantiehuizen in Italië";
		$landing_content_slide_html="<h3>Onze vakantiehuizen in Itali&euml;</h3>Evenals het Italiaanse land is ook ons aanbod van vakantiehuizen in Itali&euml; zeer divers. Voor wie iets heel bijzonders zoekt, hebben wij een aantal zeer verrassende vakantiehuizen in ons aanbod, zoals een vakantiehuis dat  als decor diende in een James Bond film of een appartement in &eacute;&eacute;n van de torens van San Gimignano. Veel vakantiegangers willen in Itali&euml; genieten van het authentieke Italiaanse platteland en kennis maken met de typische streekproducten. Een verblijf op een <a href=\"/agriturismo-italie\">agriturismo</a> is hierbij aan te bevelen. Een agriturismo is een landelijk gelegen domein dat enerzijds wordt gebruikt als boerenbedrijf en anderzijds als toeristisch verblijf. Vooral in <a href=\"/agriturismo-toscane\">Toscane</a> hebben wij op het gebied van agriturismi veel keuzemogelijkheden. Ook kan het overnachten in een vakantiehuis op een <a href=\"/zoek-en-boek.php?filled=1&fzt=&vf_kenm35=1#vf\">wijndomein</a> een extra leuke invulling aan je vakantie geven.<br />Zoek je het comfort van en de gezelligheid van een wat groter domein, bekijk dan eens onze selectie van <a href=\"/zoek-en-boek.php?filled=1&fzt=&sp=1&vf_kenm7=1#vf\">vakantieparken</a>.</p><p>Kortom: met bijna 1.000 verschillende vakantiehuizen door heel Itali&euml; hebben wij iedereen die op vakantie naar Itali&euml; wil gaan wel wat te bieden. Bekijk de <a href=\"/bestemmingen.php\">kaart van Itali&euml;</a> voor een handig overzicht van de verschillende regio's met het aantal bijbehorende vakantiehuizen. Klik op de regio voor meer informatie en de vakantiehuizen aldaar.";

		$landing_searchtext = "Bekijk en doorzoek onze vakantiehuizen in Italië";

		$landing_canonical = "vakantie-in-italie";

		$landing_meta_description = "Op zoek naar een vakantiehuis in Italië? Bekijk hier bijna 1.000 karakteristieke vakantiehuizen, vakantievilla's en agriturismi verdeeld over heel Italië";

		if($_GET["prequery"]) {

		}
	}

	if($landing_photo and $landing_title and $landing_content_html) {

		$pre_query_breadcrumb = $landing_title;

		$pre_query_content .= "<div style=\"width:690px;padding-right:5px;\"><div style=\"background-color:#ffd38f;padding:10px;margin-bottom:20px;\">";
		$pre_query_content .= "<h2 style=\"margin-bottom:10px;\">".wt_he($landing_title)."</h2><img src=\"".$vars["path"]."pic/".$landing_photo."\" width=\"250\" style=\"float:left;margin-right:10px;".$landing_photo_style."\"><p>".$landing_content_html."</p><div style=\"clear: both;\"></div></div>";

		$pre_query_content .= $landing_content2_html;

		$pre_query_content .= "<div style=\"text-align:right;margin-bottom:15px;\"><a href=\"#\" class=\"slide_more\" data-open=\"".wt_he($landing_moretext)." &raquo;\" data-close=\"&laquo; inklappen\">".wt_he($landing_moretext)." &raquo;</a></div>";



		$pre_query_content .= "<div class=\"slide_more_div\">".$landing_content_slide_html."<br/></div>";

		$pre_query_content .= "<h2>".wt_he($landing_searchtext).":</h2><br/>";

		$title["zoek-en-boek"] = $landing_title;

	}

}

?>