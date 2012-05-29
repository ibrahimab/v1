<?php




# 6 december, gebleven bij:
# - productgroepen
# - merken
# - series
# - bestellingen
#


#
# 21 februari, gebleven bij:
# - delete-icon bij een show-pagina
# - exposities
# - transactie toevoegen vanuit kunstwerken.php
# - "terug naar" in geval van "back_link"

#
# NOG DOEN:
# - Maxsize textfield op basis van database-gegevens
# - verschil tussen persoon en bedrijf bij <SELECT>-invulvelden
# - $_GET[$counter."mt"] weghalen (en controleren of het bij alle sites goed werkt)
#
#
#
#
# Nieuwe form-class
# wt_naam
# koppeltabel (n:n)
# tabellen met primary key zonder id/autoincrement
# kleine afbeeldingen in database?
# wherequery
# tabel met meerdere primary keys
# date bij show_rightcell (bij end_decleration)
# bij list: 
#	- markeren voor wissen (met checkbox)
#
# bij edit:
#	- check op correct INSERTen/UPDATEen (indien primkey al bestaat)
#

#
# UITLEG ALLE _FIELD-FUNCTIONS
#
# ===========
# list_field:
# ===========
# options:
#	- selection 
#
#
#
# Uitleg _GET-fields
# flqs = former list query string
# flf = former list file
# fsqs = former show query string
# 
#

class cms2 {

	function cms2() {
		$this->settings["language"]="nl";
		if($_GET["bc"]==-1) {
			unset($_SESSION["CMS2"]["back"],$_GET["bc"]);
		}
		
		# Messages
		$this->settings["message"]["nognietaanweziginsysteem"]["nl"]="er zijn nog geen _VALveldnaam_ aanwezig in het systeem";
		$this->settings["message"]["nognietaanweziginsysteem"]["en"]="no _VALveldnaam_ in the system yet";

		$this->settings["message"]["verplichtveldnognietbeschikbaar"]["nl"]="verplicht veld '_VALveldnaam_' is nog niet beschikbaar";
		$this->settings["message"]["verplichtveldnognietbeschikbaar"]["en"]="Compulsory field '_VALveldnaam_' is not available yet";

		$this->settings["message"]["veldnaamopenen"]["nl"]="_VALveldnaam_ openen";
		$this->settings["message"]["veldnaamopenen"]["en"]="Open _VALveldnaam_";

		$this->settings["message"]["veldnaambewerken"]["nl"]="_VALveldnaam_ bewerken";
		$this->settings["message"]["veldnaambewerken"]["en"]="Edit _VALveldnaam_";

		$this->settings["message"]["veldnaamprinten"]["nl"]="_VALveldnaam_ printen";
		$this->settings["message"]["veldnaamprinten"]["en"]="Print _VALveldnaam_";

		$this->settings["message"]["veldnaamwissen"]["nl"]="_VALveldnaam_ wissen";
		$this->settings["message"]["veldnaamwissen"]["en"]="Delete _VALveldnaam_";

		$this->settings["message"]["foutbijhetwissenvan"]["nl"]="Fout bij het wissen van";
		$this->settings["message"]["foutbijhetwissenvan"]["en"]="Eror deleting";

		$this->settings["message"]["terugnaarvorigepagina"]["nl"]="Terug naar vorige pagina";
		$this->settings["message"]["terugnaarvorigepagina"]["en"]="Back to the previous page";

		$this->settings["message"]["erzijngeenveldnaamgekoppeld"]["nl"]="Er zijn geen _VALveldnaam_ gekoppeld.";
		$this->settings["message"]["erzijngeenveldnaamgekoppeld"]["en"]="There are no _VALveldnaam_ linked.";
		
		$this->settings["message"]["erzijngeenveldnaamaanwezig"]["nl"]="Er zijn geen _VALveldnaam_ aanwezig.";
		$this->settings["message"]["erzijngeenveldnaamaanwezig"]["en"]="There are no _VALveldnaam_.";

		$this->settings["message"]["selecteereentoetevoegenveldnaam"]["nl"]="selecteer een toe te voegen _VALveldnaam_";
		$this->settings["message"]["selecteereentoetevoegenveldnaam"]["en"]="select a _VALveldnaam_ to add";

		$this->settings["message"]["noggeenveldnaaminhetsysteem"]["nl"]="nog geen _VALveldnaam_ in het systeem";
		$this->settings["message"]["noggeenveldnaaminhetsysteem"]["en"]="no _VALveldnaam_ in the system yet";

		$this->settings["message"]["veldnaamtoevoegen"]["nl"]="_VALveldnaam_ toevoegen";
		$this->settings["message"]["veldnaamtoevoegen"]["en"]="Add _VALveldnaam_";

		$this->settings["message"]["nieuwbestand"]["nl"]="nieuw bestand";
		$this->settings["message"]["nieuwbestand"]["en"]="new file";

		$this->settings["message"]["bestand"]["nl"]="bestand";
		$this->settings["message"]["bestand"]["en"]="file";

		$this->settings["message"]["bestandgewist"]["nl"]="bestand gewist";
		$this->settings["message"]["bestandgewist"]["en"]="file deleted";

		$this->settings["message"]["bestandgewistenvervangen"]["nl"]="bestand gewist en vervangen door nieuw bestand";
		$this->settings["message"]["bestandgewistenvervangen"]["en"]="file deleted and replaced by new file";

		$this->settings["message"]["toevoegen"]["nl"]="TOEVOEGEN";
		$this->settings["message"]["toevoegen"]["en"]="ADD";

		$this->settings["message"]["opslaan"]["nl"]="OPSLAAN";
		$this->settings["message"]["opslaan"]["en"]="SAVE";

		$this->settings["message"]["nieuw"]["nl"]="Nieuw";
		$this->settings["message"]["nieuw"]["en"]="New";

		$this->settings["message"]["herhaal"]["nl"]="Herhaal";
		$this->settings["message"]["herhaal"]["en"]="Repeat";

		$this->settings["message"]["niethetzelfde"]["nl"]="niet hetzelfde";
		$this->settings["message"]["niethetzelfde"]["en"]="not equal";
		
		$this->settings["message"]["veldnaamtoevoegennietmogelijk"]["nl"]="_VALveldnaam_ toevoegen niet mogelijk:";
		$this->settings["message"]["veldnaamtoevoegennietmogelijk"]["en"]="not possible to add _VALveldnaam_:";

		$this->settings["message"]["ja"]["nl"]="ja";
		$this->settings["message"]["ja"]["en"]="yes";

		$this->settings["message"]["nee"]["nl"]="nee";
		$this->settings["message"]["nee"]["en"]="no";

		$this->settings["message"]["terugnaarveldnaam"]["nl"]="Terug naar _VALveldnaam_";
		$this->settings["message"]["terugnaarveldnaam"]["en"]="Back to _VALveldnaam_";

		$this->settings["message"]["overzichtveldnaam"]["nl"]="overzicht _VALveldnaam_";
		$this->settings["message"]["overzichtveldnaam"]["en"]="overview _VALveldnaam_";

		$this->settings["message"]["bewerken"]["nl"]="Bewerken";
		$this->settings["message"]["bewerken"]["en"]="Edit";

		$this->settings["message"]["printen"]["nl"]="Printen";
		$this->settings["message"]["printen"]["en"]="Print";

		$this->settings["message"]["record_niet_gevonden"]["nl"]="Record niet gevonden";
		$this->settings["message"]["record_niet_gevonden"]["en"]="Record not found";

		$this->settings["message"]["record_toegevoegd"]["nl"]="Nieuw record aangemaakt";
		$this->settings["message"]["record_toegevoegd"]["en"]="New record created";

		$this->settings["message"]["aangevinkterecordswissen"]["nl"]="Aangevinkte _VALveldnaam_ wissen";
		$this->settings["message"]["aangevinkterecordswissen"]["en"]="Delete checkbox _VALveldnaam_";

#		$this->settings["message"][""]["nl"]="";
#		$this->settings["message"][""]["en"]="";
#		$this->settings["message"][""]["nl"]="";
#		$this->settings["message"][""]["en"]="";
		return true;
	}

	function init($counter) {
		if(!isset($this->settings[$counter]["datetime"]["edit"])) $this->settings[$counter]["datetime"]["edit"]=true;
		if(!isset($this->settings[$counter]["datetime"]["add"])) $this->settings[$counter]["datetime"]["add"]=true;
		if(!isset($this->settings[$counter]["list"]["edit_icon"])) $this->settings[$counter]["list"]["edit_icon"]=true;
		if(!isset($this->settings[$counter]["list"]["delete_icon"])) $this->settings[$counter]["list"]["delete_icon"]=true;
		if(!isset($this->settings[$counter]["list"]["delete_checkbox"])) $this->settings[$counter]["list"]["delete_checkbox"]=false;
		if(!isset($this->settings[$counter]["list"]["show_icon"])) $this->settings[$counter]["list"]["show_icon"]=false;
		if(!isset($this->settings[$counter]["list"]["print_icon"])) $this->settings[$counter]["list"]["print_icon"]=false;
		if(!isset($this->settings[$counter]["list"]["add_link"])) $this->settings[$counter]["list"]["add_link"]=true;

		if(!isset($this->settings[$counter]["show"]["edit_icon"])) $this->settings[$counter]["show"]["edit_icon"]=true;
		if(!isset($this->settings[$counter]["show"]["delete_icon"])) $this->settings[$counter]["show"]["delete_icon"]=false; # DOET HET NOG NIET
		if(!isset($this->settings[$counter]["show"]["print_icon"])) $this->settings[$counter]["show"]["print_icon"]=false;
		if(!isset($this->settings[$counter]["show"]["add_link"])) $this->settings[$counter]["show"]["add_link"]=false; # DOET HET NOG NIET

		if(!isset($this->settings[$counter]["log"]["active"])) $this->settings[$counter]["log"]["active"]=false;
		
		$this->init[$counter]=true;
		return true;
	}

	function message($title,$html=true,$value_array="") {
		$return=$this->settings["message"][$title][$this->settings["language"]];
		while(list($key,$value)=@each($value_array)) {
			$return=ereg_replace("_VAL".$key."_",$value,$return);
		}
		if($html) {
			$return=htmlentities($return,ENT_QUOTES,"iso-8859-15");
		}
		@reset($value_array);
		while(list($key,$value)=@each($value_array)) {
			$return=ereg_replace("_HTML".$key."_",$value,$return);
		}
		return $return;
	}

