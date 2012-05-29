<?php

#
# Dit script opent het juiste opmaak-bestand (op basis van $vars["websitetype"])
#

if($vars["websitetype"]==2) {
	# Wintersportaccommodaties.nl
	include("content/opmaak_wsa.php");
} elseif($vars["websitetype"]==6) {
	# Chalets in Vallandy (.nl en .com)
	include("content/opmaak_vallandry.php");
} elseif($vars["websitetype"]==7) {
	# Italissima.nl
	include("content/opmaak_italissima.php");
} elseif($vars["websitetype"]==3) {
	# Zomerhuisje (.nl en .eu)
	include("content/opmaak_zomerhuisje.php");
} else {
	# Chalet (.nl, .eu, .be), Chalettour.nl
	include("content/opmaak_chalet.php");
}

?>