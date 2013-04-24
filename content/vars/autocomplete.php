<?php

#
# Tekst-verfijningen: autocomplete koppelen aan het verfijnblok
#

if($vars["taal"]=="nl") {
	#
	# Nederlands
	#

	if($vars["websitetype"]==1 or $vars["websitetype"]==4 or $vars["websitetype"]==8) {
		#
		# Chalet / Chalettour / SuperSki
		#
		$autocomplete["aan de piste"][] = "vf_piste1=1";
		// $autocomplete["bij de piste"][] = "vf_piste1=1";
		// $autocomplete["op de piste"][] = "vf_piste1=1";

		$autocomplete["catering mogelijk"][] = "vf_kenm2=1";
		// $autocomplete["met catering"][] = "vf_kenm2=1";

		$autocomplete["huisdieren toegestaan"][] = "vf_kenm6=1";

		$autocomplete["open haard/houtkachel"][] = "vf_kenm7=1";
		// $autocomplete["bij de open haard"][] = "vf_kenm7=1";
		// $autocomplete["met open haard"][] = "vf_kenm7=1";


		$autocomplete["goed voor kids"][] = "vf_kenm43=1";
		$autocomplete["kindvriendelijk"][] = "vf_kenm43=1";

		$autocomplete["10 voor après-ski"][] = "vf_kenm45=1";
		$autocomplete["après-ski"][] = "vf_kenm45=1";

		$autocomplete["grote groepen"][] = "vf_kenm46=1";
		$autocomplete["groepsaccommodaties"][] = "vf_kenm46=1";


	} elseif($vars["websitetype"]==3) {
		#
		# Zomerhuisje
		#


	} elseif($vars["websitetype"]==7) {
		#
		# Italissima
		#
		$autocomplete["gemeenschappelijk zwembad"][] = "vf_kenm8=1";

		$autocomplete["privé-zwembad"][] = "vf_kenm18=1";

		$autocomplete["gemeenschappelijke tuin/terras"][] = "vf_kenm49=1";

		$autocomplete["privé-tuin/terras"][] = "vf_kenm15=1";

		$autocomplete["balkon"][] = "vf_kenm16=1";

		$autocomplete["restaurant"][] = "vf_kenm37=1";

		$autocomplete["speeltoestellen"][] = "vf_kenm10=1";

		$autocomplete["tennisbaan"][] = "vf_kenm9=1";

		$autocomplete["wasmachine"][] = "vf_kenm11=1";

		$autocomplete["internet / Wi-Fi"][] = "vf_kenm50=1";

		$autocomplete["huisdieren niet toegestaan"][] = "vf_kenm20=1";

		$autocomplete["huisdieren toegestaan"][] = "vf_kenm19=1";

		$autocomplete["kindvriendelijk"][] = "vf_kenm21=1";

		$autocomplete["grote groepen"][] = "vf_kenm28=1";
		$autocomplete["groepsaccommodaties"][] = "vf_kenm28=1";

		$autocomplete["wijndomein"][] = "vf_kenm35=1";

		$autocomplete["golfbaan of max. 20 km"][] = "vf_kenm2=1";

		$autocomplete["agriturismo"][] = "vf_kenm38=1";



	}

} elseif($vars["taal"]=="en") {
	#
	# Engels
	#

}











?>