<?php

/**
* class to pre-calculate the minimum prices (vanafprijs) per type
*
* @author: Jeroen Boschman (jeroen@webtastic.nl)
* @since: 2015-06-09 10:00
*/

class vanafprijs extends chaletDefault
{

	/**  object for bijkomendekosten */
	private $bk;

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
	 * calculate all types where `vanafprijs_bereken_opnieuw` is not null in table `type`
	 *
	 * @param integer limit the number of types to calculate
	 * @return void
	 */
	public function calculate_all_open_types( $limit=0 ) {

		$db = new DB_sql;

		if( $limit>0 ) {
			$limit_query = " LIMIT 0,".intval($limit);
		}


		$db->query("SELECT type_id FROM type WHERE vanafprijs_bereken_opnieuw IS NOT NULL ORDER BY vanafprijs_bereken_opnieuw".$limit_query.";");
		while( $db->next_record() ) {
			$inquery .= ",".$db->f( "type_id" );
		}

		if( $inquery ) {

			$inquery = substr($inquery, 1);

			$this->calculate_types( $inquery );

		}

	}


	/**
	 * set all types (where `archief`=0) to calculate (`vanafprijs_bereken_opnieuw` is set to NOW() in table `type`)
	 *
	 * @return void
	 */
	public function set_all_types_to_calculate() {

		$db = new DB_sql;

		$db->query("SELECT DISTINCT type_id FROM view_accommodatie WHERE archief=0;");
		while( $db->next_record() ) {
			$inquery .= ",".$db->f( "type_id" );
		}

		if( $inquery ) {
			$inquery = substr($inquery, 1);

			$db->query("UPDATE type SET vanafprijs_bereken_opnieuw=NOW() WHERE type_id IN (".$inquery.");");
			// echo $db->lq."<br />";

		}

	}


	/**
	 * set one type to calculate (`vanafprijs_bereken_opnieuw` is set to NOW() in table `type`)
	 *
	 * @param integer type_id to calculate
	 * @return void
	 */
	public static function set_type_to_calculate( $type_id ) {

		$db = new DB_sql;

		$db->query("UPDATE type SET vanafprijs_bereken_opnieuw=NOW() WHERE type_id='".intval( $type_id )."';");

	}

	/**
	 * get all prices of one or more type_ids (comma separated)
	 *
	 * @param integer/string comma separated list of type_id's
	 * @return array with price (value) per type (key)
	 */
	public function get_vanafprijs($type_ids) {

		$db = new DB_sql;

		if( $this->config->voorkant_cms and $this->config->wederverkoop ) {
			// voorkant_cms and wederverkoop
			$price_field = "prijs_wederverkoop_intern";

		} elseif ( $this->config->wederverkoop ) {
			// wederverkoop
			$price_field = "prijs_wederverkoop";

		} elseif ( $this->config->voorkant_cms ) {
			// wederverkoop
			$price_field = "prijs_intern";

		} else {
			// normal price
			$price_field = "prijs";

		}

		$query = "SELECT type_id, `".$price_field."` AS prijs FROM cache_vanafprijs_incl_bkk_type WHERE type_id IN (".wt_as($type_ids).");";

		$db->query( $query );
		while( $db->next_record() ) {
			$return [ $db->f( "type_id" )] = floatval($db->f( "prijs" ));
		}

		return $return;

	}

	/**
	 * internal function to get the minimum price of a specific query
	 *
	 * @param integer type_id
	 * @param boolean arrangement (including skipas) or not
	 * @param integer maximum number of persons for this type
	 * @param integer Winter (1) or Summer (2) type
	 * @param string the actual query; should contain the fields `prijs`, `week`, `seizoen_id`
	 * @return mixed the calculated minimum price
	 */
	private function query_minimum_price($type_id, $arrangement, $maxaantalpersonen, $wzt, $query) {

		$db = new DB_sql;

		$db->query($query);
		if( $db->num_rows() ) {
			while( $db->next_record() ) {

				$prijs[$db->f( "week" )] = $db->f("prijs");

				if( $arrangement ) {
					$bk_add_to_price = $this->bk[$wzt][$type_id][$db->f( "seizoen_id" )][$maxaantalpersonen] / $maxaantalpersonen;
				} else {
					$bk_add_to_price = $this->bk[$wzt][$type_id][$db->f( "seizoen_id" )][1];
				}
				$prijs[$db->f( "week" )] += $bk_add_to_price;
			}

			$vanafprijs = min($prijs);
			$vanafprijs = round( $vanafprijs, 2);

			return $vanafprijs;

		} else {
			return false;
		}
	}