	function db_field($counter,$type,$id,$field="",$options="") {
		if(!$this->init[$counter]) $this->init($counter);
		
		$this->db[$counter]["type"][$id]=$type;
		if($field) {
			$this->db[$counter]["field"][$id]=$field;
		} else {
			$this->db[$counter]["field"][$id]=$id;
		}
		if($options) $this->db[$counter]["options"][$id]=$options;

		# Gegevens uit andere tabel
		if($options["othertable"] and !$options["selection"]) {
			if($options["otherfield"]=="wt_naam" or $options["otherfield"]=="wt_naam2") {
				if($options["encode_otherfield"]) {
					$options["otherfield"]="DECODE(achternaam,'".md5($options["encode_otherfield"])."') AS achternaam, DECODE(voornaam,'".md5($options["encode_otherfield"])."') AS voornaam, DECODE(tussenvoegsel,'".md5($options["encode_otherfield"])."') AS tussenvoegsel";
					if(!$options["order"]) $options["order"]="achternaam, voornaam, tussenvoegsel";
				} else {
					$options["otherfield"]="achternaam, voornaam, tussenvoegsel";
				}
			} elseif($options["otherfield"]=="wt_naam3" or $options["otherfield"]=="wt_naam4") {
				if($options["encode_otherfield"]) {
					$options["otherfield"]="DECODE(achternaam,'".md5($options["encode_otherfield"])."') AS achternaam, DECODE(voorletters,'".md5($options["encode_otherfield"])."') AS voorletters, DECODE(tussenvoegsel,'".md5($options["encode_otherfield"])."') AS tussenvoegsel";
					if(!$options["order"]) $options["order"]="achternaam, voorletters, tussenvoegsel";
				} else {
					$options["otherfield"]="achternaam, voorletters, tussenvoegsel";
				}
			} elseif($options["encode_otherfield"]) {
				$options["otherfield"]="DECODE(".$options["otherfield"].",'".md5($options["encode_otherfield"])."') AS ".$options["otherfield"];
			}
			$db=new DB_sql;
			$db->query("SELECT ".$options["otherkeyfield"].",".$options["otherfield"]." FROM ".($this->db[$options["othertable"]]["maintable"] ? $this->db[$options["othertable"]]["maintable"] : $options["othertable"]).($options["otherwhere"] ? " WHERE ".$options["otherwhere"] : "")." ORDER BY ".($options["order"] ? $options["order"] : $options["otherfield"]).";");
			if($db->num_rows()) {
				while($db->next_record()) {
					if($this->db[$counter]["options"][$id]["otherfield"]=="wt_naam") {
						$this->db[$counter]["options"][$id]["selection"][$db->f($options["otherkeyfield"])]=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"));
						$this->db[$counter]["options"][$id]["selection_sort"][$db->f($options["otherkeyfield"])]=$db->f("achternaam")." ".$db->f("voornaam")." ".$db->f("tussenvoegsel");
					} elseif($this->db[$counter]["options"][$id]["otherfield"]=="wt_naam2") {
						$this->db[$counter]["options"][$id]["selection"][$db->f($options["otherkeyfield"])]=wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"),true);
					} elseif($this->db[$counter]["options"][$id]["otherfield"]=="wt_naam3") {
						$this->db[$counter]["options"][$id]["selection"][$db->f($options["otherkeyfield"])]=wt_naam($db->f("voorletters"),$db->f("tussenvoegsel"),$db->f("achternaam"),false,true);
					} elseif($this->db[$counter]["options"][$id]["otherfield"]=="wt_naam4") {
						$this->db[$counter]["options"][$id]["selection"][$db->f($options["otherkeyfield"])]=wt_naam($db->f("voorletters"),$db->f("tussenvoegsel"),$db->f("achternaam"),true,true);
					} else {
						$this->db[$counter]["options"][$id]["selection"][$db->f($options["otherkeyfield"])]=$db->f($options["otherfield"]);
					}
				}
			} else {
				if(ereg("^[0-9]+$",$options["othertable"])) {
					# othertable is in cijfervorm opgegeven
					$this->add_error_temp[$counter][$id]=$this->message("nognietaanweziginsysteem",true,array("veldnaam"=>$this->settings[$options["othertable"]]["types"]));
				} else {
					$this->add_error_temp[$counter][$id]=$this->message("verplichtveldnognietbeschikbaar",true,array("veldnaam"=>"[field_".$id."]"));
				}
			}
		} elseif(($type=="select" or $type=="radio") and !is_array($options["selection"])) {
			$this->add_error_temp[$counter][$id]=$this->message("verplichtveldnognietbeschikbaar",true,array("veldnaam"=>"[field_".$id."]"));
		}
	}
	
	function list_field($counter,$id,$title="",$options="",$layout="") {
		if($title) {
			$this->list[$counter]["title"][$id]=$title;
		} else {
			$this->list[$counter]["title"][$id]=ucfirst($id);
		}
		if($options) $this->list[$counter]["options"][$id]=$options;
		if($layout) $this->list[$counter]["layout"][$id]=$layout;
	}
	
	function edit_field($counter,$obl,$id,$title="",$prevalue="",$options="",$layout="") {
		if($id=="htmlcol") {
			$this->htmlcol_edit_counter[$counter]++;
			$id="htmlcol_".$this->htmlcol_edit_counter[$counter];
		} elseif($id=="htmlrow") {
			$this->htmlrow_edit_counter[$counter]++;
			$id="htmlrow_".$this->htmlrow_edit_counter[$counter];
		}

		$this->edit[$counter]["obl"][$id]=$obl;
		
		# add-error wissen indien dit veld niet verplicht is
#		if($this->add_error[$counter][$id] and !$obl) unset($this->add_error[$counter][$id]);
		
		if($title) {
			$this->edit[$counter]["title"][$id]=$title;
		} else {
			$this->edit[$counter]["title"][$id]=ucfirst($id);
		}
		if($prevalue) $this->edit[$counter]["prevalue"][$id]=$prevalue;
		if($options) $this->edit[$counter]["options"][$id]=$options;
		if($layout) $this->edit[$counter]["layout"][$id]=$layout;
	}

	function show_field($counter,$id,$title="",$options="",$layout="") {
		if($id=="htmlcol") {
			$this->htmlcol_show_counter[$counter]++;
			$id="htmlcol_".$this->htmlcol_show_counter[$counter];
		} elseif($id=="htmlrow") {
			$this->htmlrow_show_counter[$counter]++;
			$id="htmlrow_".$this->htmlrow_show_counter[$counter];
		}
		if($title) {
			$this->show[$counter]["title"][$id]=$title;
		} else {
			$this->show[$counter]["title"][$id]=ucfirst($id);
		}
		if($options) $this->show[$counter]["options"][$id]=$options;
		if($layout) $this->show[$counter]["layout"][$id]=$layout;
	}

	function get_query($counter,$type="SELECT",$select="",$from="",$where="") {
		reset($this->db[$counter]["field"]);
		if($this->db[$counter]["where"]) {
			if($where) $where.=" AND ".$this->db[$counter]["where"]; else $where=$this->db[$counter]["where"];
		}
		$columninfo=$this->get_columninfo($this->db[$counter]["maintable"]);
		while(list($key,$value)=each($this->db[$counter]["field"])) {
			if($this->db[$counter]["type"][$key]<>"picture" and $this->db[$counter]["type"][$key]<>"upload" and !$this->db[$counter]["options"][$key]["notdb"]) {
				if(!$primkey) {
					$primkey=$this->get_primarykey($this->db[$counter]["maintable"],$counter);
#					if($select) $select.=", ".$primkey["all"].", ".$primkey["concat"]; else $select=$primkey["all"].", ".$primkey["concat"];
					$select.=", ".$primkey["all"].", ".$primkey["concat"];
					if($_GET[$counter."k0"] and !$this->delete_error[$counter]) {
						# Niet alle records, maar slechts 1 record opvragen (in geval van delete-error alle records tonen (bij list))
						if($where) $where.=" AND ".$primkey["where"]; else $where=$primkey["where"];
					}
				}
				if(!$temp["tables"][$this->db[$counter]["maintable"]]) {
					$temp["tables"][$this->db[$counter]["maintable"]]=true;
					if($from) $from.=", ".$this->db[$counter]["maintable"]; else $from=$this->db[$counter]["maintable"];
				}
#				if($select) $select.=", ".$this->db[$counter]["maintable"].".".$value." AS ".$key; else $select=$this->db[$counter]["maintable"].".".$value." AS ".$key;
				if($this->db[$counter]["options"][$key]["encode"]) {
					$select.=", DECODE(".$this->db[$counter]["maintable"].".".$value.",'".md5($this->db[$counter]["options"][$key]["encode"])."') AS ".$key;
				} else {
					if($columninfo[$value]["type"]=="date" or $columninfo[$value]["type"]=="datetime") {
						# DATE en DATETIME-velden opvragen in UNIX_TIMESTAMP
						$select.=", UNIX_TIMESTAMP(".$this->db[$counter]["maintable"].".".$value.") AS ".$key;
					} else {
						$select.=", ".$this->db[$counter]["maintable"].".".$value." AS ".$key;
					}
				}
			}
		}
		if(substr($select,0,2)==", ") {
			$select=substr($select,2);
		}
		
		if($type=="DELETE") {
			$return="DELETE FROM ".$from.($where ? " WHERE ".$where : "").";";
		} elseif($select and $from) {
			if($this->db[$counter]["othertables"]) {
				$from.=", ".$this->db[$counter]["othertables"];
			}
			$return="SELECT ".$select." FROM ".$from.($where ? " WHERE ".$where : "").";";
		}
		if($return) {
			return $return;
		} else {
			return false;
		}
	}

	function get_primarykey($table,$counter) {
		$db=new DB_sql;
		$db->query("SHOW INDEX FROM ".$table.";");
		$reccounter=0;
		while($db->next_record()) {
			if($db->f("Key_name")=="PRIMARY") {
				$return[$reccounter]=$db->f("Column_name");
				if($all) $all.=", ".$table.".".$db->f("Column_name")." AS ".$counter."k".$reccounter; else $all=$table.".".$db->f("Column_name")." AS ".$counter."k".$reccounter;
				if($concat) $concat.=",".$table.".".$db->f("Column_name").",'-'"; else $concat="CONCAT(".$table.".".$db->f("Column_name").",'-'";
				$array[$reccounter]=$table.".".$db->f("Column_name");
				$reccounter++;
			}
		}
		if($concat) {
			$return["concat"]=$concat.") AS primkey";
		}
		if($array) {
			$return["array"]=$array;
		}
		$return["all"]=$all;
		while(list($key,$value)=each($return["array"])) {
			if($where) $where.=" AND ".$value."='".addslashes($_GET[$counter."k".$key])."'"; else $where=$value."='".addslashes($_GET[$counter."k".$key])."'";;
		}
		$return["where"]=$where;
		
		return $return;
	}

	function get_columninfo($table) {
		# Veld-types uit database halen
		if(!$this->get_columninfo[$table]) {
			$db=new DB_sql;
			$db->query("SHOW COLUMNS FROM ".addslashes($table).";");
			while($db->next_record()) {
				unset($id,$maxlength);
				if(ereg("(.*)\(([0-9]+)\)",$db->f("Type"),$regs)) {
					$maxlength=$regs[2];
					$type=$regs[1];
				} else {
					unset($maxlength);
					$type=$db->f("Type");
				}
				$return[$db->f("Field")]["type"]=$type;
				$return[$db->f("Field")]["maxlength"]=$maxlength;
				$return[$db->f("Field")]["full"]=$db->f("Type");
			}
			$this->get_columninfo[$table]=true;
			return $return;
		}
	}

