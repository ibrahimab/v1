<?php

/**
 * class to get the changed arrival dates and number of nights (vertrekdagaanpassing)
 *
 * @author Jeroen Boschman (jeroen@webtastic.nl)
 * @since  2015-05-26 14:00
*/

class vertrekdagaanpassing
{

	/**
	 * get the vertrekdagaanpassing for one or more types from the database
	 * and store this data in
	 * - $this->info (general textual info regaring the vertrekdagaanpassing)
	 * - $this->dates (the actual dates that have a vertrekdagaanpassing)
	 *
	 * @param integer/string type_id of the accommodation, comma separated
	 * @return void
	 */
	function __construct( $type_id )
	{

		$db = new DB_sql;

		$this->config = new Configuration;

		//
		// Afwijkende vertrekdag (=aankomst) (bepaald per seizoen per accommodatie)
		//

		// aankomst_plusmin / vertrek_plusmin
		$db->query("SELECT t.type_id, a.aankomst_plusmin, a.vertrek_plusmin FROM accommodatie a INNER JOIN type t USING (accommodatie_id) WHERE t.type_id IN(".wt_as($type_id).");");
		while( $db->next_record() ) {
			$this->aankomst_plusmin[$db->f( "type_id" )] = $db->f( "aankomst_plusmin" );
			$this->vertrek_plusmin[$db->f( "type_id" )] = $db->f( "vertrek_plusmin" );
		}

		// vertrekdagtype
		$db->query("SELECT t.type_id, v.naam, v.vertrekdagtype_id, v.soort, v.afwijking, UNIX_TIMESTAMP(s.begin) AS begin, UNIX_TIMESTAMP(s.eind) AS eind, asz.seizoen_id, a.aankomst_plusmin, a.vertrek_plusmin FROM accommodatie a, vertrekdagtype v, type t, seizoen s, accommodatie_seizoen asz WHERE a.accommodatie_id=asz.accommodatie_id AND a.accommodatie_id=t.accommodatie_id AND t.type_id IN(".wt_as($type_id).") AND asz.vertrekdagtype_id=v.vertrekdagtype_id AND asz.seizoen_id=s.seizoen_id;");
		while($db->next_record()) {
			$this->info[$db->f( "type_id" )][$db->f("seizoen_id")]["naam"]=$db->f("naam");
			$this->info[$db->f( "type_id" )][$db->f("seizoen_id")]["id"]=$db->f("vertrekdagtype_id");
			if($db->f("soort")==2) {
				// Optie B - zondag-zondag
				$week=$db->f("begin");
				$eind=mktime(0,0,0,date("m",$db->f("eind")),date("d",$db->f("eind"))+7,date("Y",$db->f("eind")));
				while($week<=$eind) {
					$this->dates[$db->f( "type_id" )][$db->f("seizoen_id")][date("dm",$week)]=1;
					$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
				}
			} elseif($db->f("soort")==1 and $db->f("afwijking")) {
				// Unieke afwijking
				$data=@split("\n",$db->f("afwijking"));
				while(list($key,$value)=@each($data)) {
					if(ereg("^([0-9]{4}) (.[0-9])$",trim($value),$regs)) {
						$this->dates[$db->f( "type_id" )][$db->f("seizoen_id")][$regs[1]]=intval($regs[2]);
					}
				}
			}
		}
	}

	/**
	 * get the plus / min value for a specific date, based on arrival (aankomst) or departure (vertrek)
	 *
	 * @param integer type_id
	 * @param integer seizoen_id
	 * @param integer week in unixtime
	 * @param string aankomst or vertrek (arrival or departure)
	 * @return integer
	 */
	private function get_plusmin( $type_id, $seizoen_id, $week, $type="aankomst" )
	{
		$plusmin = 0;
		if( $type=="aankomst" ) {
			$aankomst_vertrek_plusmin = $this->aankomst_plusmin[$type_id];
		} elseif( $type=="vertrek" ) {
			$aankomst_vertrek_plusmin = $this->vertrek_plusmin[$type_id];
		}
		if ( isset($this->dates[$type_id][$seizoen_id][date("dm", $week)] ) or $aankomst_vertrek_plusmin ) {
			$plusmin = $this->dates[$type_id][$seizoen_id][date("dm", $week)];
			$plusmin += $aankomst_vertrek_plusmin;
		}
		return $plusmin;
	}

	/**
	 * get the general textual info ($this->info)
	 *
	 * @return array (naam, id)
	 */
	public function get_info()
	{
		return $this->info;
	}

	/**
	 * get the actual dates ($this->dates)
	 *
	 * @return array
	 */
	public function get_dates()
	{
		return $this->dates;
	}

	/**
	 * convert a Saturday unixtime to the actual arrival date
	 *
	 * @param integer type_id of the accommodation
	 * @param integer seizoen_id
	 * @param integer unixtime of the wanted week
	 * @return integer
	 */
	public function get_arrival_unixtime( $type_id, $seizoen_id, $week )
	{
		$plusmin = $this->get_plusmin( $type_id, $seizoen_id, $week, "aankomst" );
		if ( $plusmin ) {
			$return = mktime( 0, 0, 0, date("m", $week), date("d", $week) + $plusmin, date("Y", $week) );
		} else {
			$return = $week;
		}
		return $return;
	}

	/**
	 * get the number of nights of a specific arrival date
	 *
	 * @param integer type_id of the accommodation
	 * @param integer seizoen_id
	 * @param integer unixtime of the wanted week
	 * @return integer
	 */
	public function get_number_of_nights( $type_id, $seizoen_id, $week )
	{

		$plusmin_arrival = $this->get_plusmin( $type_id, $seizoen_id, $week );

		$departure = mktime( 0, 0, 0, date("m", $week), date("d", $week) + 7, date("Y", $week) );
		$plusmin_departure = $this->get_plusmin( $type_id, $seizoen_id, $departure, "vertrek" );

		$number_of_nights = 7;

		$number_of_nights -= $plusmin_arrival;
		$number_of_nights += $plusmin_departure;

		return $number_of_nights;
	}
}
