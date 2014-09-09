<?php


/**
 * make an exact copy of one database record
 */
class copydatabaserecord {

	function __construct() {

	}

	public function copy_record() {

		$db = new DB_sql;
		$db2 = new DB_sql;

		$db->query( "SELECT * FROM `".$this->table."` WHERE ".$this->key."='".$this->id."';" );
		while ( $db->next_record() ) {

			unset( $setquery );

			foreach ( $db->Record as $key => $value ) {
				if ( $key and !is_int( $key ) and !$this->ignore[$key] ) {

					$this->last_record[$key] = $value;

					if ( $this->append[$key] ) {
						$value .= $this->append[$key];
					}
					if ( isset( $this->change[$key] ) ) {
						$value = $this->change[$key];
					}

					if ( $key=="adddatetime" or $key=="editdatetime" ) {
						$setquery .= ", ".$key."=NOW()";
					} elseif ( $key==$this->key and !isset( $this->change[$key] ) ) {

					} else {
						if ( is_null( $value ) ) {
							$setquery .= ", ".$key."=NULL";
						} else {
							$setquery .= ", ".$key."='".addslashes( $value )."'";
						}
					}
				}
			}

			if ( $setquery ) {
				$db2->query( "INSERT INTO `".$this->table."` SET ".substr( $setquery, 1 ) );
				$this->new_id = $db2->insert_id();
			}
		}
	}

	public function copy_files( $folder, $extension, $source_id, $destination_id ) {
		$files = scandir( $folder );

		foreach ( $files as $key => $value ) {
			if ( $value==$source_id.".".$extension ) {
				// copy file (e.g. 1.jpg)
				copy( $folder.$value , $folder.$destination_id.".".$extension );

				// filesync
				filesync::add_to_filesync_table($folder.$destination_id.".".$extension);

			} elseif ( preg_match( "@^".$source_id."-([0-9]+)\.".$extension."$@", $value, $regs ) ) {
				// copy file (e.g. 1-1.jpg)
				copy( $folder.$value , $folder.$destination_id."-".$regs[1].".".$extension );

				// filesync
				filesync::add_to_filesync_table($folder.$destination_id."-".$regs[1].".".$extension);
			}
		}
	}

	public function copy_accommodatie( $accommodatie_id ) {

		global $vars, $login;
		$db = new DB_sql;
		$db2 = new DB_sql;

		// accommodatie
		$copydatabaserecord = new copydatabaserecord;
		$copydatabaserecord->table = "accommodatie";
		$copydatabaserecord->key = "accommodatie_id";
		$copydatabaserecord->id = $accommodatie_id;
		$copydatabaserecord->append["naam"] = " (kopie)";
		$copydatabaserecord->append["internenaam"] = " (kopie)";
		$copydatabaserecord->change["tonen"] = 0;
		// $copydatabaserecord->ignore["aantekeningen"] = true;
		// $copydatabaserecord->ignore["url_leverancier"] = true;
		$copydatabaserecord->copy_record();

		$this->new_accommodatie_id = $copydatabaserecord->new_id;

		if ( $this->new_accommodatie_id ) {

			// accommodatie_seizoen
			$copydatabaserecord = new copydatabaserecord;
			$copydatabaserecord->table = "accommodatie_seizoen";
			$copydatabaserecord->key = "accommodatie_id";
			$copydatabaserecord->id = $accommodatie_id;
			$copydatabaserecord->change["accommodatie_id"] = $this->new_accommodatie_id;
			$copydatabaserecord->copy_record();

			// optie_accommodatie
			$copydatabaserecord = new copydatabaserecord;
			$copydatabaserecord->table = "optie_accommodatie";
			$copydatabaserecord->key = "accommodatie_id";
			$copydatabaserecord->id = $accommodatie_id;
			$copydatabaserecord->change["accommodatie_id"] = $this->new_accommodatie_id;
			$copydatabaserecord->copy_record();

			// bk_accommodatie
			$copydatabaserecord = new copydatabaserecord;
			$copydatabaserecord->table = "bk_accommodatie";
			$copydatabaserecord->key = "accommodatie_id";
			$copydatabaserecord->id = $accommodatie_id;
			$copydatabaserecord->change["accommodatie_id"] = $this->new_accommodatie_id;
			$copydatabaserecord->copy_record();

			// types below accommodation
			$db->query( "SELECT type_id FROM type WHERE accommodatie_id='".intval( $accommodatie_id )."' ORDER BY type_id;" );
			while ( $db->next_record() ) {
				$this->copy_type( $db->f( "type_id" ) );
			}

			// copy images
			$this->copy_files( $vars["unixdir"]."pic/cms/accommodaties/", "jpg", $accommodatie_id, $this->new_accommodatie_id );
			$this->copy_files( $vars["unixdir"]."pic/cms/accommodaties_aanvullend/", "jpg", $accommodatie_id, $this->new_accommodatie_id );
			$this->copy_files( $vars["unixdir"]."pic/cms/accommodaties_aanvullend_breed/", "jpg", $accommodatie_id, $this->new_accommodatie_id );
			$this->copy_files( $vars["unixdir"]."pic/cms/accommodaties_aanvullend_onderaan/", "jpg", $accommodatie_id, $this->new_accommodatie_id );
			$this->copy_files( $vars["unixdir"]."pic/cms/accommodaties_aanvullend_tn/", "jpg", $accommodatie_id, $this->new_accommodatie_id );
			$this->copy_files( $vars["unixdir"]."pic/cms/accommodaties_tn/", "jpg", $accommodatie_id, $this->new_accommodatie_id );
			$this->copy_files( $vars["unixdir"]."pic/cms/hoofdfoto_accommodatie/", "jpg", $accommodatie_id, $this->new_accommodatie_id );

			// copy pdf's
			$this->copy_files( $vars["unixdir"]."pdf/accommodatie_aanvullende_informatie/", "pdf", $accommodatie_id, $this->new_accommodatie_id );

			// log copy-action
			$db->query("SELECT naam, begincode, type_id, wzt FROM view_accommodatie WHERE accommodatie_id='".intval($accommodatie_id)."';");
			if($db->next_record()) {
				$db2->query("INSERT INTO cmslog SET user_id='".intval($login->user_id)."', cms_id=1, cms_name='".($db->f("wzt")==1 ? "winter" : "zomer")."accommodatie', table_name='accommodatie', specialtype=4, specialtext='accommodatie gekopieerd van \'".addslashes($db->f("naam"))."\'', record_id='".intval($this->new_accommodatie_id)."', savedate=NOW();");
			}
		}
	}