	function set_delete_init($counter,$deletequery=true) {
		if($counter and $_GET["delete"]==$counter and $_GET["confirmed"] and !$this->delete_error[$counter] and ($this->settings[$counter]["list"]["delete_icon"] or $this->settings[$counter]["show"]["delete_icon"])) {
			# Wissen na aanklikken delete-icon
			if(!$this->set_delete_init[$counter]) {
				$db=new DB_sql;

				# Eerst een SELECT-query om de key op te vragen
				$query=$this->get_query($counter,"SELECT");
				$db->query($query);
				if($db->num_rows()==1 and $db->next_record()) {
					$this->delete_key[$counter]=$db->f($counter."k0");
					if($deletequery) {
						# Dan deleten

						# Eventuele afbeeldingen wissen
						reset($this->db[$counter]["field"]);
						while(list($key,$value)=each($this->db[$counter]["field"])) {
							if($this->db[$counter]["type"][$key]=="picture" or $this->db[$counter]["type"][$key]=="upload") {
								$file=$this->db[$counter]["options"][$key]["savelocation"].$this->delete_key[$counter].".".$this->db[$counter]["options"][$key]["filetype"];
								if(file_exists($file)) {
									unlink($file);
								}
							}
						}
								
						# Connectwidth_table-record(s) deleten/wissen
						# NOG BOUWEN!!
						
						$query=$this->get_query($counter,"DELETE");
						$db->query($query);
					}
					$this->set_delete_init[$counter]=true;
					return true;
				}
			} else return false;
		} elseif($counter and $_POST["delete_checkbox_filled"][$counter]==1 and !$this->delete_error[$counter] and $this->settings[$counter]["list"]["delete_checkbox"]) {
			# Wissen na aanklikken checkboxes
			if(!$this->set_delete_init[$counter]) {
			
				if(is_array($_POST["delete_checkbox"][$counter])) {
					$db=new DB_sql;
					$primkey=$this->get_primarykey($this->db[$counter]["maintable"],$counter);

					reset($_POST["delete_checkbox"][$counter]);
					unset($delete_inquery);
					while(list($key,$value)=each($_POST["delete_checkbox"][$counter])) {
						if($value==1) {
							unset($query);
							$dbkeys=split("-",$key);
							if(is_array($dbkeys)) {
								while(list($key2,$value2)=each($dbkeys)) {
									if(!$query) $query="DELETE FROM ".$this->db[$counter]["maintable"]." WHERE 1=1";
									if($primkey[$key2]) {
										$query.=" AND ".$primkey[$key2]."='".addslashes($value2)."'";
									}
								}
								$query.=";";
							}
							$db->query($query);
#							echo $query."<br>";
						}
					}
				}
				$this->set_delete_init[$counter]=true;
				return true;
			}
		} else return false;
	}

	function set_delete($counter) {
		$this->set_delete_init($counter);
		if($counter and $_GET["delete"]==$counter and $_GET["confirmed"] and !$this->delete_error[$counter]) {
			header("Location: ".$this->go_to($counter));
			exit;
		} elseif($counter and $_POST["delete_checkbox_filled"][$counter]==1 and !$this->delete_error[$counter]) {
			header("Location: ".$_SERVER["REQUEST_URI"]);
			exit;
		}
	}
	
	function delete_error($counter,$error) {
		if(!$this->delete_error_init) {
			$this->delete_error_init=true;
			reset($_GET);
			while(list($key,$value)=each($_GET)) {
				if(ereg("^(".$counter."k[0-9]+)$",$key,$regs)) {
					$this->delete_key.=$_GET[$regs[1]]."-";
					$this->not_in_query_string[]=$regs[1];
				}
			}
		}
		$this->delete_error[$counter][]=$error;
	}
	
	function inquery($query) {
		if($query) {
			$db=new DB_sql;
			$db->query($query);
			while($db->next_record()) {
				if($return) $return.=",".$db->f("delkey"); else $return=$db->f("delkey");
			}
			return $return;
		}
	}
	
	function set_list($counter) {
		if($_POST["addlink"][$counter]) {
			# Toevoegen van record d.m.v. pulldown
			reset($this->db[$counter]["type"]);
			list($key,$value)=each($this->db[$counter]["field"]);
			$db=new DB_sql;
			$db->query("INSERT INTO ".$this->db[$counter]["maintable"]." SET ".$value."='".addslashes($_POST["addlink"][$counter])."'".($this->db[$counter]["set"] ? ", ".$this->db[$counter]["set"] : "").";");
#			echo $db->lastquery;
			header("Location: ".$_SERVER["REQUEST_URI"]);
			exit;
		}
	}
	
	function php_self($counter) {
		if($this->settings[$counter]["file"]) {
			$return=$this->settings[$counter]["file"];
			if(is_array($this->settings[$counter]["file_querystring"])) {
				$return.="?";
				reset($this->settings[$counter]["file_querystring"]);
				while(list($key,$value)=each($this->settings[$counter]["file_querystring"])) {
					$return.=$key."=".urlencode($value)."&";
				}
			} else {
				$return.="?";
			}
		} else {
			$return=$_SERVER["PHP_SELF"]."?";
		}
		return $return;
	}
	
