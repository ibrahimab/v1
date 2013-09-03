<?php

#
# Dit script opent het juiste opmaak-bestand (op basis van $vars["websitetype"])
#

$opmaak = new opmaakonderdelen;

if($vars["websitetype"]==3) {
	# Zomerhuisje (.nl en .eu)
	include("content/opmaak_zomerhuisje.php");
} elseif($vars["websitetype"]==6) {
	# Chalets in Vallandy (.nl en .com)
	include("content/opmaak_vallandry.php");
} elseif($vars["websitetype"]==7) {
	# Italissima.nl
	include("content/opmaak_italissima.php");
} elseif($vars["websitetype"]==8) {
	# SuperSki
	include("content/opmaak_superski.php");
} elseif($vars["websitetype"]==9) {
	# Venturasol
	include("content/opmaak_venturasol.php");
} else {
	# Chalet (.nl, .eu, .be), Chalettour.nl
	include("content/opmaak_chalet.php");
}

?>