	public function copy_type( $type_id ) {

		global $vars, $login;

		$db = new DB_sql;
		$db2 = new DB_sql;

		// type
		$copydatabaserecord = new copydatabaserecord;
		$copydatabaserecord->table = "type";
		$copydatabaserecord->key = "type_id";
		$copydatabaserecord->id = $type_id;
		if ( $this->new_accommodatie_id ) {
			$copydatabaserecord->change["accommodatie_id"] = $this->new_accommodatie_id;
		} else {
			$copydatabaserecord->append["naam"] = " (kopie)";
			$copydatabaserecord->change["tonen"] = 0;
		}
		$copydatabaserecord->ignore["zomerwinterkoppeling_accommodatie_id"] = true;
		$copydatabaserecord->ignore["oude_accommodatie_id"] = true;
		$copydatabaserecord->ignore["leverancierscode"] = true;
		$copydatabaserecord->ignore["leverancierscode_negeertarief"] = true;
		$copydatabaserecord->ignore["voorraad_gekoppeld_type_id"] = true;
		$copydatabaserecord->copy_record();
		$this->new_type_id = $copydatabaserecord->new_id;

		// type_seizoen
		$copydatabaserecord = new copydatabaserecord;
		$copydatabaserecord->table = "type_seizoen";
		$copydatabaserecord->key = "type_id";
		$copydatabaserecord->id = $type_id;
		$copydatabaserecord->change["type_id"] = $this->new_type_id;
		$copydatabaserecord->copy_record();

		// bk_type
		$copydatabaserecord = new copydatabaserecord;
		$copydatabaserecord->table = "bk_type";
		$copydatabaserecord->key = "type_id";
		$copydatabaserecord->id = $type_id;
		$copydatabaserecord->change["type_id"] = $this->new_type_id;
		$copydatabaserecord->copy_record();

		$this->copy_files( $vars["unixdir"]."pic/cms/types/", "jpg", $type_id, $this->new_type_id );
		$this->copy_files( $vars["unixdir"]."pic/cms/types_breed/", "jpg", $type_id, $this->new_type_id );
		$this->copy_files( $vars["unixdir"]."pic/cms/types_specifiek/", "jpg", $type_id, $this->new_type_id );
		$this->copy_files( $vars["unixdir"]."pic/cms/types_specifiek_tn/", "jpg", $type_id, $this->new_type_id );
		$this->copy_files( $vars["unixdir"]."pic/cms/hoofdfoto_type/", "jpg", $type_id, $this->new_type_id );

		// log copy-action
		$db->query("SELECT begincode, type_id, wzt FROM view_accommodatie WHERE type_id='".intval($type_id)."';");
		if($db->next_record()) {
			$db2->query("INSERT INTO cmslog SET user_id='".intval($login->user_id)."', cms_id=2, cms_name='".($db->f("wzt")==1 ? "winter" : "zomer")."type', table_name='type', specialtype=4, specialtext='type gekopieerd van ".$db->f("begincode").$db->f("type_id")."', record_id='".intval($this->new_type_id)."', savedate=NOW();");
		}
	}
}

?>