<?php

class tablelist {

	# Werkend voorbeeld
	/*

	$db->query("SELECT user_id, voornaam, email, lastlogin FROM dla_user;");
	if($db->num_rows()) {
		$tl=new tablelist;
		$tl->settings["systemid"]=1;
		$tl->settings["arrowcolor"]="white";
		$tl->settings["max_results_per_page"]=30;
		$tl->settings["path"]=$vars["path"];
		$tl->settings["resultpages_top"]=true;
		$tl->settings["th_id"]="col_";
		$tl->settings["td_class"]="col_";   # elke cel een class: "deze_voorloper+naam"

		$tl->sort=array("lastlogin","naam","email");
		$tl->sort_desc=true;

		$tl->field_show("show.php?id=[ID]","Details bekijken");
		$tl->field_text("naam","Naam");
		$tl->field_text("email","E-mail");
		$tl->field_text("lastlogin","Laatste login");
		while($db->next_record()) {
			# add_record($id,$key,$value,$sortvalue="",$datetime=false,$options="")
			$tl->add_record("naam",$db->f("user_id"),$db->f("voornaam"));
			$tl->add_record("email",$db->f("user_id"),$db->f("email"));
			$tl->add_record("lastlogin",$db->f("user_id"),date("d-m-Y",$db->f("lastlogin")),$db->f("lastlogin"),true);
		}
		echo $tl->table("tbl",1);
	} else {
		echo "Er zijn geen gebruikers aanwezig in het systeem.";
	}

	*/

	function tablelist() {
		$this->settings["language"]="nl";
		$this->settings["resultsperpage"]=10;
		$this->settings["arrowcolor"]="black";
		$this->settings["systemid"]=1;
		$this->settings["show_index"]=true;
		$this->settings["path"]="";
		$this->settings["resultpages_top"]=false;
		$this->settings["th_id"]="";
		$this->settings["td_class"]="";
		$this->settings["aname_top"]="";

		$this->index_array=array("1"=>"0-9","a"=>"A","b"=>"B","c"=>"C","d"=>"D","e"=>"E","f"=>"F","g"=>"G","h"=>"H","i"=>"I","j"=>"J","k"=>"K","l"=>"L","m"=>"M","n"=>"N","o"=>"O","p"=>"P","q"=>"Q","r"=>"R","s"=>"S","t"=>"T","u"=>"U","v"=>"V","w"=>"W","x"=>"X","y"=>"Y","z"=>"Z");
		return true;
	}

	function convert_firstchar($text) {
		return(strtr($text,
		"AÀÁÂÃÄÅaàáâãäåBbCÇcçDdEÈÉÊËeèéêëFfGgHhIÌÍÎÏiìíîïJjKkLlMmNÑnñOÒÓÔÕÖØoòóôõöøPpQqRrSsšTtUÙÚÛÜùúûüVvWwXxYyÿZz0123456789",
		"aaaaaaaaaaaaaabbccccddeeeeeeeeeeffgghhiiiiiiiiiijjkkllmmnnnnooooooooooooooppqqrrsssttuuuuuuuuuvvwwxxyyyzz1111111111"));
	}

	function newfield($type,$id,$title,$content,$sortcontent,$options,$layout) {
		if(!$this->main_field) $this->main_field=$id;
		if(!$this->set_sort[$this->set_sort_counter+1]) {
			$this->set_sort_counter++;
			$this->set_sort[$this->set_sort_counter]=$id;
		}
		$this->fields["type"][$id]=$type;
		$this->fields["title"][$id]=$title;
		$this->fields["content"][$id]=$content;
		if(!$sortcontent) $sortcontent=$content;
		$this->fields["sortcontent"][$id]=$sortcontent;
		if($options) $this->fields["options"][$id]=$options;
		if($layout) $this->fields["layout"][$id]=$layout;
	}

