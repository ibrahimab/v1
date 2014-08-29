<?php

// functions from allfunctions.php that require a mobile version


function wt_naam($voornaam='',$tussenvoegsel='',$achternaam,$achternaameerst=false,$voorletters=false) {
		global $vars, $isMobile;

	if($voornaam) $voornaam=trim($voornaam);
	if($tussenvoegsel) $tussenvoegsel=trim($tussenvoegsel);
	$achternaam=trim($achternaam);

	if($voorletters and $voornaam) {
		if(substr($voornaam,-1)<>".") $voornaam.=".";
	}
	if($achternaameerst) {
		$return=ucfirst($achternaam);
		if($voornaam or $tussenvoegsel) $return.=", ".($voornaam ? ucfirst($voornaam)." " : "").($tussenvoegsel ? $tussenvoegsel : "");
	} else {
		$return=ucfirst($voornaam);
		if($tussenvoegsel) {
			if($return) $return.=" ".$tussenvoegsel; else $return=$tussenvoegsel;
		}
		if($return) $return.=" ".ucfirst($achternaam); else $return=ucfirst($achternaam);
	}

	$return = preg_replace("@ {2,}@"," ",$return);
		if($isMobile){
				$nameInTussenvoegsel = explode(" ", $return);

				foreach($nameInTussenvoegsel as $name){
					if(in_array(strtolower($name), $vars["availableTussenvoegsel"])){
						$names[] = lcfirst($name);
					}else{
						$names[] = $name;
					}
				}
				$finalName = implode(" ", $names);

				return $finalName;
		}else {
				return $return;
		}
}

?>