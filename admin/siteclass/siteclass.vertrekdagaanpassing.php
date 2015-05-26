<?php

/**
 * class to get the changed arrival dates (vertrekdagaanpassing)
 *
 * @author Jeroen Boschman (jeroen@webtastic.nl)
 * @since  2015-05-26 14:00
*/

class vertrekdagaanpassing
{

	/**
	 * get the vertrekdagaanpassing for a specific type from the database
	 * and store this data in
	 * - $this->info (general textual info regaring the vertrekdagaanpassing)
	 * - $this->dates (the actual dates that have a vertrekdagaanpassing)
	 *
	 * @param integer type_id of the accommodation
	 * @return void
	 */
	function __construct($type_id)
	{

		$db = new DB_sql;

		$this->config = new Configuration;

		//
		// Afwijkende vertrekdag (=aankomst) (bepaald per seizoen per accommodatie)
		//
		$db->query("SELECT v.naam, v.toelichting".$this->config->ttv." AS toelichting, v.vertrekdagtype_id, v.soort, v.afwijking, UNIX_TIMESTAMP(s.begin) AS begin, UNIX_TIMESTAMP(s.eind) AS eind, asz.seizoen_id FROM accommodatie a, vertrekdagtype v, type t, seizoen s, accommodatie_seizoen asz WHERE a.accommodatie_id=asz.accommodatie_id AND a.accommodatie_id=t.accommodatie_id AND t.type_id='".intval($type_id)."' AND asz.vertrekdagtype_id=v.vertrekdagtype_id AND asz.seizoen_id=s.seizoen_id;");
		while($db->next_record()) {
			$this->info[$db->f("seizoen_id")]["naam"]=$db->f("naam");
			$this->info[$db->f("seizoen_id")]["toelichting"]=$db->f("toelichting");
			$this->info[$db->f("seizoen_id")]["id"]=$db->f("vertrekdagtype_id");
			if($db->f("soort")==2) {
				# Optie B - zondag-zondag
				$week=$db->f("begin");
				$eind=mktime(0,0,0,date("m",$db->f("eind")),date("d",$db->f("eind"))+7,date("Y",$db->f("eind")));
				while($week<=$eind) {
					$this->dates[$db->f("seizoen_id")][date("dm",$week)]=1;
					$week=mktime(0,0,0,date("m",$week),date("d",$week)+7,date("Y",$week));
				}
			} elseif($db->f("soort")==1 and $db->f("afwijking")) {
				# Unieke afwijking
				$data=@split("\n",$db->f("afwijking"));
				while(list($key,$value)=@each($data)) {
					if(ereg("^([0-9]{4}) (.[0-9])$",trim($value),$regs)) {
						$this->dates[$db->f("seizoen_id")][$regs[1]]=intval($regs[2]);
					}
				}
			}
		}
	}

	/**
	 * get the general textual info ($this->info)
	 *
	 * @return array (naam, toelichting, id)
	 */
	public function get_info() {
		return $this->info;
	}

	/**
	 * get the actual dates ($this->dates)
	 *
	 * @return array
	 */
	public function get_dates() {
		return $this->dates;
	}

}

