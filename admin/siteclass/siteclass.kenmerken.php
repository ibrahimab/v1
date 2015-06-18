<?php

/**
 * class to get facilities (kenmerken)
 *
 * @author Jeroen Boschman (jeroen@webtastic.nl)
 * @since  2015-05-26 21:00
*/

class kenmerken extends chaletDefault
{

	/**  type_id of accommodation to query  */
	private $type_id;

	/**
	 * call the parent constructor
	 *
	 * @return void
	 */
	function __construct()
	{

		parent::__construct();

	}

	/**
	 * query all needed data from the database
	 *
	 * @return void
	 */
	private function query_database()
	{
		$db = new DB_sql;

		$db->query("SELECT
					t.kenmerken AS kenmerken_type,
					a.kenmerken AS kenmerken_accommodatie,
					p.kenmerken AS kenmerken_plaats,
					s.kenmerken AS kenmerken_skigebied,
					a.toonper
					FROM type t
					INNER JOIN accommodatie a USING (accommodatie_id)
					INNER JOIN plaats p USING (plaats_id)
					INNER JOIN skigebied s USING (skigebied_id)

					WHERE t.type_id='".intval($this->type_id)."'
		;");
		if( $db->next_record() ) {
			$kenmerken_array["type"] = $db->f( "kenmerken_type" );
			$kenmerken_array["accommodatie"] = $db->f( "kenmerken_accommodatie" );
			$kenmerken_array["plaats"] = $db->f( "kenmerken_plaats" );
			$kenmerken_array["skigebied"] = $db->f( "kenmerken_skigebied" );
			$kenmerken_array["toonper"] = $db->f( "toonper" );
		}

		return $kenmerken_array;

	}

	/**
	 * convert comma seperated list of kenmerken-id's to an key-array of active kenmerken
	 *
	 * @param integer type_id of wanted accommodation-type
	 * @param array already available database-data (type, accommodatie, plaats, skigebied)
	 * @return array
	 */
	public function get_kenmerken( $type_id, $kenmerken_array = "" )
	{

		$this->type_id = $type_id;

		if(!is_array($kenmerken_array) ) {
			$kenmerken_array = $this->query_database();
		}

		$kenmerken_type 		= explode(',', $kenmerken_array['type']);
		$kenmerken_accommodatie = explode(',', $kenmerken_array['accommodatie']);
		$kenmerken_plaats	    = explode(',', $kenmerken_array['plaats']);
		$kenmerken_skigebied	= explode(',', $kenmerken_array['skigebied']);
		$toon_kenmerken			= [];

		if( $this->config->seizoentype==1 ) {
			//
			// winter
			//

			if(@in_array("23", $kenmerken_type) or @in_array("24", $kenmerken_accommodatie)) {
				$toon_kenmerken["jacuzzi-bubbelbad"] = true;
			}
			if(@in_array("2", $kenmerken_type) or @in_array("2", $kenmerken_accommodatie)) {
				$toon_kenmerken["aan-de-piste"] = true;
			}
			if(@in_array("1", $kenmerken_type) or @in_array("1", $kenmerken_accommodatie)) {
				$toon_kenmerken["catering-mogelijk"] = true;
			}
			if(@in_array("11", $kenmerken_type) or @in_array("13", $kenmerken_accommodatie)) {
				$toon_kenmerken["huisdieren-toegestaan"] = true;
			}
			if(@in_array("22", $kenmerken_type) or @in_array("23", $kenmerken_accommodatie)) {
				$toon_kenmerken["internet-via-wifi"] = true;
			} elseif(@in_array("20", $kenmerken_type) or @in_array("21", $kenmerken_accommodatie)) {
				$toon_kenmerken["internetverbinding"] = true;
			}
			if(@in_array("10", $kenmerken_type) or @in_array("12", $kenmerken_accommodatie)) {
				$toon_kenmerken["open-haard-houtkachel"] = true;
			}
			if(@in_array("3", $kenmerken_type) or @in_array("3", $kenmerken_accommodatie)) {
				$toon_kenmerken["sauna-prive"] = true;
			} elseif(@in_array("10", $kenmerken_accommodatie)) {
				$toon_kenmerken["sauna-gemeenschappelijk"] = true;
			}
			if(@in_array("16", $kenmerken_type) or @in_array("17", $kenmerken_accommodatie)) {
				$toon_kenmerken["wasmachine"] = true;
			}
			if(@in_array("4", $kenmerken_type) or @in_array("4", $kenmerken_accommodatie)) {
				$toon_kenmerken["zwembad-prive"] = true;
			} elseif(@in_array("11", $kenmerken_accommodatie)) {
				$toon_kenmerken["zwembad"] = true;
			}

			if( $kenmerken_array["toonper"]==1 and !$this->config->wederverkoop ) {
				$toon_kenmerken["inclusief-skipas"] = true;
			}

		} elseif($this->config->websitetype==7) {
			//
			// Italissima
			//
			if(@in_array("27", $kenmerken_type) or @in_array("45", $kenmerken_accommodatie)) {
				$toon_kenmerken["airconditioning"] = true;
			}
			if(@in_array("19", $kenmerken_type) or @in_array("33", $kenmerken_accommodatie)) {
				$toon_kenmerken["balkon-prive"] = true;
			}
			if(@in_array("28", $kenmerken_type) or @in_array("29", $kenmerken_type) or @in_array("46", $kenmerken_accommodatie) or @in_array("47", $kenmerken_accommodatie)) {
				$toon_kenmerken["barbecue"] = true;
			}
			if(@in_array("39", $kenmerken_accommodatie)) {
				$toon_kenmerken["centrum-op-loopafstand"] = true;
			}
			if(@in_array("11", $kenmerken_type) or @in_array("13", $kenmerken_accommodatie)) {
				$toon_kenmerken["huisdieren-toegestaan"] = true;
			}
			if(@in_array("24", $kenmerken_type) or @in_array("42", $kenmerken_accommodatie)) {
				$toon_kenmerken["internet-via-wifi"] = true;
			} elseif(@in_array("22", $kenmerken_type) or @in_array("36", $kenmerken_accommodatie)) {
				$toon_kenmerken["internetverbinding"] = true;
			}
			if(@in_array("4", $kenmerken_type) or @in_array("4", $kenmerken_accommodatie)) {
				$toon_kenmerken["zwembad-prive"] = true;
			} else {
				if(@in_array("11", $kenmerken_accommodatie)) {
					$toon_kenmerken["zwembad"] = true;
				}
			}
			if(@in_array("23", $kenmerken_type) or @in_array("38", $kenmerken_accommodatie)) {
				$toon_kenmerken["omheinde-tuin"] = true;
			}
			if(@in_array("40", $kenmerken_accommodatie)) {
				$toon_kenmerken["restaurant-op-domein"] = true;
			}
			if(@in_array("26", $kenmerken_accommodatie)) {
				$toon_kenmerken["speeltoestellen"] = true;
			}
			if(@in_array("19", $kenmerken_accommodatie)) {
				$toon_kenmerken["tennisbaan"] = true;
			}
			if(@in_array("17", $kenmerken_type) or @in_array("30", $kenmerken_accommodatie)) {
				$toon_kenmerken["tuin-terras-prive"] = true;
			} elseif(@in_array("20", $kenmerken_type) or @in_array("34", $kenmerken_accommodatie)) {
				$toon_kenmerken["tuin-terras-of-balkon"] = true;
			}
			if(@in_array("9", $kenmerken_accommodatie)) {
				$toon_kenmerken["wellness-faciliteiten"] = true;
			}
			if(@in_array("18", $kenmerken_type) or @in_array("32", $kenmerken_accommodatie)) {
				$toon_kenmerken["wasmachine"] = true;
			}
		}

		return $toon_kenmerken;

	}
}
