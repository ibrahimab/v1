<?php

echo "<!DOCTYPE html>\n<html>\n<head><title></title></head>\n";
echo "<body style=\"font-family:Verdana;font-size:0.8em;\">\n";

$keuzes[1][1][1]="Chalet.nl htmlbanner land-thema 728x90";
#$keuzes[1][2][1]="Chalet.nl htmlbanner land-thema 468x60";
$keuzes[1][3][1]="Chalet.nl htmlbanner land-thema 250x250";
#$keuzes[1][4][1]="Chalet.nl htmlbanner land-thema 234x60";
$keuzes[1][5][1]="Chalet.nl htmlbanner land-thema 120x600";
$keuzes[1][6][1]="Chalet.nl htmlbanner land-thema 300x250";

$keuzes[1][1][2]="Chalet.nl htmlbanner land-datum-aantal personen 728x90";
$keuzes[1][2][2]="Chalet.nl htmlbanner land-datum 468x60";
$keuzes[1][3][2]="Chalet.nl htmlbanner land-datum 250x250";
#$keuzes[1][4][2]="Chalet.nl htmlbanner land-datum 234x60";
$keuzes[1][5][2]="Chalet.nl htmlbanner land-datum-aantal personen 120x600";
$keuzes[1][6][2]="Chalet.nl htmlbanner land-datum 300x250";
$keuzes[1][7][2]="Chalet.nl htmlbanner land-datum-aantal personen 120x450";

$keuzes[4][1][1]="Chalet.be htmlbanner land-thema 728x90";
#$keuzes[4][2][1]="Chalet.be htmlbanner land-thema 468x60";
$keuzes[4][3][1]="Chalet.be htmlbanner land-thema 250x250";
#$keuzes[4][4][1]="Chalet.be htmlbanner land-thema 234x60";
$keuzes[4][5][1]="Chalet.be htmlbanner land-thema 120x600";
$keuzes[4][6][1]="Chalet.be htmlbanner land-thema 300x250";

$keuzes[4][1][2]="Chalet.be htmlbanner land-datum-aantal personen 728x90";
$keuzes[4][2][2]="Chalet.be htmlbanner land-datum 468x60";
$keuzes[4][3][2]="Chalet.be htmlbanner land-datum 250x250";
#$keuzes[4][4][2]="Chalet.be htmlbanner land-datum 234x60";
$keuzes[4][5][2]="Chalet.be htmlbanner land-datum-aantal personen 120x600";
$keuzes[4][6][2]="Chalet.be htmlbanner land-datum 300x250";
$keuzes[4][7][2]="Chalet.be htmlbanner land-datum-aantal personen 120x450";

#$keuzes[5][1][1]="SuperSki htmlbanner land-thema 728x90";
#$keuzes[5][2][1]="SuperSki htmlbanner land-thema 468x60";
#$keuzes[5][3][1]="SuperSki htmlbanner land-thema 250x250";
#$keuzes[5][4][1]="SuperSki htmlbanner land-thema 234x60";
#$keuzes[5][5][1]="SuperSki htmlbanner land-thema 120x600";
#$keuzes[5][6][1]="SuperSki htmlbanner land-thema 300x250";

$keuzes[5][1][2]="SuperSki htmlbanner land-datum 728x90";
$keuzes[5][2][2]="SuperSki htmlbanner land-datum 468x60";
$keuzes[5][3][2]="SuperSki htmlbanner land-datum 250x250";
$keuzes[5][4][2]="SuperSki htmlbanner land-datum 234x60";
$keuzes[5][5][2]="SuperSki htmlbanner land-datum 120x600";
$keuzes[5][6][2]="SuperSki htmlbanner land-datum 300x250";

$keuzes[2][1][1]="Zomerhuisje.nl htmlbanner land-thema 728x90";
$keuzes[2][2][1]="Zomerhuisje.nl htmlbanner land-thema 468x60";
$keuzes[2][3][1]="Zomerhuisje.nl htmlbanner land-thema 250x250";
$keuzes[2][4][1]="Zomerhuisje.nl htmlbanner land-thema 234x60";
$keuzes[2][5][1]="Zomerhuisje.nl htmlbanner land-thema 120x600";
$keuzes[2][6][1]="Zomerhuisje.nl htmlbanner land-thema 300x250";

$keuzes[2][1][2]="Zomerhuisje.nl htmlbanner land-datum 728x90";
$keuzes[2][2][2]="Zomerhuisje.nl htmlbanner land-datum 468x60";
$keuzes[2][3][2]="Zomerhuisje.nl htmlbanner land-datum 250x250";
$keuzes[2][4][2]="Zomerhuisje.nl htmlbanner land-datum 234x60";
$keuzes[2][5][2]="Zomerhuisje.nl htmlbanner land-datum 120x600";
$keuzes[2][6][2]="Zomerhuisje.nl htmlbanner land-datum 300x250";

$keuzes[3][1][2]="Italissima.nl htmlbanner regio-datum 728x90";
$keuzes[3][2][2]="Italissima.nl htmlbanner regio-datum 468x60";
$keuzes[3][3][2]="Italissima.nl htmlbanner regio-datum-personen 250x250";
$keuzes[3][4][2]="Italissima.nl htmlbanner regio-datum 234x60";
$keuzes[3][5][2]="Italissima.nl htmlbanner regio-datum-personen 120x600";

$keuzes[6][1][2]="Italissima.be htmlbanner regio-datum 728x90";
$keuzes[6][2][2]="Italissima.be htmlbanner regio-datum 468x60";
$keuzes[6][3][2]="Italissima.be htmlbanner regio-datum-personen 250x250";
$keuzes[6][4][2]="Italissima.be htmlbanner regio-datum 234x60";
$keuzes[6][5][2]="Italissima.be htmlbanner regio-datum-personen 120x600";

$netwerken[1]="TradeTracker";
$netwerken[2]="Sneeuwhoogte.nl";
#$netwerken[3]="Snowplaza";


$netwerk_wzt_niet_tonen[1]=array("0");
$netwerk_wzt_niet_tonen[2]=array("2","3","6");
#$netwerk_wzt_niet_tonen[3]=array("1","2","3","4","5","6");

// Sneeuwhoogte.nl: alleen 728x90-banners
$netwerk_alleen_deze_maten_tonen[2]=array("728x90");

for($i=1;$i<=3;$i++) {

	if(!$netwerken[$i]) {
		continue;
	}

	if($i>1) echo "<hr>";
	echo "<h2>HTML-banners voor ".htmlentities($netwerken[$i])."</h2>Kies een banner:<ul>";
	reset($keuzes);
	while(list($key,$value)=each($keuzes)) {
		if(!in_array($key,$netwerk_wzt_niet_tonen[$i])) {
				while(list($key2,$value2)=each($value)) {
					while(list($key3,$value3)=each($value2)) {
						$maat_tonen=false;
						if($netwerk_alleen_deze_maten_tonen[$i]) {
							foreach ($netwerk_alleen_deze_maten_tonen[$i] as $key4 => $value4) {
								if(strpos($value3,$value4)) {
									$maat_tonen=true;
								}
							}
						} else {
							$maat_tonen=true;
						}

						if($maat_tonen) {
							echo "<li><a href=\"htmlbanner.php?wzt=".$key."&t=".$key2."&themadatum=".$key3."&n=".$i."\" target=\"_blank\">".htmlentities($value3)."</a></li>";
						}
					}
				}
				echo "<br>";
		}
	}
	echo "</ul>";
}
echo "</body>";
echo "</html>";

?>