	function field_delete($url,$confirm,$alttext,$img="",$imgwidth=20,$imgheight=20) {
		if(!$img) $img=$this->settings["path"]."pic/class.cms_delete.gif";
#		$this->delete="<td class=\"tbl_icon\" onmouseover=\"row[ROWCOUNTER].className='row_delete';\" id=\"td[ROWCOUNTER]\" onmouseout=\"row_".$this->settings["systemid"]."[ROWCOUNTER].className='row[ROW_1_OR_2]';\"><a href=\"".$url."\" onclick=\"row[ROWCOUNTER].className='row_edit';return confirmLink(this,'".addslashes($confirm)."')\"><img src=\"".$img."\" alt=\"".wt_he($alttext)."\" width=\"".$imgwidth."\" height=\"".$imgheight."\" border=\"0\"></a></td>";
		$this->delete="<td class=\"tbl_icon\" onmouseover=\"ToggleClass('row_".$this->settings["systemid"]."_[ROWCOUNTER]','row[ROW_1_OR_2]');\" onmouseout=\"ToggleClass('row_".$this->settings["systemid"]."_[ROWCOUNTER]','row[ROW_1_OR_2]');\"><a href=\"".$url."\" onclick=\"return confirmLink('row_".$this->settings["systemid"]."_[ROWCOUNTER]',this,'".addslashes($confirm)."')\"><img src=\"".$img."\" alt=\"".wt_he($alttext)."\" title=\"".wt_he($alttext)."\" width=\"".$imgwidth."\" height=\"".$imgheight."\" border=\"0\"></a></td>";
	}

	function field_delete_checkbox() {
		$this->delete_checkbox="<td class=\"tbl_icon\" onmouseover=\"ToggleClass('row_".$this->settings["systemid"]."_[ROWCOUNTER]','row[ROW_1_OR_2]');\" onmouseout=\"ToggleClass('row_".$this->settings["systemid"]."_[ROWCOUNTER]','row[ROW_1_OR_2]');\" onclick=\"if(document.forms['frm_delete_checkbox_".$this->settings["systemid"]."'].elements['delete_checkbox[".$this->settings["systemid"]."][[ONLY_ID]]'].checked==true) ToggleClass('row_".$this->settings["systemid"]."_[ROWCOUNTER]','row[ROW_1_OR_2]'); else ToggleClass('row_".$this->settings["systemid"]."_[ROWCOUNTER]','row[ROW_1_OR_2]');\"><input type=\"checkbox\" name=\"delete_checkbox[".$this->settings["systemid"]."][[ONLY_ID]]\" value=\"1\" id=\"delete_checkbox[".$this->settings["systemid"]."][[ONLY_ID]]\"></td>";
	}

	function field_edit($url,$alttext,$img="",$imgwidth=20,$imgheight=20) {
		if(!$img) $img=$this->settings["path"]."pic/class.cms_edit.gif";
		$this->edit="<td class=\"tbl_icon\" onmouseover=\"document.getElementById('row_".$this->settings["systemid"]."_[ROWCOUNTER]').className='row_edit';\" onmouseout=\"document.getElementById('row_".$this->settings["systemid"]."_[ROWCOUNTER]').className='row[ROW_1_OR_2]';\"><a href=\"".$url."\"><img src=\"".$img."\" alt=\"".wt_he($alttext)."\" title=\"".wt_he($alttext)."\" width=\"".$imgwidth."\" height=\"".$imgheight."\" border=\"0\"></a></td>";
	}

	function field_show($url,$alttext,$img="",$imgwidth=20,$imgheight=20) {
		if(!$img) $img=$this->settings["path"]."pic/class.cms_show.gif";
		$this->show="<td class=\"tbl_icon\" onmouseover=\"document.getElementById('row_".$this->settings["systemid"]."_[ROWCOUNTER]').className='row_show';\" onmouseout=\"document.getElementById('row_".$this->settings["systemid"]."_[ROWCOUNTER]').className='row[ROW_1_OR_2]';\"><a href=\"".$url."\"><img src=\"".$img."\" alt=\"".wt_he($alttext)."\" title=\"".wt_he($alttext)."\" width=\"".$imgwidth."\" height=\"".$imgheight."\" border=\"0\"></a></td>";
	}

