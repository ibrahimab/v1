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
		$autocomplete["ski in ski out"][] = "vf_piste1=1";
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

		// $autocomplete["grote groepen"][] = "vf_kenm46=1";
		$autocomplete["groepsaccommodaties"][] = "vf_kenm46=1";

		// aantal badkamers
		$autocomplete["minimaal 2 badkamers"][] = "vf_badk1=1";
		$autocomplete["minimaal 3 badkamers"][] = "vf_badk1=2";
		$autocomplete["minimaal 4 badkamers"][] = "vf_badk1=3";
		$autocomplete["minimaal 5 badkamers"][] = "vf_badk1=4";
		$autocomplete["minimaal 6 badkamers"][] = "vf_badk1=5";
		$autocomplete["minimaal 8 badkamers"][] = "vf_badk1=6";
		$autocomplete["minimaal 10 badkamers"][] = "vf_badk1=7";


	} elseif($vars["websitetype"]==3) {
		#
		# Zomerhuisje
		#
		$autocomplete["zwembad"][] = "vf_kenm8=1";

		$autocomplete["tennisbaan"][] = "vf_kenm9=1";

		$autocomplete["speeltoestellen"][] = "vf_kenm10=1";

		$autocomplete["restaurant"][] = "vf_kenm37=1";

		$autocomplete["privé-terras"][] = "vf_kenm15=1";

		$autocomplete["privé-zwembad"][] = "vf_kenm18=1";

		$autocomplete["huisdieren toegestaan"][] = "vf_kenm19=1";

		$autocomplete["huisdieren niet toegestaan"][] = "vf_kenm20=1";

		$autocomplete["wellness"][] = "vf_kenm24=1";

		$autocomplete["golfbaan of max. 20 km"][] = "vf_kenm2=1";

		$autocomplete["greenfee-proof"][] = "vf_kenm29=1";

		$autocomplete["kindvriendelijk"][] = "vf_kenm21=1";
		$autocomplete["goed voor kids"][] = "vf_kenm21=1";

		$autocomplete["bij het water"][] = "vf_kenm31=1";


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

		$autocomplete["huisdieren toegestaan"][] = "vf_kenm19=1";

		$autocomplete["huisdieren niet toegestaan"][] = "vf_kenm20=1";

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
	if($vars["websitetype"]==1) {

		#
		# Chalet.eu
		#
		$autocomplete["on the slopes"][] = "vf_piste1=1";

		$autocomplete["catering possible"][] = "vf_kenm2=1";

		$autocomplete["pets allowed"][] = "vf_kenm6=1";

		$autocomplete["fire place / wood stove"][] = "vf_kenm7=1";

		$autocomplete["good for kids"][] = "vf_kenm43=1";
		$autocomplete["child friendly"][] = "vf_kenm43=1";

		$autocomplete["grading A for après-ski"][] = "vf_kenm45=1";
		$autocomplete["après-ski"][] = "vf_kenm45=1";

		$autocomplete["large groups"][] = "vf_kenm46=1";
		$autocomplete["group accommodations"][] = "vf_kenm46=1";
	}
}











?>