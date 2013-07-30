<?php


/**
*
*/
class tarieventabel {

	function __construct() {

	}

	public function toontabel($type_id,$seizoen_id) {

		$this->haal_uit_database($type_id,$seizoen_id);


		$return .= "<div class=\"tarieventabel_wrapper\">";

		$return .= $this->tabel_top();
		$return .= $this->tabel_content();
		$return .= $this->tabel_bottom();

		$return .= "</div>";

		return $return;

	}

	private function haal_uit_database($type_id,$seizoen_id) {

	}

	private function tabel_top() {

		$return .= "<div class=\"tarieventabel_top\">";
		$return .= "<h1>Tarieven winter 2013/2014</h1>in euro's, per persoon, inclusief skipas";
		$return .= "</div>";

		return $return;

	}

	private function tabel_content() {

		$this->seizoen_id=22;
		$this->type_id=241;

		$this->tarieven_uit_database();

		// bepalen welke aantallen personen direct zichtbaar moeten zijn
		if($_GET["ap"]) {
			$maxtonen=$_GET["ap"]+2;
			$mintonen=$_GET["ap"]-2;
		} else {
			$max=max(array_keys($this->aantal_personen));
			$maxtonen=$max;
			$mintonen=$max-4;
		}


		$return .= <<<EOT

		<table cellspacing="0" class="tarieventabel_border tarieventabel_titels_links">
			<tr class="tarieventabel_maanden"><td>&nbsp;</td></tr>
			<tr class="tarieventabel_datumbalk"><td>Aankomstdatum</td></tr>
			<tr class="tarieventabel_datumbalk"><td>Aankomstdag</td></tr>
			<tr class="tarieventabel_datumbalk"><td>Aantal nachten</td></tr>
			<tr><td>Accommodatiekorting</td></tr>

EOT;

		// regels met aantal personen tonen
		foreach ($this->aantal_personen as $key => $value) {
			$return.="<tr><td>".$key."&nbsp;".($key==1 ? html("persoon","tarieventabel") : html("personen","tarieventabel"))."</td></tr>";
		}

		$return.="</table>";

		$return .= $this->tabel_tarieven();

		return $return;

	}

	private function tabel_tarieven() {

		global $vars;

		$return.="<div class=\"tarieventabel_wrapper_rechts\"><table cellspacing=\"0\" class=\"tarieventabel_border tarieventabel_content\">";

		# regel met maanden
		$return.="<tr class=\"tarieventabel_maanden\">";
		foreach ($this->maand as $key => $value) {

			$unixtime=mktime(0,0,0,substr($key,5,2),1,substr($key,0,4));
			$return.="<td colspan=\"".$value."\">".DATUM("MAAND JJJJ",$unixtime,$vars["taal"])."</td>";
		}
		$return.="</tr>";

		# regel met aankomstdatum
		$return.="<tr class=\"tarieventabel_datumbalk tarieventabel_datumbalk_content\">";
		foreach ($this->dag as $key => $value) {
			$return.="<td>".$value."</td>";
		}
		$return.="</tr>";

		# regel met dag van de week
		$return.="<tr class=\"tarieventabel_datumbalk tarieventabel_datumbalk_content\">";
		foreach ($this->dag_van_de_week as $key => $value) {
			$return.="<td>".$value."</td>";
		}
		$return.="</tr>";

		# regel met aantal nachten
		$return.="<tr class=\"tarieventabel_datumbalk tarieventabel_datumbalk_content\">";
		foreach ($this->dag_van_de_week as $key => $value) {
			$return.="<td>".$this->aantalnachten[$key]."</td>";
		}
		$return.="</tr>";

		# regel met accommodatiekorting
		$return.="<tr class=\"tarieventabel_korting_tr\">";
		foreach ($this->dag_van_de_week as $key => $value) {
			if($this->aanbieding[$key]) {
				$return.="<td><div class=\"tarieventabel_tarieven_korting\">".wt_he($this->aanbieding[$key])."</div></td>";
			} else {
				$return.="<td>&nbsp;</td>";
			}
		}
		$return.="</tr>";

		# daadwerkelijke tarieven tonen
		foreach ($this->aantal_personen as $key => $value) {

			$return.="<tr class=\"tarieventabel_tarieven\">";
			foreach ($this->dag as $key2 => $value2) {
				$return.="<td><div class=\"tarieventabel_tarieven_div\">";

				if($this->aanbieding[$key2]) {
					$return.="<div class=\"tarieventabel_tarieven_korting\">";
				}

				$return.=number_format($this->tarief[$key][$key2],0,",",".");

				if($this->aanbieding[$key2]) {
					$return.="</div>";
				}

				$return.="</div></td>";
			}
			$return.="</tr>";
		}

		$return.="</table></div>";

		return $return;

	}