	function field_print($url,$alttext,$img="",$imgwidth=20,$imgheight=20) {
		if(!$img) $img=$this->settings["path"]."pic/class.cms_print.gif";
		$this->print="<td class=\"tbl_icon\" onmouseover=\"document.getElementById('row_".$this->settings["systemid"]."_[ROWCOUNTER]').className='row_print';\" onmouseout=\"document.getElementById('row_".$this->settings["systemid"]."_[ROWCOUNTER]').className='row[ROW_1_OR_2]';\"><a href=\"".$url."\"><img src=\"".$img."\" alt=\"".wt_he($alttext)."\" title=\"".wt_he($alttext)."\" width=\"".$imgwidth."\" height=\"".$imgheight."\" border=\"0\"></a></td>";
	}

	function field_text($id,$title,$content="",$options="",$layout="") {
		$this->newfield("text",$id,$title,$content,$sortcontent,$options,$layout);
	}

	function field_currency($id,$title,$content="",$options="",$layout="") {
		$this->newfield("currency",$id,$title,$content,$sortcontent,$options,$layout);
	}

	function add_url_id($key,$url_id) {
		$this->fields["url_id"][$key]=$url_id;
	}

	function add_record($id,$key,$value,$sortvalue="",$datetime=false,$options="") {
		if(!$sortvalue) $sortvalue=$value;
		$sortvalue=strtolower($sortvalue);
		if($datetime) {
			$sortvalue=substr("00000".adodb_date("Y",$sortvalue),-5).adodb_date("mdHis",$sortvalue);
		} else {
			if(strlen($sortvalue)<12 and ereg("^[0-9\.]+$",$sortvalue)) $sortvalue=substr("000000000000000".$sortvalue,-14);
		}
		if($options["html"]) $this->fields["options"][$id][$key]=$options;

		$this->fields["content"][$id][$key]=$value;
		$this->fields["sortcontent"][$id][$key]=$sortvalue;
	}

	function sort($id) {
		$temp["set_sort"]=$this->set_sort;
	}


	function replace_id_record($text,$id,$javascript=false,$rowcounter=0,$row_1_or_2=0) {
		$return=$text;
		if($this->fields["url_id"][$id]) {
			$return=ereg_replace("\[ID\]",$this->fields["url_id"][$id],$return);
		} else {
			$return=ereg_replace("\[ID\]",urlencode($id),$return);
		}
		$return=ereg_replace("\[ONLY_ID\]",urlencode($id),$return);
		if($javascript) {
			$return=ereg_replace("\[RECORD\]",addslashes(wt_he($this->fields["content"][$this->main_field][$id])),$return);
		} else {
			$return=ereg_replace("\[RECORD\]",wt_he($this->fields["content"][$this->main_field][$id]),$return);
		}
		if($rowcounter) {
			$return=ereg_replace("\[ROWCOUNTER\]",wt_he($rowcounter),$return);
		}
		if($row_1_or_2) {
			$return=ereg_replace("\[ROW_1_OR_2\]",wt_he($row_1_or_2),$return);
		}
		return $return;
	}

