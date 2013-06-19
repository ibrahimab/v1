<?php


/**
*
*/
class tarieventabel {

	function __construct() {
		# code...
	}

	public function toontabel($type_id,$seizoen_id) {

		$this->haal_uit_database($type_id,$seizoen_id);

		$return .= $this->tabel_top();
		$return .= $this->tabel_content();
		$return .= $this->tabel_bottom();

		return $return;

	}

	private function haal_uit_database($type_id,$seizoen_id) {

	}

	private function tabel_top() {


		$return .= <<<EOT

		<table cellspacing="0" cellpadding="0" class="tarieventabel" id="tarieventabel_table[1]"><tr><th colspan="19">Tarieven winter 2013/2014<br/>
		<span style="font-weight:normal;">in euro's, per persoon, inclusief skipas</span></th></tr><tr><td>

EOT;
		return $return;

	}

	private function tabel_content() {

		$return .= <<<EOT

		<table cellspacing="0" class="tarieventabel">
			<tr><td>&nbsp;</td></tr>
			<tr><td>Aankomstdatum</td></tr>
			<tr><td>Aankomstdag</td></tr>
			<tr><td>Aantal nachten</td></tr>
			<tr><td>Accommodatiekorting</td></tr>
			<tr><td>12 personen</td></tr>
			<tr><td>11 personen</td></tr>
			<tr><td>10 personen</td></tr>
			<tr><td>9 personen</td></tr>
			<tr><td>8 personen</td></tr>
			<tr><td>7 personen</td></tr>
			<tr><td>6 personen</td></tr>
			<tr><td>5 personen</td></tr>
			<tr><td>4 personen</td></tr>
			<tr><td>3 personen</td></tr>
			<tr><td>2 personen</td></tr>
			<tr><td>1 persoon</td></tr>
		</table>

EOT;

		$return .= $this->tabel_tarieven();

		return $return;

	}

	private function tabel_tarieven() {
		$return .= <<<EOT

EOT;
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