	/**
	 * calculate one or more types (comma separated)
	 *
	 * @param integer/string comma separated list of type_id's
	 * @return void
	 */
	private function calculate_types( $type_ids ) {

		$db = new DB_sql;

		$db2 = new DB_sql;

		$bijkomendekosten = new bijkomendekosten;

		// winter
		$this->bk[1] = $bijkomendekosten->get_complete_cache(1);

		// summer
		$this->bk[2] = $bijkomendekosten->get_complete_cache(2);



		// tarievenbekend nieuw seizoen: zomer
		$tarievenbekend_seizoen_id.=",25"; // Italissima

		// tarievenbekend nieuw seizoen: winter
		$db->query("SELECT DISTINCT tarievenbekend_seizoen_id FROM thema WHERE actief=1;");
		while($db->next_record()) {
			$tarievenbekend_seizoen_id.=",".$db->f("tarievenbekend_seizoen_id");
		}

		// check for verzameltypes
		$db->query("SELECT DISTINCT verzameltype_parent FROM type WHERE verzameltype_parent IS NOT NULL AND type_id IN (".$type_ids.");");
		while($db->next_record()) {
			self::set_type_to_calculate($db->f("verzameltype_parent"));
		}


		$db->query("UPDATE cache_vanafprijs_incl_bkk_type SET wis=1 WHERE type_id IN (".$type_ids.");");
		$db->query("SELECT type_id, toonper, wzt, maxaantalpersonen FROM view_accommodatie WHERE type_id IN (".$type_ids.") ORDER BY type_id;");
		while($db->next_record()) {
			unset($prijs, $update);
			if($db->f("toonper")==1) {
				//
				// arrangement (toonper=1)
				//

				$query = "SELECT tp.prijs AS prijs, tp.week, sz.seizoen_id FROM tarief tr, tarief_personen tp, seizoen sz WHERE tr.seizoen_id=sz.seizoen_id AND tp.type_id=tr.type_id AND tp.seizoen_id=sz.seizoen_id AND tp.week=tr.week AND tr.week>".time()." AND sz.tonen=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND tp.prijs>0 AND tp.type_id='".intval($db->f("type_id"))."';";
				$prijs["normaal"] = $this->query_minimum_price($db->f("type_id"), true, $db->f( "maxaantalpersonen" ), $db->f( "wzt" ), $query);

				$query = "SELECT tp.prijs AS prijs, tp.week, sz.seizoen_id FROM tarief tr, tarief_personen tp, seizoen sz WHERE tr.seizoen_id=sz.seizoen_id AND tp.type_id=tr.type_id AND tp.seizoen_id=sz.seizoen_id AND tp.week=tr.week AND tr.week>".time()." AND sz.tonen>=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND tp.prijs>0 AND tp.type_id='".intval($db->f("type_id"))."';";
				$prijs["intern"] = $this->query_minimum_price($db->f("type_id"), true, $db->f( "maxaantalpersonen" ), $db->f( "wzt" ), $query);

				if($tarievenbekend_seizoen_id) {

					$query = "SELECT tp.prijs AS prijs, tp.week, sz.seizoen_id FROM tarief tr, tarief_personen tp, seizoen sz WHERE sz.seizoen_id IN (".substr($tarievenbekend_seizoen_id,1).") AND tr.seizoen_id=sz.seizoen_id AND tp.type_id=tr.type_id AND tp.seizoen_id=sz.seizoen_id AND tp.week=tr.week AND tr.week>".time()." AND sz.tonen>=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND tp.prijs>0 AND tp.type_id='".intval($db->f("type_id"))."';";
					$prijs["normaal_nieuw_seizoen"] = $this->query_minimum_price($db->f("type_id"), true, $db->f( "maxaantalpersonen" ), $db->f( "wzt" ), $query);

				}

			} else {
				//
				// accommodatie (toonper=3)
				//

				$query = "SELECT tr.c_verkoop_site AS prijs, tr.week, sz.seizoen_id FROM accommodatie a, type t, tarief tr, seizoen sz WHERE a.toonper=3 AND tr.type_id=t.type_id AND tr.seizoen_id=sz.seizoen_id AND tr.week>".time()." AND sz.tonen=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND t.accommodatie_id=a.accommodatie_id AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND t.type_id='".intval($db->f("type_id"))."';";
				$prijs["normaal"] = $this->query_minimum_price($db->f("type_id"), false, $db->f( "maxaantalpersonen" ), $db->f( "wzt" ), $query);

				$query = "SELECT tr.c_verkoop_site AS prijs, tr.week, sz.seizoen_id FROM accommodatie a, type t, tarief tr, seizoen sz WHERE a.toonper=3 AND tr.type_id=t.type_id AND tr.seizoen_id=sz.seizoen_id AND tr.week>".time()." AND sz.tonen>=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND t.accommodatie_id=a.accommodatie_id AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND t.type_id='".intval($db->f("type_id"))."';";
				$prijs["intern"] = $this->query_minimum_price($db->f("type_id"), false, $db->f( "maxaantalpersonen" ), $db->f( "wzt" ), $query);

				if($tarievenbekend_seizoen_id) {

					$query = "SELECT tr.c_verkoop_site AS prijs, tr.week, sz.seizoen_id FROM accommodatie a, type t, tarief tr, seizoen sz WHERE sz.seizoen_id IN (".substr($tarievenbekend_seizoen_id,1).") AND a.toonper=3 AND tr.type_id=t.type_id AND tr.seizoen_id=sz.seizoen_id AND tr.week>".time()." AND sz.tonen=4 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND t.accommodatie_id=a.accommodatie_id AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND t.type_id='".intval($db->f("type_id"))."';";
					$prijs["normaal_nieuw_seizoen"] = $this->query_minimum_price($db->f("type_id"), false, $db->f( "maxaantalpersonen" ), $db->f( "wzt" ), $query);

				}
			}

			if($db->f("wzt")==1) {

				// wederverkoop-prijs bepalen
				$query = "SELECT tr.wederverkoop_verkoopprijs AS prijs, tr.week, sz.seizoen_id FROM accommodatie a, type t, tarief tr, seizoen sz WHERE (a.toonper=1 OR a.toonper=3) AND tr.type_id=t.type_id AND tr.seizoen_id=sz.seizoen_id AND tr.blokkeren_wederverkoop=0 AND tr.week>".time()." AND sz.tonen=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND t.accommodatie_id=a.accommodatie_id AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND t.type_id='".intval($db->f("type_id"))."';";
				$prijs["wederverkoop"] = $this->query_minimum_price($db->f("type_id"), false, $db->f( "maxaantalpersonen" ), $db->f( "wzt" ), $query);


				// wederverkoop-prijs intern bepalen
				$query = "SELECT tr.wederverkoop_verkoopprijs AS prijs, tr.week, sz.seizoen_id FROM accommodatie a, type t, tarief tr, seizoen sz WHERE (a.toonper=1 OR a.toonper=3) AND tr.type_id=t.type_id AND tr.seizoen_id=sz.seizoen_id AND tr.blokkeren_wederverkoop=0 AND tr.week>".time()." AND sz.tonen>=3 AND tr.beschikbaar=1 AND (tr.bruto>0 OR tr.c_bruto>0 OR tr.arrangementsprijs>0) AND t.accommodatie_id=a.accommodatie_id AND a.tonen=1 AND a.tonenzoekformulier=1 AND t.tonen=1 AND t.tonenzoekformulier=1 AND t.type_id='".intval($db->f("type_id"))."';";
				$prijs["wederverkoop_intern"] = $this->query_minimum_price($db->f("type_id"), false, $db->f( "maxaantalpersonen" ), $db->f( "wzt" ), $query);

			}

			$update.=", prijs='".addslashes($prijs["normaal"])."'";
			$update.=", prijs_intern='".addslashes($prijs["intern"])."'";
			$update.=", prijs_wederverkoop='".addslashes($prijs["wederverkoop"])."'";
			$update.=", prijs_wederverkoop_intern='".addslashes($prijs["wederverkoop_intern"])."'";
			$update.=", prijs_nieuw_seizoen='".addslashes($prijs["normaal_nieuw_seizoen"])."'";
			$update.=", wis=0";

			$update=substr($update,2);
			$db2->query("INSERT INTO cache_vanafprijs_incl_bkk_type SET type_id='".intval($db->f("type_id"))."', ".$update.", adddatetime=NOW(), editdatetime=NOW() ON DUPLICATE KEY UPDATE ".$update.", editdatetime=NOW();");

		}
		$db->query("DELETE FROM cache_vanafprijs_incl_bkk_type WHERE wis=1 AND type_id IN (".$type_ids.");");
		$db->query("UPDATE type SET vanafprijs_bereken_opnieuw=NULL, vanafprijs_berekend=NOW() WHERE type_id IN (".$type_ids.");");

	}
}