	private function tarieven_uit_database() {

		global $vars, $accinfo;

		$db = new DB_sql;



		# Accinfo
		if($accinfo) {
			$this->accinfo=$accinfo;
		} else {
			$this->accinfo=accinfo($this->type_id);
		}

		# Controle op vertrekdagaanpassing?
		include($vars["unixdir"]."content/vertrekdagaanpassing.html");

		// tarieven uit database halen
		$db->query("SELECT week, prijs, personen FROM tarief_personen WHERE type_id='".$this->type_id."' AND seizoen_id='".$this->seizoen_id."' AND week>UNIX_TIMESTAMP(NOW()) ORDER BY week, personen DESC;");
		while($db->next_record()) {
			if(!$this->begin) $this->begin=$db->f("week");
			$this->eind=$db->f("week");
			$this->tarief[$db->f("personen")][$db->f("week")]=$db->f("prijs");

			$this->aantal_personen[$db->f("personen")]=true;

		}

// echo wt_dump($this->tarief);
// exit;

		// aantal weken in een maand bepalen
		$week=$this->begin;
		while($week<=$this->eind) {

			if($vertrekdag[$this->seizoen_id][date("dm",$week)] or $this->accinfo["aankomst_plusmin"]) {
				$aangepaste_unixtime=mktime(0,0,0,date("m",$week),date("d",$week)+$vertrekdag[$this->seizoen_id][date("dm",$week)]+$this->accinfo["aankomst_plusmin"],date("Y",$week));
			} else {
				$aangepaste_unixtime=$week;
				$exacte_unixtime=$week;
			}

			$this->dag[$week]=date("d",$aangepaste_unixtime);
			$this->dag_van_de_week[$week]=strftime("%a",$aangepaste_unixtime);
			$this->maand[date("Y-m",$aangepaste_unixtime)]++;


			$exacte_unixtime=$aangepaste_unixtime;

			$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
		}

		# Aantal nachten bepalen
		$week=$this->begin;
		$eind=mktime(0,0,0,date("m",$this->eind),date("d",$this->eind)+7,date("Y",$this->eind));
		while($week<=$eind) {
			# Afwijkende vertrekdag
			$aantalnachten_afwijking[date("dm",$week)]+=$vertrekdag[$this->seizoen_id][date("dm",$week)];
			$vorigeweek=mktime(0,0,0,date("n",$week),date("j",$week)-7,date("Y",$week));
			$aantalnachten_afwijking[date("dm",$vorigeweek)]-=$vertrekdag[$this->seizoen_id][date("dm",$week)];

			# Afwijkende verblijfsduur
			$aantalnachten_afwijking[date("dm",$week)]=$aantalnachten_afwijking[date("dm",$week)]+$accinfo["aankomst_plusmin"]-$accinfo["vertrek_plusmin"];

			if($aantalnachten_afwijking[date("dm",$week)]) {
				$this->aantalnachten[$week]=$aantalnachten_afwijking[date("dm",$week)];
			} else {
				$this->aantalnachten[$week]=7;
			}

			$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
		}


		# aanbiedingen
		$this->aanbieding[1388185200]="10%";
		$this->aanbieding[1389394800]="15%";

// echo wt_dump($maand);

		return $return;
	}

	private function tabel_bottom() {
		$return .= <<<EOT
			</td></tr>
			</table>
EOT;

		return $return;

	}
}



?>