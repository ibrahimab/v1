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
		$return .= $this->tabel_tarieven();
		$return .= $this->tabel_bottom();

		return $return;

	}

	private function haal_uit_database($type_id,$seizoen_id) {

	}

	private function tabel_top() {


		$return = <<<EOT

		<table cellspacing="0" cellpadding="0" class="tarieventabel" id="tarieventabel_table[1]"><tr><th colspan="19">Tarieven winter 2013/2014<br/>
		<span style="font-weight:normal;">in euro's, per persoon, inclusief skipas</span></th></tr><tr class="kopgroot"><td width="10%">&nbsp;</td>

EOT;
		return $return;

	}

	private function tabel_tarieven() {

	}

	private function tabel_bottom() {
		$return = <<<EOT
			</table>
EOT;

		return $return;

	}

}



?>