	function aantalresultaten_pagina($aantal,$paginas=false) {
		# toon aantal resultaten en pagina's
		if(!$this->settings["max_results_per_page"] or $aantal<=$this->settings["max_results_per_page"]) return;

		if($this->settings["max_results_per_page"] and $aantal>$this->settings["max_results_per_page"]) {
			$van=($this->page-1)*$this->settings["max_results_per_page"]+1;
			$tot=$this->page*$this->settings["max_results_per_page"];
			if($tot>$aantal) $tot=$aantal;
			$return.="Resultaten ".$van." - ".$tot." van ".$aantal."<p>";
		}
		if($paginas) {
			@reset($_GET);
			while(list($key,$value)=@each($_GET)) {
				if($key<>$this->settings["systemid"]."page") {
					if($querystring) $querystring.="&".$key."=".urlencode($value); else $querystring="?".$key."=".urlencode($value);
				}
			}
			if($querystring) {
				$querystring.="&".$this->settings["systemid"]."page=";
			} else {
				$querystring="?".$this->settings["systemid"]."page=";
			}

#			$return.="<p>";
			$maxpage=ceil($aantal/$this->settings["max_results_per_page"]);
			if($maxpage>10) {
				$startpage=$this->page-4;
				$endpage=$this->page+4;
				if($endpage>$maxpage) {
					$endpage=$maxpage;
					$startpage=$endpage-8;
				}
				if($startpage<1) $startpage=1;
				if($maxpage>=9 and $endpage<9) $endpage=9;
			} else {
				$startpage=1;
				$endpage=$maxpage;
			}
			if($this->page>1) $return.="<a href=\"".$querystring.($this->page-1)."\">&lt; vorige</a> - ";
			$return.="pagina ";
			for($i=$startpage;$i<=$endpage;$i++) {
				if($this->page==$i) {
					$return.="<strong>";
				} else {
					$return.="<a href=\"".$querystring.$i."\">";
				}
				$return.=$i;
				if($this->page==$i) {
					$return.="</strong>";
				} else {
					$return.="</a>";
				}

				if($i<$endpage) $return.=" - ";
			}
			if($this->page<$maxpage) $return.=" - <a href=\"".$querystring.($this->page+1)."\">volgende &gt;</a>";
			$return.="<p />";
		}
		if($return) return $return;

	}