	function display_list($counter) {
		$this->back_link($counter);
		$db=new DB_sql;
		if($_GET[$counter."where"]) $where=$_GET[$counter."where"];
		$db->query($this->get_query($counter,"SELECT","","",$where));
		if($db->num_rows()) {
			$this->output[$counter]["list"]["records"]=$db->num_rows();
			$tl=new tablelist;
			$tl->settings["arrowcolor"]="white";
			$tl->settings["systemid"]=$counter;
			
			# maximaal aantal resultaten per pagina instellen?
			if($this->settings[$counter]["list"]["max_results_per_page"]) {
				$tl->settings["max_results_per_page"]=$this->settings[$counter]["list"]["max_results_per_page"];
			}
			if(isset($this->settings[$counter]["list"]["resultpages_top"])) {
				$tl->settings["resultpages_top"]=$this->settings[$counter]["list"]["resultpages_top"];
			}

			# Sorteren van de list
			if($this->list_sort[$counter]) {
				$tl->sort=$this->list_sort[$counter];
			}
			if($this->list_sort_desc[$counter]) $tl->sort_desc=true;
			@reset($this->list[$counter]["title"]);

			# Querystring zonder "&sort=" bepalen
#			reset($_GET);
#			while(list($key,$value)=each($_GET)) {
#				if(!ereg("^sort([0-9]+)$",$key) and !in_array($key,$this->not_in_query_string) and $key<>"bc") {
#					if($querystring_without_sort) $querystring_without_sort.="&".$key."=".urlencode($value); else $querystring_without_sort=$key."=".urlencode($value);
#				}
#			}
#			$url=$counter."mt=".$this->db[$counter]["maintable"]."&bc=".($_GET["bc"]+1).($this->stripped_query_string ? "&".$counter."flqs=".urlencode($this->stripped_query_string)."&".$querystring_without_sort : "");
#			$url=$counter."mt=".$this->db[$counter]["maintable"]."&bc=".($_GET["bc"]+1).($this->stripped_query_string ? "&".$this->stripped_query_string : "");
			$url="bc=".($_GET["bc"]+1).($this->stripped_query_string ? "&".$this->stripped_query_string : "");
			if($this->settings[$counter]["list"]["show_icon"]) {
				if($this->settings[$counter]["list"]["show_link"]) {
				# WERKT NOG NIET!!!
#					$tl->field_show($this->settings[$counter]["list"]["show_link"]."","\"[RECORD]\" openen");
				} else {
					$tl->field_show($this->php_self($counter)."show=".$counter."&".$url."&[ID]",$this->message("veldnaamopenen",false,array("veldnaam"=>ucfirst($this->settings[$counter]["type_single"]))));
				}
			}
			if($this->settings[$counter]["list"]["edit_icon"]) $tl->field_edit($this->php_self($counter)."edit=".$counter."&".$url."&[ID]",$this->message("veldnaambewerken",false,array("veldnaam"=>ucfirst($this->settings[$counter]["type_single"]))));
			if($this->settings[$counter]["list"]["print_icon"]) $tl->field_print($this->php_self($counter)."print=1&".$url."&[ID]",$this->message("veldnaamprinten",false,array("veldnaam"=>ucfirst($this->settings[$counter]["type_single"]))));
			if($this->settings[$counter]["list"]["delete_icon"]) $tl->field_delete($this->php_self($counter)."delete=".$counter."&".$url."&[ID]",$this->message("veldnaamwissen",false,array("veldnaam"=>ucfirst(htmlentities($this->settings[$counter]["type_single"]))." ".($this->settings[$counter]["list"]["delete_question_and"] ? $this->settings[$counter]["list"]["delete_question_and"]." " : "")))."?",$this->message("veldnaamwissen",false,array("veldnaam"=>ucfirst($this->settings[$counter]["type_single"]))));
			if($this->settings[$counter]["list"]["delete_checkbox"]) $tl->field_delete_checkbox();

			while(list($key,$value)=@each($this->list[$counter]["title"])) {
				if($this->list[$counter]["options"][$key]["index_field"]) {
					$tl->field_text($key,$value,"",array("index_field"=>true));
				} else {
					$tl->field_text($key,$value);
				}
			}
			while($db->next_record()) {
				# Volledige primary key naar tablelist sturen				
				unset($endkey,$url_id);
				$keycounter=0;
				while(!$endkey) {
					if($db->f($counter."k".$keycounter)<>"") {
						if($url_id) $url_id.="&".$counter."k".$keycounter."=".$db->f($counter."k".$keycounter); else $url_id=$counter."k".$keycounter."=".$db->f($counter."k".$keycounter);
						$keycounter++;
					} else {
						$endkey=true;
					}
				}
				$tl->add_url_id($db->f("primkey"),$url_id);
				@reset($this->list[$counter]["title"]);
				while(list($key,$value)=@each($this->list[$counter]["title"])) {
					if($key=="wt_naam") {
						# wt_naam (=Voornaam tussenvoegsel Achternaam)
						$tl->add_record($key,$db->f("primkey"),wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam")),$db->f("achternaam").$db->f("voornaam").$db->f("tussenvoegsel"));
					} elseif($key=="wt_naam2") {
						# wt_naam2 (=Achternaam, Voornaam tussenvoegsel)
						$tl->add_record($key,$db->f("primkey"),wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"),true),$db->f("achternaam").$db->f("voornaam").$db->f("tussenvoegsel"));
					} elseif($key=="wt_naam3") {
						# wt_naam3 (= Voorletters tussenvoegsel Achternaam)
						$tl->add_record($key,$db->f("primkey"),wt_naam($db->f("voorletters"),$db->f("tussenvoegsel"),$db->f("achternaam"),false,true),$db->f("achternaam").$db->f("voorletters").$db->f("tussenvoegsel"));
					} elseif($key=="wt_naam4") {
						# wt_naam4 (= Achternaam, Voorletters tussenvoegsel)
						$tl->add_record($key,$db->f("primkey"),wt_naam($db->f("voorletters"),$db->f("tussenvoegsel"),$db->f("achternaam"),true,true),$db->f("achternaam").$db->f("voorletters").$db->f("tussenvoegsel"));
					} elseif($this->db[$counter]["type"][$key]=="date" or $this->db[$counter]["type"][$key]=="datetime") {
						# date-type
						if($db->f($key)) {
							if($this->list[$counter]["options"][$key]["selection"]) {
								$tl->add_record($key,$db->f("primkey"),$this->list[$counter]["options"][$key]["selection"][$db->f($key)],$db->f($key),true);
							} else {
								$tl->add_record($key,$db->f("primkey"),datum($this->list[$counter]["options"][$key]["date_format"],$db->f($key),$this->settings["language"]),$db->f($key),true);
							}
						} else {
							$tl->add_record($key,$db->f("primkey"),"",$db->f($key),true);
						}
					} elseif($this->db[$counter]["type"][$key]=="select" or $this->db[$counter]["type"][$key]=="radio") {
						# select-type en radio-type
						unset($array);
						if($this->list[$counter]["options"][$key]["selection"]) {
							# Indien bij list_field een andere selection-list is opgegeven dan bij db_field
							$array=$this->list[$counter]["options"][$key];
						} else {
							$array=$this->db[$counter]["options"][$key];
						}
						if($this->list[$counter]["options"][$key]["date_format"] and $array["selection"][$db->f($key)]) {
							# Select-field met date-format in list-options
							if($this->list[$counter]["options"][$key]["key_is_date"]) {
								$tl->add_record($key,$db->f("primkey"),datum($this->list[$counter]["options"][$key]["date_format"],$db->f($key),$this->settings["language"]),$array["selection"][$db->f($key)],true);
							} else {
								$tl->add_record($key,$db->f("primkey"),datum($this->list[$counter]["options"][$key]["date_format"],$array["selection"][$db->f($key)],$this->settings["language"]),$array["selection"][$db->f($key)],true);
							}
						} else {
							# Overige select-fields
							if($this->list[$counter]["layout"][$key]["html"]) {
								$html=true;
							} else {
								$html=false;
							}
							if($this->list[$counter]["options"][$key]["sort_by_key"]) {
								$tl->add_record($key,$db->f("primkey"),$array["selection"][$db->f($key)],$db->f($key),"",array("html"=>$html));
							} else {
								$tl->add_record($key,$db->f("primkey"),$array["selection"][$db->f($key)],$array["selection_sort"][$db->f($key)],"",array("html"=>$html));
							}
						}
					} elseif($this->db[$counter]["type"][$key]=="yesno") {
						$tl->add_record($key,$db->f("primkey"),($db->f($key) ? $this->message("ja",false) : ($this->list[$counter]["options"][$key]["showno"] ? $this->message("nee",false) : "")),$db->f($key));
					} elseif($this->list[$counter]["options"][$key]["pretext"] or $this->list[$counter]["options"][$key]["posttext"]) {
						$tl->add_record($key,$db->f("primkey"),ereg_replace("\[FIELD_VALUE\]",$db->f($key),$this->list[$counter]["options"][$key]["pretext"]).htmlentities($db->f($key)).$this->list[$counter]["options"][$key]["posttext"],$db->f($key),"",array("html"=>true));
					} else {
						# Overige types
						$tl->add_record($key,$db->f("primkey"),$db->f($key));
#						echo $db->f($key)."<br>";
					}
				}
			}
			if(is_array($this->delete_error[$counter])) {
				echo "<font class=\"wtform_error\">";
				if($this->delete_error_firstline) {
					echo $this->delete_error_firstline; 
				} else {
					echo $this->message("foutbijhetwissenvan")." ";
					$temp_recordnaam=htmlentities($tl->fields["content"][($this->settings[$counter]["list"]["mainfield"] ? $this->settings[$counter]["list"]["mainfield"] : $this->show_mainfield[$counter])][$this->delete_key]);
					if($temp_recordnaam) {
						echo $temp_recordnaam;
					} else {
						echo htmlentities($this->settings[$counter]["type_single"]);
					}
					echo ":";
				}
				echo "<ul>";
				while(list($key,$value)=each($this->delete_error[$counter])) {
					echo "<li>".$value."</li>";
				}
				echo "</ul></font><p>";
			}
			# List niet tonen indien referer andere pagina is
			if(is_array($this->delete_error[$counter]) and $_SERVER["HTTP_REFERER"] and !ereg($this->settings[$counter]["file"],$_SERVER["HTTP_REFERER"])) {
#				echo "<a href=\"".$_SERVER["HTTP_REFERER"]."\">".$this->message("foutbijhetwissenvan")."</a>";
			} else {
				if($this->settings[$counter]["list"]["add_link"]) $this->list_addlink($counter);
				if($this->settings[$counter]["list"]["delete_checkbox"]) {
					echo "<form method=\"post\" action=\"".htmlentities($_SERVER["REQUEST_URI"])."\" name=\"frm_delete_checkbox_".$counter."\">";
					echo "<input type=\"hidden\" name=\"delete_checkbox_filled[".$counter."]\" value=\"1\">";
				}
				echo "<p>".$tl->table("tbl",$counter);
				if($this->settings[$counter]["list"]["delete_checkbox"]) {
#					$wishref="<a href=\"#\" onclick=\"confirmDelete('".$this->php_self($counter)."delete_checkbox=".$counter."','".$this->message("aangevinkterecordswissen",true,array("veldnaam"=>$this->settings[$counter]["types"]))."?');\">";
					$wishref="<a href=\"#\" onclick=\"if(confirm('".$this->message("aangevinkterecordswissen",true,array("veldnaam"=>$this->settings[$counter]["types"]))."?')) { document.frm_delete_checkbox_".$counter.".submit(); return false; } else return false;\">";
					echo $wishref."<img src=\"pic/class.cms_delete.gif\" width=\"20\" height=\"20\" alt=\"".$this->message("printen",false)."\" border=\"0\" style=\"vertical-align:middle;position:relative;bottom:1px;\"></a>&nbsp;";
					echo $wishref.$this->message("aangevinkterecordswissen",true,array("veldnaam"=>$this->settings[$counter]["types"]))."</a>";
					echo "</form>";
				}
			}
		} else {
			if($this->settings[$counter]["list"]["add_link"]) $this->list_addlink($counter);
			echo "<p>";
			if($this->settings[$counter]["parent"]) {
				echo $this->message("erzijngeenveldnaamgekoppeld",true,array("veldnaam"=>$this->settings[$counter]["types"]));
			} else {
				echo $this->message("erzijngeenveldnaamaanwezig",true,array("veldnaam"=>$this->settings[$counter]["types"]));
			}
			$this->output[$counter]["list"]["records"]=0;
		}
	}

	function list_addlink($counter) {
		reset($this->db[$counter]["type"]);
		list($key,$value)=each($this->db[$counter]["type"]);
		if($this->settings[$counter]["parent"] and count($this->db[$counter]["field"])==1 and $this->db[$counter]["type"][$key]=="select") {
			# Toevoegen van records met maar 1 db-select-field
			if(is_array($this->db[$counter]["options"][$key]["selection"])) {
				echo "<form method=\"post\" action=\"".$_SERVER["REQUEST_URI"]."\" name=\"pulldownadd".$counter."\"><select class=\"wtform_input\" name=\"addlink[".$counter."]\" onchange=\"this.form.submit();\">";
				echo "<option value=\"0\">- ".$this->message("selecteereentoetevoegenveldnaam",true,array("veldnaam"=>$this->settings[$counter]["type_single"]))." -</option>";
				while(list($key2,$value2)=each($this->db[$counter]["options"][$key]["selection"])) {
					echo "<option value=\"".htmlentities($key2)."\">".htmlentities($value2)."</option>\n";
				}
				echo "</select></form>";
			} else {
				echo "(".$this->message("noggeenveldnaaminhetsysteem",true,array("veldnaam"=>$this->settings[$counter]["types"])).")";
			}
		} else {
			# Toevoeglink voor alle overige tabellen
#			echo "<a href=\"".$this->php_self($counter)."bc=".($_GET["bc"]+1)."&add=".$counter."&".$counter."mt=".$this->db[$counter]["maintable"];
			echo "<a href=\"".$this->php_self($counter)."bc=".($_GET["bc"]+1)."&add=".$counter;
			if($this->stripped_query_string) {
				echo "&".$this->stripped_query_string;
			}
			if(is_array($this->settings[$counter]["prevalue"])) {
				reset($this->settings[$counter]["prevalue"]);
				while(list($key,$value)=each($this->settings[$counter]["prevalue"])) {
					echo "&pv_".$key."=".urlencode($value);				
				}
			}
			echo "\">".htmlentities(ucfirst($this->message("veldnaamtoevoegen",false,array("veldnaam"=>$this->settings[$counter]["type_single"]))))."</a>";
		}
	}

	function go_to($counter) {
		if($_GET["gotouri"]) {
			$return=$_GET["gotouri"];
		} elseif($_SESSION["CMS2"]["back"][$_GET["bc"]-1]["file"]) {
			$return=$_SESSION["CMS2"]["back"][$_GET["bc"]-1]["file"];
			if($_SESSION["CMS2"]["back"][$_GET["bc"]-1]["qs"]) {
				$return.="?".$_SESSION["CMS2"]["back"][$_GET["bc"]-1]["qs"];
			}
		} else {
			$return=$this->php_self($counter);
		}
		$return=ereg_replace("\?$","",$return);
		return $return;
	}
	
	function set_edit_form_init($counter) {
		global $cms_form;
		if($_GET["add"]==$counter) $add=true;
		# Gegevens voor formulier opzetten
		if(($_GET["edit"]==$counter or $_GET["add"]==$counter) and !$this->set_edit_form_init[$counter] and is_array($this->edit[$counter]["title"])) {
			$this->set_edit_form_init[$counter]=true;
			$db=new DB_sql;
			$primarykey=$this->get_primarykey($this->db[$counter]["maintable"],$counter);
	
			$cms_form[$counter]=new form2;
			$cms_form[$counter]->settings["fullname"]="cms_".$counter;
			$cms_form[$counter]->settings["db"]["table"]=$this->db[$counter]["maintable"];
			$cms_form[$counter]->settings["language"]=$this->settings["language"];
			$cms_form[$counter]->settings["show_save_message"]=$this->settings["show_save_message"];
			$cms_form[$counter]->settings["show_upload_message"]=$this->settings["show_upload_message"];
			
			if($add) {
				$cms_form[$counter]->settings["message"]["submitbutton"][$this->settings["language"]]=$this->message("toevoegen",false);
			} else {
				$cms_form[$counter]->settings["message"]["submitbutton"][$this->settings["language"]]=$this->message("opslaan",false);
			}
			if($this->settings[$counter]["edit"]["top_submit_button"]) {
				$cms_form[$counter]->settings["layout"]["top_submit_button"]=true;
			}
			if($add) {
	#			$cms_form[$counter]->settings["db"]["add"]=$add;
				$cms_form[$counter]->settings["db"]["set"]=$this->db[$counter]["set"];
			} else {
				$cms_form[$counter]->settings["db"]["where"]=$primarykey["where"];
			}
			while(list($key,$value)=each($this->edit[$counter]["title"])) {
				$this->db[$counter]["type"][$key];
	
				# DB-gegevens
				unset($formdb,$prevalue,$options,$layout);
				if(!$this->db[$counter]["options"][$key]["notdb"]) {
					$formdb["field"]=$this->db[$counter]["field"][$key];
				}
				$prevalue=$this->edit[$counter]["prevalue"][$key];
				$options=$this->edit[$counter]["options"][$key];
				if($this->db[$counter]["options"][$key]["selection"] and !$options["selection"]) $options["selection"]=$this->db[$counter]["options"][$key]["selection"];
				if($this->db[$counter]["options"][$key]["multiselection"] and !$options["multiselection"]) $options["multiselection"]=$this->db[$counter]["options"][$key]["multiselection"];
				if($this->db[$counter]["options"][$key]["multiselectionfields"]) $options["multiselectionfields"]=$this->db[$counter]["options"][$key]["multiselectionfields"];
				if($this->db[$counter]["options"][$key]["encode"] and !$formdb["encode"]) $formdb["encode"]=$this->db[$counter]["options"][$key]["encode"];
				
				$layout=$this->edit[$counter]["layout"][$key];
				if($this->edit[$counter]["options"][$key]["noedit"]) {
					# options=noedit
					if(!$prevalue["selection"] and $this->settings[$counter]["prevalue"][$key]) {
						$prevalue["selection"]=$this->settings[$counter]["prevalue"][$key];
					}
					$cms_form[$counter]->field_noedit($key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="checkbox") {
					$cms_form[$counter]->field_checkbox($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="currency") {
					$cms_form[$counter]->field_currency($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="date") {
					$cms_form[$counter]->field_date($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);	
				} elseif($this->db[$counter]["type"][$key]=="datetime") {
					$cms_form[$counter]->field_datetime($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);	
				} elseif($this->db[$counter]["type"][$key]=="email") {
					$cms_form[$counter]->field_email($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="float") {
					$cms_form[$counter]->field_float($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="integer") {
					$cms_form[$counter]->field_integer($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="multiradio") {
					$cms_form[$counter]->field_multiradio($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="noedit") {
					$cms_form[$counter]->field_noedit($key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="onlyinoutput") {
					$cms_form[$counter]->field_onlyinoutput($key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="password") {
					$cms_form[$counter]->field_password($this->edit[$counter]["obl"][$key],$key,($_GET["edit"]==$counter ? $this->message("nieuw",false)." ".strtolower($this->edit[$counter]["title"][$key]) : $this->edit[$counter]["title"][$key]),$formdb,$prevalue,$options,$layout);
					$cms_form[$counter]->field_password($this->edit[$counter]["obl"][$key],$key."_repeat",$this->message("herhaal",false)." ".strtolower($this->edit[$counter]["title"][$key]),"",$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="picture" or $this->db[$counter]["type"][$key]=="upload") {
					$options["move_file_to"]=$this->db[$counter]["options"][$key]["savelocation"];
	 				$options["must_be_filetype"]=$this->db[$counter]["options"][$key]["filetype"];
	 				$options["multiple"]=$this->db[$counter]["options"][$key]["multiple"];
	 				if($_GET[$counter."k0"]) {
	 					$options["rename_file_to"]=$_GET[$counter."k0"];
	 				} else {
		 				$options["rename_file_to"]="";
		 			}
					$cms_form[$counter]->field_upload($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],"",$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="radio") {
					$cms_form[$counter]->field_radio($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="select") {
					$cms_form[$counter]->field_select($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="text") {
					$cms_form[$counter]->field_text($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="textarea") {
					$cms_form[$counter]->field_textarea($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="url") {
					$cms_form[$counter]->field_url($this->edit[$counter]["obl"][$key],$key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif($this->db[$counter]["type"][$key]=="yesno") {
					$cms_form[$counter]->field_yesno($key,$this->edit[$counter]["title"][$key],$formdb,$prevalue,$options,$layout);
				} elseif(ereg("^htmlcol_",$key)) {
					#
					# Waarde buiten db om (htmlcol)
					#
					$cms_form[$counter]->field_htmlcol($key,$value,$prevalue,$options,$layout);
				} elseif(ereg("^htmlrow_",$key)) {
					#
					# Waarde buiten db om (htmlrow)
					#
					$cms_form[$counter]->field_htmlrow($key,$value,$options,$layout);
				} elseif(!$this->db[$counter]["type"][$key]) {
					trigger_error("WT-Error: unknown edit-id '".$key."' called in function 'display_edit'",E_USER_ERROR);
				} else {
					trigger_error("WT-Error: unknown db-type '".$this->db[$counter]["type"][$key]."' called in function 'display_edit'",E_USER_ERROR);
				}
			}
			$cms_form[$counter]->check_input();
			
			# Invoer controleren
			@reset($this->edit[$counter]["title"]);
			while(list($key,$value)=@each($this->edit[$counter]["title"])) {
				if($this->db[$counter]["type"][$key]=="password") {
					# Zijn de wachtwoorden hetzelfde?
					if($cms_form[$counter]->input[$key]==$cms_form[$counter]->input[$key."_repeat"]) {
						# Voorkomen dat error_strongpassword ook bij _repeat getoond wordt
						if($cms_form[$counter]->error[$key."_repeat"]<>"obl") unset($cms_form[$counter]->error[$key."_repeat"]);
					} else {
						$cms_form[$counter]->error($key."_repeat",$this->message("niethetzelfde",false),true);
					}
				}
			}
		}
	}
	
	function set_edit($counter,$add=false) {
	
		if($_GET["add"]) {
			# Kijken of add_error_temp nodig is (alleen indien $obl) (zo ja: add_error vullen)
			while(list($key,$value)=@each($this->add_error_temp[$counter])) {
				if($this->edit[$counter]["obl"][$key]) {
					$this->add_error[$counter][$key]=$value;
				}
			}
		} else {
			$db0=new DB_sql;
			$query=$this->get_query($counter,"SELECT");
			$db0->query($query);
			if($db0->num_rows()<>1) {
#				trigger_error("WT-Error: record niet gevonden bij set_edit",E_USER_NOTICE);
				$this->set_edit_error[$counter][]=$this->message("record_niet_gevonden");
				return false;
			}
		}
	
		global $cms_form;
#		if($_GET[$counter."mt"]==$this->db[$counter]["maintable"]) {
		if(($_GET["edit"]==$counter or $_GET["add"]==$counter) and is_array($this->edit[$counter]["title"])) {
			$this->set_edit_form_init($counter);			
			if($cms_form[$counter]->okay) {
				
				# Gegevens opslaan in de database
				$cms_form[$counter]->save_db();
				
				# Gegevens loggen
				if($this->settings[$counter]["log"]["active"]) {
					global $login;
					$db=new DB_sql;
					while(list($key,$value)=each($cms_form[$counter]->checked_input)) {
						unset($cmslog_previous,$cmslog_now,$cmslog_recordname,$cmslog_compare_previous,$cmslog_compare_now,$cmslog_compare_previous_array,$cmslog_compare_now_array,$cmslog_temp);
						
						if($cms_form[$counter]->fields["type"][$key]=="checkbox") {
							$cmslog_compare_previous_array=split(",",$cms_form[$counter]->fields["previous"][$key]["selection"]);
							@asort($cmslog_compare_previous_array);
							while(list($key2,$value2)=@each($cmslog_compare_previous_array)) {
								if($cmslog_compare_previous) $cmslog_compare_previous.=",".$value2; else $cmslog_compare_previous=$value2;
							}
							$cmslog_compare_now_array=split(",",$value);
							@asort($cmslog_compare_now_array);
							while(list($key2,$value2)=@each($cmslog_compare_now_array)) {
								if($cmslog_compare_now) $cmslog_compare_now.=",".$value2; else $cmslog_compare_now=$value2;
							}
							if($cmslog_compare_now<>$cmslog_compare_previous) {
								$cmslog_compare_previous_array=split(",",$cms_form[$counter]->fields["previous"][$key]["selection"]);
								while(list($key2,$value2)=@each($cmslog_compare_previous_array)) {
									if($cmslog_previous) $cmslog_previous.="\n".$cms_form[$counter]->fields["options"][$key]["selection"][$value2]; else $cmslog_previous=$cms_form[$counter]->fields["options"][$key]["selection"][$value2];
								}
								$cmslog_compare_now_array=split(",",$value);
								while(list($key2,$value2)=@each($cmslog_compare_now_array)) {
									if($cmslog_now) $cmslog_now.="\n".$cms_form[$counter]->fields["options"][$key]["selection"][$value2]; else $cmslog_now=$cms_form[$counter]->fields["options"][$key]["selection"][$value2];
								}
							}
						} elseif($cms_form[$counter]->fields["type"][$key]=="datetime") {
							$cmslog_compare_previous=$cms_form[$counter]->fields["previous"][$key]["time"];
							$cmslog_compare_now=$value["unixtime"];

							if($this->db[$counter]["type"][$key]=="date") {
								if($cmslog_compare_previous<>0) {
									$cmslog_compare_previous=mktime(0,0,0,date("m",$cmslog_compare_previous),date("d",$cmslog_compare_previous),date("Y",$cmslog_compare_previous));
								}
								if($cmslog_compare_now<>0) {
									$cmslog_compare_now=mktime(0,0,0,date("m",$cmslog_compare_now),date("d",$cmslog_compare_now),date("Y",$cmslog_compare_now));
								}
							}
							if($cmslog_compare_now<>$cmslog_compare_previous) {
								if($this->db[$counter]["type"][$key]=="datetime") {
									if($cms_form[$counter]->fields["previous"][$key]["time"]<>0) {
										$cmslog_previous=datum("D MAAND JJJJ, UU:ZZ",$cms_form[$counter]->fields["previous"][$key]["time"],$this->settings["language"]);
									}
									if($value["unixtime"]<>0) {
										$cmslog_now=datum("D MAAND JJJJ, UU:ZZ",$value["unixtime"],$this->settings["language"]);
									}
								} else {
									if($cms_form[$counter]->fields["previous"][$key]["time"]<>0) {
										$cmslog_previous=datum("D MAAND JJJJ",$cms_form[$counter]->fields["previous"][$key]["time"],$this->settings["language"]);
									}
									if($value["unixtime"]<>0) {
										$cmslog_now=datum("D MAAND JJJJ",$value["unixtime"],$this->settings["language"]);
									}
								}
							}
						} elseif($cms_form[$counter]->fields["type"][$key]=="htmlcol") {

						} elseif($cms_form[$counter]->fields["type"][$key]=="htmlrow") {
						
						} elseif($cms_form[$counter]->fields["type"][$key]=="multiradio") {
							while(list($key2,$value2)=@each($cms_form[$counter]->fields["previous"][$key]["multiselection"])) {
								$cmslog_temp=@split(",",$value2);
								while(list($key3,$value3)=@each($cmslog_temp)) {
									$cmslog_compare_previous_array[$value3]=$key2;
								}
							}
							@reset($cms_form[$counter]->input[$key]);
							while(list($key2,$value2)=@each($cms_form[$counter]->input[$key])) {
								$cmslog_temp=@split(",",$value2);
								while(list($key3,$value3)=@each($cmslog_temp)) {
									$cmslog_compare_now_array[$value3]=$key2;
								}
							}
							
							@reset($cms_form[$counter]->fields["options"][$key]["selection"]);
							while(list($key2,$value2)=@each($cms_form[$counter]->fields["options"][$key]["selection"])) {
								if($cmslog_compare_now_array[$key2]<>$cmslog_compare_previous_array[$key2]) {
									if($cmslog_compare_previous_array[$key2]) {
										if($cmslog_previous) $cmslog_previous.="\n".$cms_form[$counter]->fields["options"][$key]["selection"][$key2].": ".$cms_form[$counter]->fields["options"][$key]["multiselection"][$cmslog_compare_previous_array[$key2]]; else $cmslog_previous=$cms_form[$counter]->fields["options"][$key]["selection"][$key2].": ".$cms_form[$counter]->fields["options"][$key]["multiselection"][$cmslog_compare_previous_array[$key2]];
									}
									if($cmslog_compare_now_array[$key2]) {
										if($cmslog_now) $cmslog_now.="\n".$cms_form[$counter]->fields["options"][$key]["selection"][$key2].": ".$cms_form[$counter]->fields["options"][$key]["multiselection"][$cmslog_compare_now_array[$key2]]; else $cmslog_now=$cms_form[$counter]->fields["options"][$key]["selection"][$key2].": ".$cms_form[$counter]->fields["options"][$key]["multiselection"][$cmslog_compare_now_array[$key2]];
									}
								}
							}
						} elseif($cms_form[$counter]->fields["type"][$key]=="noedit") {

						} elseif($cms_form[$counter]->fields["type"][$key]=="onlyinoutput") {

						} elseif($cms_form[$counter]->fields["type"][$key]=="password") {
							if(!strpos($key,"_repeat")) {
								$cmslog_previous="";
								$cmslog_now="******";
							}
						} elseif($cms_form[$counter]->fields["type"][$key]=="radio") {
							$cmslog_compare_previous=$cms_form[$counter]->fields["previous"][$key]["selection"];
							if($cmslog_compare_previous=="0" or !$cmslog_compare_previous) {
								$cmslog_compare_previous=0;
							}
							$cmslog_compare_now=$value;
							if($cmslog_compare_now=="0" or !$cmslog_compare_now) {
								$cmslog_compare_now=0;
							}
							if($cmslog_compare_now<>$cmslog_compare_previous) {
								$cmslog_previous=$cms_form[$counter]->fields["options"][$key]["selection"][$cmslog_compare_previous];
								$cmslog_now=$cms_form[$counter]->fields["options"][$key]["selection"][$cmslog_compare_now];
							}
						} elseif($cms_form[$counter]->fields["type"][$key]=="select") {
							$cmslog_compare_previous=$cms_form[$counter]->fields["previous"][$key]["selection"];
							if($cmslog_compare_previous=="0" or !$cmslog_compare_previous) {
								$cmslog_compare_previous=0;
							}
							$cmslog_compare_now=$value;
							if($cmslog_compare_now=="0" or !$cmslog_compare_now) {
								$cmslog_compare_now=0;
							}
							if($cmslog_compare_now<>$cmslog_compare_previous) {
								$cmslog_previous=$cms_form[$counter]->fields["options"][$key]["selection"][$cmslog_compare_previous];
								$cmslog_now=$cms_form[$counter]->fields["options"][$key]["selection"][$cmslog_compare_now];
							}
						} elseif($cms_form[$counter]->fields["type"][$key]=="text") {
							if(trim($value)<>trim($cms_form[$counter]->fields["previous"][$key]["text"])) {
								$cmslog_previous=$cms_form[$counter]->fields["previous"][$key]["text"];
								$cmslog_now=$value;
							}
						} elseif($cms_form[$counter]->fields["type"][$key]=="textarea") {
							if(trim($value)<>trim($cms_form[$counter]->fields["previous"][$key]["text"])) {
								$cmslog_previous=$cms_form[$counter]->fields["previous"][$key]["text"];
								$cmslog_now=$value;
							}
						} elseif($cms_form[$counter]->fields["type"][$key]=="upload") {
							if($cms_form[$counter]->upload[$key]) {
								if($_POST["imagedelete"][$key]) {
									$cmslog_previous=$this->message("bestand",false);
									$cmslog_now="-- ".$this->message("bestandgewistenvervangen",false)." --";
								} else {
									$cmslog_previous="";
									$cmslog_now=$this->message("nieuwbestand",false);
								}
							} elseif($_POST["imagedelete"][$key]) {
								$cmslog_previous=$this->message("bestand",false);
								$cmslog_now="-- ".$this->message("bestandgewist",false)." --";
							}
						} elseif($cms_form[$counter]->fields["type"][$key]=="yesno") {
							if(intval($value)<>intval($cms_form[$counter]->fields["previous"][$key]["selection"])) {
								if(intval($value)==0) {
									$cmslog_previous=$this->message("ja",false);
									$cmslog_now=$this->message("nee",false);
								} else {
									$cmslog_previous=$this->message("nee",false);
									$cmslog_now=$this->message("ja",false);
								}
							}
						}
						if(isset($cmslog_previous) or isset($cmslog_now)) {
							if($this->show_mainfield[$counter]) {
								$cmslog_recordname=$cms_form[$counter]->checked_input[$this->show_mainfield[$counter]];
							}
							
							if(!$this->db[$counter]["options"][$key]["dontlog"]) {
								unset($recordid);
								if($_GET[$counter."k0"]) {
									$recordid=$_GET[$counter."k0"];
								} elseif($cms_form[$counter]->db_insert_id) {
									$recordid=$cms_form[$counter]->db_insert_id;
								}
								if($login->user_id) {
									if($_GET["add"]==$counter and !$this->tempvar[$counter]["addlog"]) {
										# Opslaan dat nieuw record is toegevoegd (specialtype=1)
										$db->query("INSERT INTO cmslog SET user_id='".addslashes($login->user_id)."', cms_id='".addslashes($counter)."', cms_name='".addslashes($this->settings[$counter]["type_single"])."', record_id='".($recordid ? addslashes($recordid) : "")."', record_name='".addslashes($cmslog_recordname)."', table_name='".addslashes($this->db[$counter]["maintable"])."', specialtype=1, url='".addslashes($_SERVER["REQUEST_URI"])."', savedate=NOW();");
										$this->tempvar[$counter]["addlog"]=true;
									}
									if($cms_form[$counter]->fields["layout"][$key]["title_html"]) {
										$field_name=trim(strip_tags(html_entity_decode($cms_form[$counter]->fields["title"][$key])));
									} else {
										$field_name=$cms_form[$counter]->fields["title"][$key];
									}
									
									$db->query("INSERT INTO cmslog SET user_id='".addslashes($login->user_id)."', cms_id='".addslashes($counter)."', cms_name='".addslashes($this->settings[$counter]["type_single"])."', record_id='".($recordid ? addslashes($recordid) : "")."', record_name='".addslashes($cmslog_recordname)."', table_name='".addslashes($this->db[$counter]["maintable"])."', field='".addslashes($key)."', field_name='".addslashes($field_name)."', field_type='".addslashes($cms_form[$counter]->fields["type"][$key])."', previous='".addslashes($cmslog_previous)."', now='".addslashes($cmslog_now)."', url='".addslashes($_SERVER["REQUEST_URI"])."', savedate=NOW();");
#									echo $db->lastquery."<br>";
								} else {
									trigger_error("WT-Error: cmslog niet opgeslagen: user_id onbekend",E_USER_NOTICE);
								}
							}
						}
					}
				}
				
				if($this->settings[$counter]["show"]["goto_new_record"] and $cms_form[$counter]->db_insert_id) {
					# Ga naar net aangemaakte record
					$cms_form[$counter]->settings["goto"]=$this->php_self($counter)."bc=".$_GET["bc"]."&show=".$counter."&".$counter."k0=".$cms_form[$counter]->db_insert_id.($this->stripped_query_string ? "&".$this->stripped_query_string : "");
				} elseif($this->settings[$counter]["show"]["goto_changed_record"]) {
					# Ga naar net gewijzigd record
					$cms_form[$counter]->settings["goto"]=$this->php_self($counter)."bc=".$_GET["bc"]."&show=".$counter."&".$counter."k0=".$_GET[$counter."k0"].($this->stripped_query_string ? "&".$this->stripped_query_string : "");
				} elseif($_GET["hidecmsmenu"]) {
					# Ga naar pagina en sluit venster
					$cms_form[$counter]->settings["goto"]="cms_sluitvenster.php";
				} else {
					# Ga terug naar waar je vandaankwam
					$cms_form[$counter]->settings["goto"]=$this->go_to($counter);
				}
			}
			if($_GET["add"]==$counter and is_array($this->add_error[$counter]) and @count($this->add_error[$counter])>0) {
				# Bij add_error end_declaration niet toestaan
			} else {
				$cms_form[$counter]->end_declaration();
			}
		}
	}

	function display_edit($counter) {
		global $cms_form;
		$this->back_link($counter);
		if(is_array($this->edit[$counter]["title"]) and $_GET["add"]==$counter and is_array($this->add_error[$counter]) and @count($this->add_error[$counter])>0) {
			echo "<font class=\"wtform_error\">";
			echo $this->message("veldnaamtoevoegennietmogelijk",true,array("veldnaam"=>$this->settings[$counter]["type_single"]));
			echo "<ul>";
			while(list($key,$value)=each($this->add_error[$counter])) {
				reset($this->edit[$counter]["title"]);
				while(list($key2,$value2)=each($this->edit[$counter]["title"])) {
					$value=ereg_replace("\[field_".$key2."\]",$value2,$value);
				}
				echo "<li>".$value."</li>";
			}
			echo "</ul></font><p>";
		} elseif(is_array($this->set_edit_error[$counter])) {
			echo "<font class=\"wtform_error\">";
			echo "<ul>";
			reset($this->set_edit_error[$counter]);
			while(list($key,$value)=each($this->set_edit_error[$counter])) {
				echo "<li>".$value."</li>";
			}
			echo "</ul></font><p>";
		} else {
			$cms_form[$counter]->display_all();
		}
	}

	function set_show($counter) {
		$db=new DB_sql;
		$query=$this->get_query($counter,"SELECT");
		$db->query($query);
		if($db->num_rows()>1) {
			trigger_error("WT-Error: multiple num_rows in cms2-class bij set_show-query",E_USER_NOTICE);
			header("Location: ".$_SERVER["PHP_SELF"]."?".wt_stripget($_GET,array("show")));
			exit;
		}
		if($db->num_rows()<1) {
#			trigger_error("WT-Error: record niet gevonden bij set_show",E_USER_NOTICE);
#
# AANZETTEN?
#
#			$this->set_show_error[$counter][]=$this->message("record_niet_gevonden");
		}
		if($db->next_record()) {
			
			# Mainfield opvragen
			if($this->show_mainfield[$counter]) $this->show_rightcell[$this->show_mainfield[$counter]]=$db->f($this->db[$counter]["field"][$this->show_mainfield[$counter]]);
			
			while(list($key,$value)=@each($this->show[$counter]["title"])) {
				$this->show_leftcell[$key]=$value;
				$recordname=$this->db[$counter]["field"][$key];
				
				if($key=="wt_naam") {
					# wt_naam
					$this->show_rightcell[$key]=htmlentities(wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam")));
				} elseif($key=="wt_naam2") {
					# wt_naam2
					$this->show_rightcell[$key]=htmlentities(wt_naam($db->f("voornaam"),$db->f("tussenvoegsel"),$db->f("achternaam"),true));
				} elseif($key=="wt_naam3") {
					# wt_naam3
					$this->show_rightcell[$key]=htmlentities(wt_naam($db->f("voorletters"),$db->f("tussenvoegsel"),$db->f("achternaam"),false,true));
				} elseif($key=="wt_naam4") {
					# wt_naam4
					$this->show_rightcell[$key]=htmlentities(wt_naam($db->f("voorletters"),$db->f("tussenvoegsel"),$db->f("achternaam"),true,true));
				} elseif($this->db[$counter]["type"][$key]=="checkbox") {
					$checkbox_waarde=split(",",$db->f($recordname));
					while(list($key2,$value2)=each($checkbox_waarde)) {
						if($this->show_rightcell[$key]) {
							$this->show_rightcell[$key].="<br>".htmlentities($this->db[$counter]["options"][$key]["selection"][$value2]);
						} else {
							$this->show_rightcell[$key]=htmlentities($this->db[$counter]["options"][$key]["selection"][$value2]);
						}
					}
				} elseif($this->db[$counter]["type"][$key]=="currency") {
					$this->show_rightcell[$key]=htmlentities(ereg_replace("\.",",",$db->f($recordname)));
				} elseif($this->db[$counter]["type"][$key]=="date" or $this->db[$counter]["type"][$key]=="datetime") {
					if($db->f($recordname)) {
						$this->show_rightcell[$key]=htmlentities(datum($this->show[$counter]["options"][$key]["date_format"],$db->f($recordname),$this->settings["language"]));
					} else {
						$this->show_rightcell[$key]="";
					}
				} elseif($this->db[$counter]["type"][$key]=="email") {
					if($db->f($recordname)) $this->show_rightcell[$key]="<a href=\"mailto:".$db->f($recordname)."\">".htmlentities($db->f($recordname))."</a>";
				} elseif($this->db[$counter]["type"][$key]=="float") {
					$this->show_rightcell[$key]=htmlentities(ereg_replace("\.",",",$db->f($recordname)));
				} elseif($this->db[$counter]["type"][$key]=="integer") {
					$this->show_rightcell[$key]=htmlentities($db->f($recordname));
				} elseif($this->db[$counter]["type"][$key]=="noedit") {
					$this->show_rightcell[$key]=htmlentities($db->f($recordname));
				} elseif($this->db[$counter]["type"][$key]=="onlyinoutput") {
					$this->show_rightcell[$key]=htmlentities($db->f($recordname));
				} elseif($this->db[$counter]["type"][$key]=="picture") {
					unset($temp["img"]);
					unset($this->show_leftcell[$key]);
					$temp["img"]["file"]=$this->db[$counter]["options"][$key]["savelocation"].substr($db->f("primkey"),0,-1).".jpg";
					if($this->show[$counter]["options"][$key]["img_width"] and $this->show[$counter]["options"][$key]["img_height"]) {
						$temp["img"]["width"]=$this->show[$counter]["options"][$key]["img_width"];
						$temp["img"]["height"]=$this->show[$counter]["options"][$key]["img_height"];
					} else {
						$temp["img"]["getimagesize"]=@getimagesize($temp["img"]["file"]);
						$temp["img"]["width"]=$temp["img"]["getimagesize"][0];
						$temp["img"]["height"]=$temp["img"]["getimagesize"][1];
					}
					if(file_exists($temp["img"]["file"])) {
						$this->show_leftcell[$key]=$value;
						$this->show_rightcell[$key]="<img src=\"".$temp["img"]["file"]."?anticache=".time()."\" width=\"".$temp["img"]["width"]."\" height=\"".$temp["img"]["height"]."\" alt=\"".htmlentities($temp["img"]["file"])."\">";
					}
				} elseif($this->db[$counter]["type"][$key]=="radio") {
					$this->show_rightcell[$key]=htmlentities($this->db[$counter]["options"][$key]["selection"][$db->f($recordname)]);
				} elseif($this->db[$counter]["type"][$key]=="select") {
					$this->show_rightcell[$key]=htmlentities($this->db[$counter]["options"][$key]["selection"][$db->f($recordname)]);
				} elseif($this->db[$counter]["type"][$key]=="text") {
					$this->show_rightcell[$key]=htmlentities($db->f($recordname));
				} elseif($this->db[$counter]["type"][$key]=="textarea") {
					$this->show_rightcell[$key]=nl2br(htmlentities($db->f($recordname)));
				} elseif($this->db[$counter]["type"][$key]=="upload") {
					unset($temp["upload"]);
					unset($this->show_leftcell[$key]);
					$temp["upload"]["file"]=$this->db[$counter]["options"][$key]["savelocation"].substr($db->f("primkey"),0,-1).".".$this->db[$counter]["options"][$key]["filetype"];
					if(file_exists($temp["upload"]["file"])) {
						$this->show_leftcell[$key]=$value;
						if($this->db[$counter]["options"][$key]["filetype"]=="pdf") {
							$temp["upload"]["icon"]="class.form_pdf_icon.gif";
						} else {
							$temp["upload"]["icon"]="class.form_unknown_icon.gif";
						}
						$this->show_rightcell[$key]="<a href=\"".htmlentities($temp["upload"]["file"])."?c=".@filemtime($temp["upload"]["file"])."\" target=\"_blank\"><img src=\"pic/".$temp["upload"]["icon"]."\" width=\"20\" height=\"20\" alt=\"".htmlentities($temp["upload"]["file"])."\" border=\"0\"></a>";
					}
				} elseif($this->db[$counter]["type"][$key]=="url") {
					if($db->f($recordname)) $this->show_rightcell[$key]="<a href=\"".$db->f($recordname)."\" target=\"_blank\">".htmlentities($db->f($recordname))."</a>";
				} elseif($this->db[$counter]["type"][$key]=="yesno") {
					$this->show_rightcell[$key]=($db->f($recordname) ? $this->message("ja") : $this->message("nee"));
				} elseif(ereg("^htmlcol_",$key)) {
				
				} elseif(ereg("^htmlrow_",$key)) {

				} elseif(!$this->db[$counter]["type"][$key]) {
					trigger_error("WT-Error: unknown edit-id '".$key."' called in function 'set_show'",E_USER_ERROR);
				} else {
					trigger_error("WT-Error: unknown db-type '".$this->db[$counter]["type"][$key]."' called in function 'set_show'",E_USER_ERROR);
				}
			}
		}
	}

	function back_link($counter,$custom="") {

		# Bij een child geen back_link
		if($this->settings[$counter]["parent"]) return;

		if($_GET["bc"]) {

		} else {
			if($_SESSION["CMS2"]["back"]["max"]) {
				$_GET["bc"]=$_SESSION["CMS2"]["back"]["max"]+1;
			} else {
				$_GET["bc"]=1;
			}
		}

		$backcounter=$_GET["bc"];
		if(($_GET["show"]<>$counter and $_GET["edit"]<>$counter and $_GET["add"]<>$counter) or $custom) {
			# in geval van list en custom de teller 3 ophogen (zodat er niet wordt teruggegaan naar een back_link van een andere tabel)
			$_GET["bc"]=$_GET["bc"]+3;
		}
		if($_GET["bc"]>$_SESSION["CMS2"]["back"]["max"]) $_SESSION["CMS2"]["back"]["max"]=$_GET["bc"];

		reset($_GET);
		$no_get=array("delete","confirmed");
		if($_GET["delete"]==$counter) {
			# Maximaal 5 keys wissen (moeten allemaal gewist worden, maar 't zijn er toch nooit meer dan 5)
			$no_get[]=$counter."k0";
			$no_get[]=$counter."k1";
			$no_get[]=$counter."k2";
			$no_get[]=$counter."k3";
			$no_get[]=$counter."k4";
		}
		while(list($key,$value)=each($_GET)) {
			if(!@in_array($key,$no_get)) {
				if(is_array($value)) {
					while(list($key2,$value2)=each($value)) {
						if(is_array($value2)) {
							while(list($key3,$value3)=each($value2)) {
								if(is_array($value3)) {
									while(list($key4,$value4)=each($value3)) {
										if(is_array($value4)) {
											while(list($key5,$value5)=each($value4)) {
												if(is_array($value4)) {
													while(list($key6,$value6)=each($value5)) {
														if($qs) $qs.="&".$key."%5B".$key2."%5D%5B".$key3."%5D%5B".$key4."%5D%5B".$key5."%5D%5B".$key6."%5D=".urlencode($value6); else $qs=$key."%5B".$key2."%5D%5B".$key3."%5D%5B".$key4."%5D%5B".$key5."%5D%5B".$key6."%5D=".urlencode($value6);
													}
												} else {
													if($qs) $qs.="&".$key."%5B".$key2."%5D%5B".$key3."%5D%5B".$key4."%5D%5B".$key5."%5D=".urlencode($value5); else $qs=$key."%5B".$key2."%5D%5B".$key3."%5D%5B".$key4."%5D%5B".$key5."%5D=".urlencode($value5);
												}
											}
										} else {
											if($qs) $qs.="&".$key."%5B".$key2."%5D%5B".$key3."%5D%5B".$key4."%5D=".urlencode($value4); else $qs=$key."%5B".$key2."%5D%5B".$key3."%5D%5B".$key4."%5D=".urlencode($value4);
										}
									}
								} else {
									if($qs) $qs.="&".$key."%5B".$key2."%5D%5B".$key3."%5D=".urlencode($value3); else $qs=$key."%5B".$key2."%5D%5B".$key3."%5D=".urlencode($value3);
								}
							}
						} else {
							if($qs) $qs.="&".$key."%5B".$key2."%5D=".urlencode($value2); else $qs=$key."%5B".$key2."%5D=".urlencode($value2);
						}
					}
				} else {
					if($qs) $qs.="&".$key."=".urlencode($value); else $qs=$key."=".urlencode($value);
				}
				if($key=="bc") $contains_bc=true;
			}
		}
		if(!$contains_bc) {
			if($qs) $qs.="&bc=".$_GET["bc"]; else $qs="bc=".$_GET["bc"];
		}
#echo htmlentities($qs);		
		if($custom) {
			# Huidige gegevens opslaan
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["file"]=$_SERVER["PHP_SELF"];
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["qs"]=$_SERVER["QUERY_STRING"];
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["title"]=$custom;
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["type"]="custom";
			$_SESSION["CMS2"]["back"]["last"]="custom";
		} elseif($_GET["show"]==$counter and $_GET["edit"]<>$counter) {
			# Show

			# Huidige gegevens opslaan
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["file"]=$_SERVER["PHP_SELF"];
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["qs"]=$qs;
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["title"]=$this->show_name[$counter].($this->show_mainfield_value ? " '".$this->show_mainfield_value."'" : "");
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["type"]="show";
			$_SESSION["CMS2"]["back"]["last"]="show";
			if($_GET["bc"]) {
				if($_SESSION["CMS2"]["back"][$backcounter-1]["title"] and !$terug_naar_getoond[$_SESSION["CMS2"]["back"][$backcounter-1]["title"]]) {
					echo "<a href=\"".$_SESSION["CMS2"]["back"][$backcounter-1]["file"].($_SESSION["CMS2"]["back"][$backcounter-1]["qs"] ? "?".$_SESSION["CMS2"]["back"][$backcounter-1]["qs"] : "")."\">";
#					echo "Van ".$backcounter." TERUG NAAR ".($backcounter-1)." ".htmlentities($_SESSION["CMS2"]["back"][$backcounter-1]["title"])."</a><br>";
					echo $this->message("terugnaarveldnaam",true,array("veldnaam"=>$_SESSION["CMS2"]["back"][$backcounter-1]["title"]))."</a><br>";
					$terug_naar_getoond[$_SESSION["CMS2"]["back"][$backcounter-1]["title"]]=true;
					$echo=true;
				}
			}
		} elseif($_GET["edit"]==$counter or $_GET["add"]==$counter) {
			# Edit
			if($_GET["bc"]) {
				if($_SESSION["CMS2"]["back"][$backcounter-2]["file"] and !$terug_naar_getoond[$_SESSION["CMS2"]["back"][$backcounter-2]["title"]]) {
					echo "<a href=\"".$_SESSION["CMS2"]["back"][$backcounter-2]["file"].($_SESSION["CMS2"]["back"][$backcounter-2]["qs"] ? "?".$_SESSION["CMS2"]["back"][$backcounter-2]["qs"] : "")."\">";
#					echo "Van ".$backcounter." TERUG NAAR ".($backcounter-2)." ".htmlentities($_SESSION["CMS2"]["back"][$backcounter-2]["title"])."</a><br>";
					echo $this->message("terugnaarveldnaam",true,array("veldnaam"=>$_SESSION["CMS2"]["back"][$backcounter-2]["title"]))."</a><br>";
					$terug_naar_getoond[$_SESSION["CMS2"]["back"][$backcounter-2]["title"]]=true;
					$echo=true;
				}
				if($_SESSION["CMS2"]["back"][$backcounter-1]["file"] and  !$terug_naar_getoond[$_SESSION["CMS2"]["back"][$backcounter-1]["title"]]) {
					echo "<a href=\"".$_SESSION["CMS2"]["back"][$backcounter-1]["file"].($_SESSION["CMS2"]["back"][$backcounter-1]["qs"] ? "?".$_SESSION["CMS2"]["back"][$backcounter-1]["qs"] : "")."\">";
#					echo "Van ".$backcounter." TERUG NAAR ".($backcounter-1)." ".htmlentities($_SESSION["CMS2"]["back"][$backcounter-1]["title"])."</a><br>";
					echo $this->message("terugnaarveldnaam",true,array("veldnaam"=>$_SESSION["CMS2"]["back"][$backcounter-1]["title"]))."</a><br>";
					$terug_naar_getoond[$_SESSION["CMS2"]["back"][$backcounter-1]["title"]]=true;
					$echo=true;
				}
			}
			$_SESSION["CMS2"]["back"]["last"]="edit";
		} else {
			# List

			# Huidige gegevens opslaan
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["file"]=$_SERVER["PHP_SELF"];
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["qs"]=$qs;
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["title"]=$this->message("overzichtveldnaam",false,array("veldnaam"=>$this->settings[$counter]["types"]));
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["type"]="list";
			$_SESSION["CMS2"]["back"]["last"]="list";
		}
		if($echo) echo "<hr>";
	}

	function save_backlink_in_session($title) {
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["file"]=$_SERVER["PHP_SELF"];
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["qs"]=$_SERVER["QUERY_STRING"];
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["title"]=$this->message("overzichtveldnaam",false,array("veldnaam"=>$this->settings[$counter]["types"]));
			$_SESSION["CMS2"]["back"][$_GET["bc"]]["type"]="list";
			$_SESSION["CMS2"]["back"]["last"]="custom";
	}

	function strip_query_string($array) {
		@reset($_GET);
		while(list($key,$value)=@each($_GET)) {
			if(!in_array($key,$array)) {
				if($return) $return.="&".$key."=".urlencode($value); else $return=$key."=".urlencode($value);
			}
		}
		return $return;
	}

	function display_show($counter) {
		if(is_array($this->set_show_error[$counter])) {
			echo "<font class=\"wtform_error\">";
			echo "<ul>";
			reset($this->set_show_error[$counter]);
			while(list($key,$value)=each($this->set_show_error[$counter])) {
				echo "<li>".$value."</li>";
			}
			echo "</ul></font><p>";
		} else {
	
			# Naam (mainfield) van het show-record bepalen (geldt voor alle $counters)
			if($this->show_mainfield[$counter] and $this->show_rightcell[$this->show_mainfield[$counter]]) {
				$this->show_mainfield_value=strip_tags(html_entity_decode($this->show_rightcell[$this->show_mainfield[$counter]]));
			}
			$this->back_link($counter);
	
			#
			# edit/print/delete-iconen
			#
			# Link bepalen
	#		$link="&".$counter."mt=".$this->db[$counter]["maintable"]."&bc=".($_GET["bc"]+1).($this->stripped_query_string ? "&".$this->stripped_query_string : "");
			$link="&bc=".($_GET["bc"]+1).($this->stripped_query_string ? "&".$this->stripped_query_string : "");
			if($this->settings[$counter]["show"]["edit_icon"] or $this->settings[$counter]["show"]["print_icon"]) {
				echo "<table><tr>";
				if($this->settings[$counter]["show"]["edit_icon"]) echo "<td><a href=\"".$this->php_self($counter)."edit=".$counter.$link."\"><img src=\"pic/class.cms_edit.gif\" width=\"20\" height=\"20\" alt=\"".$this->message("bewerken",false)."\" border=\"0\"></a></td>";
				echo "<td>&nbsp;</td>";
				if($this->settings[$counter]["show"]["print_icon"]) {
					echo "<td><a href=\"";
					if($this->settings[$counter]["show"]["print_link"]) {
						echo $this->settings[$counter]["show"]["print_link"];
					} else {
						echo $this->php_self($counter)."print=1".$link;
					}
					echo "\"><img src=\"pic/class.cms_print.gif\" width=\"20\" height=\"20\" alt=\"".$this->message("printen",false)."\" border=\"0\"></a></td>";
				}
				echo "</tr></table>";
			}
			if($this->show_header[$counter]) {
				echo "<h2>".htmlentities($this->show_header[$counter])."</h2>";
			}
			
			if($this->show_name[$counter] and $this->show_mainfield[$counter]) {
				echo "<h2>".htmlentities(ucfirst($this->show_name[$counter]))." ".$this->show_rightcell[$this->show_mainfield[$counter]]."</h2>";
			}
			echo "<p><table class=\"wt_cms_show\" cellspacing=\"0\" cellpadding=\"7\">";
			
			while(list($key,$value)=@each($this->show_leftcell)) {
				echo "<tr>";
				if(ereg("^htmlcol_",$key)) {
					echo "<td>".$value."</td><td>".($this->show_rightcell[$key] ? $this->show_rightcell[$key] : "&nbsp;").($this->show[$counter]["layout"][$key]["extratext"] ? $this->show[$counter]["layout"][$key]["extratext"] : "")."</td>";
				} elseif(ereg("^htmlrow_",$key)) {
					echo "<td colspan=\"2\">".$value."</td>";
				} elseif($this->show[$counter]["layout"][$key]["colspan2"]) {
					echo "<td colspan=\"2\"".($this->show[$counter]["layout"][$key]["align"] ? " align=\"".$this->show[$counter]["layout"][$key]["align"]."\"" : "").">".($this->show_rightcell[$key] ? $this->show_rightcell[$key] : "&nbsp;")."</td>";
				} else {
					echo "<td>".$value."</td><td>".($this->db[$counter]["type"][$key]=="currency" ? "&euro; " : "").($this->show_rightcell[$key] ? $this->show_rightcell[$key] : "&nbsp;").($this->show[$counter]["layout"][$key]["extratext"] ? $this->show[$counter]["layout"][$key]["extratext"] : "")."</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
			
			@reset($this->settings[$counter]["connect"]);
			while(list($key,$value)=@each($this->settings[$counter]["connect"])) {
				if(!$this->settings[$value]["list"]["hide"]) {
					echo "<p><hr>";
					if($this->settings[$value]["types"]) echo "<h2>".htmlentities(ucfirst($this->settings[$value]["types"]))."</h2>";
					$this->display_list($value);
				}
			}
		}
	}

	function display_log($counter,$showall=false) {

		$db=new DB_sql;
		$db->query("SELECT UNIX_TIMESTAMP(savedate) AS savedate, user_id, specialtype, field_name, previous, now FROM cmslog WHERE cms_id='".addslashes($counter)."' AND record_id='".addslashes($_GET[$counter."k0"])."' AND hide=0 ORDER BY savedate DESC, cmslog_id DESC;");
		echo "<br><a name=\"displaylog".$counter."\"></a><hr><br>";
		echo "<table cellspacing=\"0\" class=\"tbl logtbl difftbl\">";
		echo "<tr><th colspan=\"5\">Logboek";
		if(!$showall and $_GET["displaylog"]<>$counter and $db->num_rows()>1) {
			echo " - laatste wijziging";
		} elseif($db->num_rows()) {
			echo " - volledig";
		}
		echo "</th></tr>";
		if($db->num_rows()) {
			echo "<tr style=\"font-weight:bold;\"><td style=\"width:150px;\">wijzigdatum</td><td>door</td><td>veld</td><td style=\"width:30%;\">van</td><td style=\"width:30%;\">naar</td></tr>";
			while($db->next_record()) {
				if($db->f("specialtype")==1) {
					echo "<tr><td>".date("d-m-Y, H:i",$db->f("savedate"))."u.</td><td>".htmlentities($this->vars["users"][$db->f("user_id")])."</td><td colspan=\"3\"><i>".$this->message("record_toegevoegd")."</i></td></tr>";
				} else {
					if(strlen($db->f("previous"))>0 and strlen($db->f("now"))>0 and (strlen($db->f("previous"))>10 or strlen($db->f("now"))>10)) {
						$now=nl2br(wt_diff($db->f("previous"),$db->f("now")));
					} else {
						$now=nl2br(htmlentities($db->f("now")));
					}
					echo "<tr><td>".date("d-m-Y, H:i",$db->f("savedate"))."u.</td><td>".htmlentities($this->vars["users"][$db->f("user_id")])."</td><td>".htmlentities($db->f("field_name"))."</td><td>".nl2br(htmlentities($db->f("previous")))."</td><td>".$now."</td></tr>";
				}
				if($_GET["displaylog"]<>$counter and !$showall) {
					break;
				}

			}
			if($_GET["displaylog"]<>$counter and $db->num_rows()>1 and !$showall) {
				echo "<tr><td colspan=\"5\"><br><a href=\"".htmlentities($_SERVER["REQUEST_URI"])."&amp;displaylog=".$counter."#displaylog".$counter."\">Bekijk het volledige logboek &gt;</a> (".$db->num_rows()." regels)</td></tr>";
			}
		} else {
			echo "<tr><td colspan=\"5\"><i>Logboek is nog leeg</i></td></tr>";
		}
		echo "</table>";
	}
	
	function display_cms($width="") {
		# Volledig CMS: opvragen, wijzigen, wissen en toevoegen van gegevens
		
		# De counter op de eerst beschikbare zetten
		reset($this->db);
		while(list($key,$value)=each($this->db)) {
			if(is_array($value["field"])) break;
		}
		if($this->end_declaration) {
			if($_GET["add"]==$key) {
				$this->display_edit($key);
			} elseif($_GET["delete"]==$key and $_GET["confirmed"] and !$this->delete_error[$key]) {
				# niks doen (alles is al via set_delete geregeld)
				
			} elseif($_GET["edit"]==$key) {
				$this->display_edit($key);
			} elseif($_GET["show"]==$key) {
				$this->display_show($key);
			} elseif($_POST["delete_checkbox_filled"]) {
				# wissen via checkboxes
				# niks doen (alles is al via set_delete geregeld)
			} else {
				$this->display_list($key);
			}
			
		} else {
			trigger_error("WT-Error: fullcms called before end_declaration",E_USER_ERROR);
		}
	}

	function end_declaration() {
		# Uit QUERY_STRING ongewenste waardes strippen
		$this->not_in_query_string[]="bc";
		$this->not_in_query_string[]="add";
		$this->not_in_query_string[]="edit";
		$this->not_in_query_string[]="show";
		$this->not_in_query_string[]="delete";
		$this->not_in_query_string[]="confirmed";

		unset($this->stripped_query_string);
		@reset($_GET);
		while(list($key,$value)=@each($_GET)) {
			if(!in_array($key,$this->not_in_query_string) and !ereg("^sort([0-9]+)$",$key)) {
				if($this->stripped_query_string) {
					$this->stripped_query_string.="&".$key."=".@urlencode($value);
				} else {
					$this->stripped_query_string=$key."=".@urlencode($value);
				}
			}
		}
		
		# Doorloop alle $counters (op basis van $this->db[]["field"])
		reset($this->db);
		while(list($key,$value)=each($this->db)) {
			if(is_array($value["field"])) {
				if($_GET["add"]==$key) {
					$this->set_edit($key,true);
					if(!$this->page_title) $this->page_title=$this->message("veldnaamtoevoegen",false,array("veldnaam"=>ucfirst($this->settings[$key]["type_single"])));
					$this->output[$key]["type"]="add";
				} elseif($_GET["delete"]==$key and $_GET["confirmed"] and !$this->delete_error[$key]) {
					$this->set_delete($key);
					$this->output[$key]["type"]="delete";
				} elseif($_POST["delete_checkbox_filled"][$key]==1 and !$this->delete_error[$key]) {
					$this->set_delete($key);
					$this->output[$key]["type"]="delete";
				} elseif($_GET["edit"]==$key) {
					$this->set_edit($key);
					if(!$this->page_title) $this->page_title=ucfirst($this->message("veldnaambewerken",false,array("veldnaam"=>$this->settings[$key]["type_single"])));
					$this->output[$key]["type"]="edit";
				} elseif($_GET["show"]==$key) {
					$this->set_show($key);
					if(!$this->page_title) $this->page_title=ucfirst($this->show_name[$key]);
					$this->output[$key]["type"]="show";
				} else {
					$this->set_list($key);
					if(!$this->page_title) $this->page_title=ucfirst($this->message("overzichtveldnaam",false,array("veldnaam"=>$this->settings[$key]["types"])));
					$this->output[$key]["type"]="list";
				}
			}
		}
		$this->end_declaration=true;
		return true;
	}
}

?>