	function table($cssclass="tl_table",$counter="",$style="") {

		if($_GET[$counter."page"]) {
			$this->page=$_GET[$counter."page"];
		} else {
			$this->page=1;
		}

		# Sorteren

		# Sorteren op basis van $_GET en $tl->sort=array.... regelen
		$temp["set_sort"]=$this->set_sort;
		unset($this->set_sort);
		# Haal de sort uit $_GET
		@reset($_GET);
		while(list($key,$value)=@each($_GET)) {
			if(ereg("^".$counter."sort([0-9]+)$",$key,$regs)) {
				$this->settings["show_index"]=false;
				$this->set_sort[$regs[1]]=$_GET[$counter."sort".$regs[1]];
			}
		}
		$temp["set_sort_counter"]=count($this->set_sort);

		# Haal de volgorde uit $tl->sort
		@reset($this->sort);
		while(list($key,$value)=@each($this->sort)) {
			$temp["set_sort_counter"]++;
			if(!@in_array($value,$this->set_sort)) $this->set_sort[$temp["set_sort_counter"]]=$value;
		}

		# Haal de sort uit volgorde kolommen
		while(list($key,$value)=each($temp["set_sort"])) {
			$temp["set_sort_counter"]++;
			if(!@in_array($value,$this->set_sort)) $this->set_sort[$temp["set_sort_counter"]]=$value;
		}

		ksort($this->set_sort);
		while(list($key,$value)=@each($this->fields["sortcontent"][$this->set_sort[1]])) {
			reset($this->set_sort);
			while(list($key2,$value2)=each($this->set_sort)) {
				$this->sort_array[$key].=" ".$this->fields["sortcontent"][$value2][$key];
			}
		}
		if($_GET[$counter."desc"] or (!$_GET[$counter."desc"] and !$_GET[$counter."sort1"] and $this->sort_desc)) {
#		if($_GET[$counter."desc"]) {
			@arsort($this->sort_array);
			$this->sort_desc_temp=true;
		} else {
			@asort($this->sort_array);
			$this->sort_desc_temp=false;
		}
		# Weergeven
		if(is_array($this->sort_array)) {

			$return.=$this->aantalresultaten_pagina(count($this->sort_array),$this->settings["resultpages_top"]);

			$return.="<table border=\"0\" cellspacing=\"0\" class=\"".$cssclass."\"".($style ? " style=\"".$style."\"" : "")."><tr>";
			if($this->delete) $return.="<th>&nbsp;</th>";
			if($this->delete_checkbox) $return.="<th style=\"text-align:center;\"><input type=\"checkbox\" onclick=\"wt_check_all_checkboxes_tablelist(this,'".$this->settings["systemid"]."');\"></th>";
			if($this->print) $return.="<th>&nbsp;</th>";
			if($this->edit) $return.="<th>&nbsp;</th>";
			if($this->show) $return.="<th>&nbsp;</th>";
			while(list($key,$value)=each($this->fields["title"])) {
				$return.="<th nowrap".($this->settings["th_id"] ? " id=\"".$this->settings["th_id"].$key."\"" : "").">";
#				$return.="<a href=\"".$_SERVER["PHP_SELF"]."?";
				$return.="<a href=\"".wt_he(ereg_replace("\?.*","",$_SERVER["REQUEST_URI"]))."?";

				reset($_GET);
				unset($temp["get_sort"]);
				while(list($key2,$value2)=each($_GET)) {
					if(!ereg("^".$counter."sort([0-9]+)$",$key2) and $key2<>$counter."desc" and !@in_array($key2,$this->settings["stripget"])) {
						if(is_array($value2)) {
							while(list($key3,$value3)=each($value2)) {
								if(is_array($value3)) {
									while(list($key4,$value4)=each($value3)) {
										$return.=$key2.urlencode("[").$key3.urlencode("]").urlencode("[").$key4.urlencode("]")."=".urlencode($value4)."&";
									}
								} else {
									$return.=$key2.urlencode("[").$key3.urlencode("]")."=".urlencode($value3)."&";
								}
							}
						} else {
							$return.=$key2."=".urlencode($value2)."&";
						}
					}
				}
				$return.=$counter."sort1=".$key."&";
				reset($this->set_sort);
				$temp["sort_counter"]=1;
				while(list($key2,$value2)=each($this->set_sort)) {
					if($value2<>$key) {
						$temp["sort_counter"]++;
						$return.=$counter."sort".$temp["sort_counter"]."=".$value2."&";
					}
				}
				$return=ereg_replace("&$","",$return);
				if($this->set_sort[1]==$key and !$this->sort_desc_temp) $return.="&".$counter."desc=1";
				if($this->settings["aname_top"]) {
					$return.="#".wt_he($this->settings["aname_top"]);
				}
				$return.="\">";
				$return.=wt_he($value);
				$return.="</a>";
				if($this->set_sort[1]==$key) {
					$return.="&nbsp;<img src=\"".$this->settings["path"]."pic/class.tablelist_".($this->sort_desc_temp ? "up" : "down")."_".$this->settings["arrowcolor"].".gif\" alt=\"\">";
				}
				$return.="</th>";
			}
			$return.="</tr>\n";
			while(list($key,$value)=@each($this->sort_array)) {
				$rowcounter++;

				# max results per page
				if($this->settings["max_results_per_page"]) {
					if($rowcounter>$this->settings["max_results_per_page"]*$this->page) {
						break;
					} elseif($this->page>1 and $rowcounter<=$this->settings["max_results_per_page"]*($this->page-1)) {
						continue;
					}
				}

				if($row_color==1) {
					$row_color=0;
#					$return.="<tr class=\"row2\" id=\"row".$rowcounter."\">";
					$return.="<tr class=\"row2\" id=\"row_".$this->settings["systemid"]."_".$rowcounter."\">";
				} else {
					$row_color=1;
#					$return.="<tr class=\"row1\" id=\"row".$rowcounter."\" onmouseover=\"this.className='row_active';\" onmouseout=\"this.className='row1';\">";
					$return.="<tr class=\"row1\" id=\"row_".$this->settings["systemid"]."_".$rowcounter."\">";
				}
				if($this->show) $return.=$this->replace_id_record($this->show,$key,false,$rowcounter,($row_color==1 ? "1" : "2"));
				if($this->edit) $return.=$this->replace_id_record($this->edit,$key,false,$rowcounter,($row_color==1 ? "1" : "2"));
				if($this->print) $return.=$this->replace_id_record($this->print,$key,false,$rowcounter,($row_color==1 ? "1" : "2"));
				if($this->delete) {
					if($this->delete_content[$key]) {
						$return.=$this->replace_id_record("<td class=\"tbl_icon\">".$this->delete_content[$key]."</td>",$key,true,$rowcounter,($row_color==1 ? "1" : "2"));
					} else {
						$return.=$this->replace_id_record($this->delete,$key,true,$rowcounter,($row_color==1 ? "1" : "2"));
					}
				}
				if($this->delete_checkbox) $return.=$this->replace_id_record($this->delete_checkbox,$key,false,$rowcounter,($row_color==1 ? "1" : "2"));

				reset($this->fields["type"]);
				while(list($key2,$value2)=each($this->fields["type"])) {
					if($this->settings["show_index"] and $this->fields["options"][$key2]["index_field"]) {
						$this->show_index=true;
						$aname=$this->convert_firstchar(substr($this->fields["content"][$key2][$key],0,1));
#						echo substr($this->fields["content"][$key2][$key],0,1)."==".$aname."<br>";
#						$aname=array_search(strtolower(substr($this->fields["content"][$key2][$key],0,1)),$this->index_array);
						if($aname and !$aname_geplaatst[$aname]) {
							$aname_in_td="<a name=\"".wt_he($aname)."\"></a>";
							$aname_geplaatst[$aname]=true;
						} else {
							$aname_in_td="";
						}
					}
					unset($tdclass);
					if($this->settings["td_class"]) {
						$tdclass=$this->settings["td_class"].$key2;
					}
					if($this->fields["layout"][$key2]["class"]) {
						if($tdclass) {
							$tdclass=" ".$this->fields["layout"][$key2]["class"];
						} else {
							$tdclass=$this->fields["layout"][$key2]["class"];
						}
					}
					if($this->fields["type"][$key2]=="currency") {
						$return.="<td".($tdclass ? " class=\"".$tdclass."\"" : "")." style=\"text-align:right\">".$aname_in_td;
						if(ereg("^[0-9\.,]+$",$this->fields["content"][$key2][$key])) {
							$this->fields["content"][$key2][$key]=ereg_replace(",",".",$this->fields["content"][$key2][$key]);
							$return.="&euro;&nbsp;".number_format($this->fields["content"][$key2][$key],2,",",".");
						} else {
							$return.=$this->fields["content"][$key2][$key];
						}
						$return.="</td>";
					} else {
						$return.="<td".($tdclass ? " class=\"".$tdclass."\"" : "").">".$aname_in_td;
						if($this->fields["content"][$key2][$key] or $this->fields["content"][$key2][$key]=="0") {
							if($this->fields["options"][$key2][$key]["html"]) {
								$return.=$this->fields["content"][$key2][$key];
							} else {
								$return.=wt_he($this->fields["content"][$key2][$key]);
							}
						} else {
							$return.="&nbsp;";
						}
						$return.="</td>";
					}
				}
				$return.="</tr>\n";
			}
			$return.="</table>";

			$return.="<p>".$this->aantalresultaten_pagina(count($this->sort_array),true);

			if($this->settings["show_index"] and $this->show_index and $rowcounter>=50) {
				while(list($key,$value)=each($this->index_array)) {
					if($aname_geplaatst[$key]) {
						$return2.="<a href=\"#".$key."\">".$value."</a>";
					} else {
						$return2.=$value;
					}
					$show_index_counter++;
					if($show_index_counter<count($this->index_array)) $return2.=" - ";
				}
				$return=$return2."<p>".$return;
			}
		}
		return $return;
	}

	function end_declaration() {
		return true;
	}
}

?>