<?php

class search {

	/*
	
	#
	# Voorbeeld:
	# 
	$search=new search;
	$search->wordsplit($_GET["search"]);
	$andquery=" AND ".$search->regexpquery(array("ft.subject","u.voornaam"));
	
	*/
	
	function search() {
		$this->settings["language"]="nl";
		$this->settings["delete_ignorewords"]=true;
		$this->settings["ignorewords"]["nl"]=array("de","het","een","in");
		$this->settings["ignorewords"]["en"]=array("the","in","and","of","by");
		$this->settings["only_whole_words"]=true;
		$this->settings["links_via_clickresult.php"]=true;
		$this->settings["allow_multiple_word_search"]=false;
		$this->settings["allow_search_with_quotes"]=false;
		$this->settings["resultsperpage"]=10;
		$this->settings["external_target_blank"]=false;
		$this->settings["addaccents"]=true;
		$this->settings["withdots"]=true;

		$this->settings["weight"]["title"]=30;
		$this->settings["weight"]["keywords"]=30;
		$this->settings["weight"]["body"]=10;
		$this->settings["hr"]="<hr>";
		$this->settings["message"]["zoek"]["nl"]="ZOEK";
		$this->settings["message"]["zoek"]["en"]="SEARCH";

		$this->settings["message"]["geenresultaten"]["html"]=false;
		$this->settings["message"]["geenresultaten"]["nl"]="Uw zoekopdracht heeft geen resultaten opgeleverd.";
		$this->settings["message"]["geenresultaten"]["en"]="Your search did not match any documents.";
		$this->settings["message"]["resultaatpagina"]["nl"]="Resultaatpagina";
		$this->settings["message"]["resultaatpagina"]["en"]="Result Page";
		$this->settings["message"]["volgende"]["nl"]="Volgende";
		$this->settings["message"]["volgende"]["en"]="Next";
		$this->settings["message"]["vorige"]["nl"]="Vorige";
		$this->settings["message"]["vorige"]["en"]="Previous";
		list($usec,$sec)=explode(" ",microtime());
		$this->starttime=((float)$usec+(float)$sec);
		if(!session_id()) session_start();
		if($_SERVER["HTTP_REFERER"] and !$_SESSION["wt_search"]["referer"]) $_SESSION["wt_search"]["referer"]=$_SERVER["HTTP_REFERER"];
		return true;
	}

	function runtime() {
		list($usec,$sec)=explode(" ", microtime()); 
		$end=((float)$usec+(float)$sec); 
		return $end-$this->starttime;
	}
	
	function message($title,$html=false) {
		if($html) {
			return $this->settings["message"][$title][$this->settings["language"]];
		} else {
			return wt_he($this->settings["message"][$title][$this->settings["language"]]);		
		}
	}
	
	function form() {
		$return="<form method=\"get\" action=\"".$_SERVER["PHP_SELF"].($_SERVER["QUERY_STRING"] ? "?".$_SERVER["QUERY_STRING"] : "")."\">";
		$return.="<input type=\"text\" class=\"wts_input\" ".($_GET["q"] ? "value=\"".wt_he($_GET["q"])."\" " : "")."name=\"q\">&nbsp;&nbsp;";
		$return.="<input type=\"submit\" class=\"wts_submit\" value=\" ".$this->message("zoek")." \">";
		$return.="</form>";
		return $return;
	}

	function dynamic_page($url,$title,$text,$keywords='') {
		$this->result["title"][$url]=$title;
		$this->result["text"][$url]=$text;
		@reset($this->wordsplit);
		while(list($key,$value)=@each($this->wordsplit)) {
			$weight=$this->word_occurrence($value,$title)*$this->settings["weight"]["title"];
			if($weight) $this->result["weight"][$url]+=$weight;
			
			$weight=$this->word_occurrence($value,$text)*$this->settings["weight"]["body"];
			if($weight) $this->result["weight"][$url]+=$weight;
			
			$weight=$this->word_occurrence($value,$keywords)*$this->settings["weight"]["keywords"];
			if($weight) $this->result["weight"][$url]+=$weight;
		}
	}

	function static_page($url,$title,$file,$keywords='') {
		$text=file_get_contents($file);
		$text=$this->striphtml($text);
		$this->result["title"][$url]=$title;
		$this->result["text"][$url]=$text;
		@reset($this->wordsplit);
		while(list($key,$value)=@each($this->wordsplit)) {
			$weight=$this->word_occurrence($value,$title)*$this->settings["weight"]["title"];
			if($weight) $this->result["weight"][$url]+=$weight;
			
			$weight=$this->word_occurrence($value,$text)*$this->settings["weight"]["body"];
			if($weight) $this->result["weight"][$url]+=$weight;
			
			$weight=$this->word_occurrence($value,$keywords)*$this->settings["weight"]["keywords"];
			if($weight) $this->result["weight"][$url]+=$weight;
		}
	}
	
	function search_pages() {
		global $db0;
		@reset($this->wordsplit);
		while(list($key,$value)=@each($this->wordsplit)) {
			$words[$key]=$value;
			$value=$this->wildcards_min($value);
			if($value["not"]) $this->wordcount=$this->wordcount-1;
			if(is_object($db0) and $value["word"]) {
				$db0->query("SELECT word, page_id, weight FROM search_word WHERE word REGEXP '".($this->settings["only_whole_words"] ? "[[:<:]]" : "").$value["word"].($this->settings["only_whole_words"] ? "[[:>:]]" : "")."';");
#				echo $db0->lastquery;
				while($db0->next_record()) {
					if($value["not"]) {
						$notpage[$db0->f("page_id")]=true;
					} else {
						$weight[$db0->f("page_id")]+=$db0->f("weight");
						if(!$word_counted[$db0->f("page_id")."_".$value["word"]]) {
							$pagewordcount[$db0->f("page_id")]++;
							$word_counted[$db0->f("page_id")."_".$value["word"]]=true;
						}
					}
				}
			}
		}
		
		while(list($key,$value)=@each($weight)) {
			if($pagewordcount[$key]==$this->wordcount) {
				if(!$notpage[$key]) {
					if($inquery) $inquery.=",".$key; else $inquery=$key;
					$showresult_weight[$key]=$weight[$key];
				}
			}
		}
		if($inquery) {
			if(is_object($db0)) {
				$db0->query("SELECT page_id, url, title, text FROM search_page WHERE page_id IN (".$inquery.");");
				while($db0->next_record()) {
					$this->result["title"][$db0->f("url")]=$db0->f("title");
					$this->result["text"][$db0->f("url")]=$db0->f("text");
					$this->result["weight"][$db0->f("url")]=$showresult_weight[$db0->f("page_id")];
				}
			}
		}
		if(!$_GET["page"]) $_GET["page"]=1;
		if(is_array($this->result)) {
			@arsort($this->result["weight"]);
			while(list($key,$value)=@each($this->result["weight"])) {
				$resultcounter++;
				if($resultcounter>$this->settings["resultsperpage"]*($_GET["page"]-1) and $resultcounter<=$this->settings["resultsperpage"]*$_GET["page"]) {
					if($this->settings["links_via_clickresult.php"]) {
						echo "<a href=\"click_result.php?url=".urlencode($key)."&t=".urlencode($this->result["title"][$key])."\"".($this->settings["external_target_blank"]&&ereg("^http",$key) ? " target=\"_blank\"" : "").">";
					} else {
						echo "<a href=\"".wt_he($key)."\"".($this->settings["external_target_blank"]&&ereg("^http",$key) ? " target=\"_blank\"" : "").">";
					}
					echo $this->highlight($this->result["title"][$key],$this->wordsplit)."</a>";
#					echo " <font size=1>(score ".$value.")</font>";
					echo "<br>".$this->findwords($this->result["text"][$key],$this->wordsplit);
#					if($resultcounter<$this->settings["resultsperpage"]*$_GET["page"]) echo $this->settings["hr"];
					echo $this->settings["hr"];
				}
			}
			if(count($this->result["weight"])>$this->settings["resultsperpage"]) {
				echo $this->message("resultaatpagina").": ";
				$pages=ceil(count($this->result["weight"])/$this->settings["resultsperpage"]);
				if($_GET["page"]>1) echo "<a href=\"".$_SERVER["PHP_SELF"]."?q=".$_GET["q"]."&page=".($_GET["page"]-1)."\">&lt; ".$this->message("vorige")."</a> - ";
				for($i=1;$i<=$pages;$i++) {
					if($i<>$_GET["page"]) echo "<a href=\"".$_SERVER["PHP_SELF"]."?q=".$_GET["q"]."&page=".$i."\">";
					echo $i;
					if($i<>$_GET["page"]) echo "</a>";
					if($i<$pages) echo " - ";
				}
				if($_GET["page"]<$pages) echo " - <a href=\"".$_SERVER["PHP_SELF"]."?q=".$_GET["q"]."&page=".($_GET["page"]+1)."\">".$this->message("volgende")." &gt;</a>";
			}
		} else {
			if($this->settings["message"]["geenresultaten"]["html"]) {
				echo $this->message("geenresultaten",true);
			} else {
				echo $this->message("geenresultaten");
			}
		}
		$this->save_query($_GET["q"],"",$resultcounter);
	}

	function cutline($text,$chars,$dots=true) {
		$text=trim($text);
		if(strlen($text)<=$chars) {
			return $text;
		} else {
			$text=substr($text,0,$chars);
			$text=split("\ ",$text);
			while(list($key,$value)=each($text)) {
				$count++;
				if(count($text)>$count) $return.=$value." ";
			}
			return trim($return).($dots ? "..." : "");
		}
	}
	
	function addaccents($value,$mysql=false) {
		if($mysql) {
			$value=eregi_replace("a","(a|ä|å|à|á|â|ã)",$value);
			$value=eregi_replace("e","(e|ë|è|é|ê)",$value);
			$value=eregi_replace("i","(i|ï|ì|í|î)",$value);
			$value=eregi_replace("o","(o|ö|ó|ò|ô|õ|ø)",$value);
			$value=eregi_replace("u","(u|ü|ù|ú|û)",$value);
			$value=eregi_replace("n","(n|ñ)",$value);
			$value=eregi_replace("c","(c|ç)",$value);
		} else {
			$value=eregi_replace("a","[a|ä|å|à|á|â|ã]",$value);
			$value=eregi_replace("e","[e|ë|è|é|ê]",$value);
			$value=eregi_replace("i","[i|ï|ì|í|î]",$value);
			$value=eregi_replace("o","[o|ö|ó|ò|ô|õ|ø]",$value);
			$value=eregi_replace("u","[u|ü|ù|ú|û]",$value);
			$value=eregi_replace("n","[n|ñ]",$value);
			$value=eregi_replace("c","[c|ç]",$value);
		}
		return $value;
	}
	
	function findwords($string,$wordarray) {
		if(is_array($wordarray)) {
			reset($wordarray);
			$originalstring=$string;
			while(list($key,$value)=each($wordarray)) {
				$value=$this->addaccents($this->htmlent(trim($value)));
				$value=eregi_replace("\?","[0-9a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš]?",$value);
				$value=eregi_replace("\*","[0-9a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš]*",$value);
				if($words) $words.="|".$value; else $words=$value;
			}
	
			$wordocc=$this->word_occurrence($words,$string);
			if($wordocc) $wordsleftright=round(20/$wordocc)-2;
			if($wordsleftright<3) $wordsleftright=3;
			while(strlen($return)<180) {
				unset($return);
				$string=$originalstring;
				$p="/(([^[:space:]]+[[:space:]]){0,".$wordsleftright."}?[^[:space:]]*)[[:space:]]?\b(".$words.")\b[[:space:]]?(([^[:space:]]+[[:space:]]){0,".$wordsleftright."}[^[:space:]]*)/i";
				$count2=0;
				while(preg_match($p,$string,$matches) and strlen($return)<200) {
					$string=substr($string,strpos($string,$matches[0])+strlen($matches[0]));
					$return.="...".trim($matches[0]);
					$count2++;
					if($count2>3) break;
				}
				$wordsleftright+=3;
				$count1++;
				if($count1>3) break;
			}
		}
		if(!$return) $return=ucfirst($this->cutline($originalstring,175));
		return $this->highlight($return."...",$wordarray);
	}
	
	function htmlent($string) {
		# Een GOED werkende htmlentities
		return ereg_replace("€","&euro;",wt_he($string));
	}
	
	function highlight($string,$wordarray) {
		if(is_array($wordarray)) {
			reset($wordarray);
			$string2=$string;
			while(list($key,$value) = each($wordarray)) {
#				$value=$this->addaccents($this->htmlent(trim($value)));
				$value=$this->addaccents(trim($value));
				$value=eregi_replace("\?","[0-9a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš]?",$value);
				$value=eregi_replace("\*","[0-9a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš]*",$value);
				while(eregi("([^a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš=]|^)($value)([^a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš=]|$)",$string2)) {
					$string2=eregi_replace("([^a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš=]|^)($value)([^a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš=]|$)","\\1=BeGiNWTBoLD=\\2=SToPWTBoLD=\\3",$string2);
				}
			}
			$string2=ereg_replace("=SToPWTBoLD=","</B>",ereg_replace("=BeGiNWTBoLD=","<B>",$this->htmlent($string2)));
			if($string2) return $string2; else return $this->htmlent($string);
		} else return $this->htmlent($string);
	}
	
	function word_occurrence($word,$phrase) {
		$word = trim(strtolower($word));
		$phrase = strtolower($phrase);
		$Bits = @preg_split("/\b".$word."\b/i", $phrase);
		return (@count($Bits)-1);
	}

	function wildcards_min($word) {
		if(substr($word,0,1)=="-") {
			$word=substr($word,1);
			$return["not"]=true;
		} else {
			$return["not"]=false;
		}
		if($this->settings["addaccents"]) {
			$word=$this->addaccents($word,true);
		}
		$word=eregi_replace("\*","[0-9a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš]*",$word);
		$word=eregi_replace("\?","[0-9a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš]?",$word);
		$return["word"]=$word;
		return $return;
	}
	
	function regexpquery($fields,$words="") {
		if(is_array($words)) {
			$doorzoek_words=$words;
		} else {
			$doorzoek_words=$this->wordsplit;
		}
		@reset($doorzoek_words);
		while(list($key,$value)=@each($doorzoek_words)) {
			$value=$this->wildcards_min($value);
			@reset($fields);
			unset($regexpquery);
			while(list($key2,$value2)=each($fields)) {
				if($regexpquery) {
					$regexpquery.="\n ".($value["not"] ? "AND" : "OR")."\n (lower(".$value2.") ".($value["not"] ? "NOT " : "")."REGEXP '".($this->settings["only_whole_words"] ? "[[:<:]]" : "").strtolower($value["word"]).($this->settings["only_whole_words"] ? "[[:>:]]" : "")."')";
				} else {
					$regexpquery="\n(lower(".$value2.") ".($value["not"] ? "NOT " : "")."REGEXP '".($this->settings["only_whole_words"] ? "[[:<:]]" : "").strtolower($value["word"]).($this->settings["only_whole_words"] ? "[[:>:]]" : "")."')";
				}
				if($this->settings["withdots"]) {
					$with_dots=ereg_replace("([\)bdfghjklmpqrstvwxyz0123456789])","\\1\\\.",$value["word"]);
					$regexpquery.=" ".($value["not"] ? "AND" : "OR")." lower(".$value2.") ".($value["not"] ? "NOT " : "")."REGEXP '".($this->settings["only_whole_words"] ? "[[:<:]]" : "").strtolower($with_dots).($this->settings["only_whole_words"] ? "[[:>:]]" : "")."'";
				}
			}
			if($return) $return.=" AND (".$regexpquery.")"; else $return="(".$regexpquery.")";
		}
		return $return;
	}
	
	function save_query($textquery,$selectquery="",$results=0) {
		global $db0;
		if(is_array($selectquery)) {
			while(list($key,$value)=each($selectquery)) {
				if($save_selectquery) $save_selectquery.=" - ".$key.": ".$value; else $save_selectquery=$key.": ".$value;
			}
		} else {
			$save_selectquery=$selectquery;
		}
		if($this->query_starttime) {
			list($usec,$sec)=explode(" ",microtime());
			$this->query_stoptime=((float)$usec+(float)$sec);
			$this->query_searchtime=sprintf('%01.2f',$this->query_stoptime-$this->query_starttime);
		}
		$totalquery=$textquery." - ".$save_selectquery;
		if(($textquery and $_SESSION["wt_search"]["textquery"]<>$textquery) or ($save_selectquery and $_SESSION["wt_search"]["selectquery"]<>$save_selectquery)) {
			if(is_object($db0)) {
				$db0->query("INSERT INTO search_query SET datetime='".time()."', textquery='".addslashes($textquery)."', selectquery='".addslashes($save_selectquery)."', host='".$_SERVER["REMOTE_ADDR"]."', results='".$results."', searchtime='".$this->query_searchtime."', referer='".addslashes($_SERVER["HTTP_REFERER"])."', user_agent='".addslashes($_SERVER["HTTP_USER_AGENT"])."';");
			}
			$_SESSION["wt_search"]["textquery"]=$textquery;
			$_SESSION["wt_search"]["selectquery"]=$save_selectquery;
			$_SESSION["wt_search"]["time"]=time();
		}
	}

	function click_result() {
		global $db0;
		if($_GET["url"]) {
			header("Location: ".$_GET["url"]);
		} else {
			header("Location: /");
		}
		if($_GET["url"] and !ereg("^https?://",$_GET["url"])) $url=wt_baseurl().$_GET["url"]; else $url=$_GET["url"];
		if($_GET["t"]) $url.="	".$_GET["t"];
		$url.="\n";
		if(($_SESSION["wt_search"]["textquery"] or $_SESSION["wt_search"]["selectquery"]) and $_SESSION["wt_search"]["time"]) {
			$db0->query("UPDATE search_query SET clicked=CONCAT(clicked,'".addslashes($url)."') WHERE textquery='".addslashes($_SESSION["wt_search"]["textquery"])."' AND selectquery='".addslashes($_SESSION["wt_search"]["selectquery"])."' AND host='".$_SERVER["REMOTE_ADDR"]."' AND datetime='".addslashes($_SESSION["wt_search"]["time"])."' AND clicked NOT REGEXP '".addslashes($url)."$';");
		}
	}
	
	function query_starttime() {
		list($usec,$sec)=explode(" ",microtime()); 
		$this->query_starttime=((float)$usec+(float)$sec); 
	}
	
	function show_stats() {
		echo "Statistieken worden gegenereerd....";
		ob_flush();
	}
	
	function wordsplit($words) {
		$words=strtolower($words);
		$words=ereg_replace(" \?$"," ",$words);
		$words=ereg_replace(" \? "," ",$words);
		$words=ereg_replace("[',+.&;\/\(\)]"," ",$words);
		$words=ereg_replace("\["," ",$words);
		$words=ereg_replace("\]"," ",$words);
		$words=ereg_replace("([0-9a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš])-([0-9a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš])?","\\1 \\2",$words);
		$words=ereg_replace("[[:blank:]".chr(160)."]+"," ",$words);

		if($this->settings["allow_search_with_quotes"]) {
			while(preg_match("/\"([0-9a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš ]+)\"/",$words,$regs)) {
				$whileteller++;
				$replace=preg_replace("/ /","_WT_BLANK_SEARCH_WITH_QUOTES_",$regs[1]);
				$words=str_replace("\"".$regs[1]."\"",$replace,$words);
				if($whileteller>100) break;
			}
		}
		
		if($this->settings["delete_ignorewords"]) {
			$skipwords=$this->settings["ignorewords"][$this->settings["language"]];
		}
		if($this->settings["allow_multiple_word_search"]) {
			$words=ereg_replace("\"([0-9a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš]+) ([0-9a-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš]+)\"","\\1_WT_BLANK_MULTIPLE_WORD_\\2",$words);
		}
		$wordsplit=split(" ",trim($words));
		while(list($key,$value)=each($wordsplit)) {
			if($this->settings["allow_multiple_word_search"]) {
				$value=ereg_replace("_WT_BLANK_MULTIPLE_WORD_"," ",$value);
			}
			if($this->settings["allow_search_with_quotes"]) {
				$value=ereg_replace("_WT_BLANK_SEARCH_WITH_QUOTES_"," ",$value);
			}
			if(!@in_array($value,$skipwords) and ereg("[0-9a-zA-ZÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñš]",$value)) {
				$this->wordsplit[$key]=$value;
			}
		}
		$this->wordcount=count($this->wordsplit);
	}
	
	
	function html2text($text) {
		$spec = array(	
			"&#34" => "\"",
				"&quot" => "\"",
			"&#35" => "#",
			"&#36" => "$",
			"&#37" => "%",
			"&#38" => "&",
				"&amp" => "&",
			"&#39" => "'",
			"&#40" => "(",
			"&#41" => ")",
			"&#42" => "*",
			"&#43" => "+",
			"&#44" => ",",
			"&#45" => "-",
				"&ndash" => "-",
			"&#46" => ".",
			"&#47" => "/",
			"&#48" => "0",
	
			"&#57" => "9",
			"&#58" => ":",
			"&#59" => ";",
			"&#60" => "<",
				"&lt" => "<",
			"&#61" => "=",
			"&#62" => ">",
				"&gt" => ">",
			"&#63" => "?",
			"&#64" => "@",
			"&#65" => "A",
	
			"&#90" => "Z",
			"&#91" => "[",
			"&#92" => "\\",
			"&#93" => "]",
			"&#94" => "^",
			"&#95" => "_",
			"&#96" => "`",
			"&#97" => "a",
	
			"&#122" => "z",
			"&#123" => "{",
			"&#124" => "|",
			"&#125" => "}",
			"&#126" => "~",
			"&#127" => "",
			"&#128" => "€",
				"&#8364" => "€",
				"&euro" => "€",
			"&#129" => "",
			"&#130" => "‚",
			"&#131" => "ƒ",
			"&#132" => "„",
			"&#133" => "…",
			"&#134" => "†",
			"&#135" => "‡",
			"&#136" => "ˆ",
			"&#137" => "‰",
			"&#138" => "Š",
			"&#139" => "‹",
			"&#140" => "Œ",
			"&#141" => "",
			"&#142" => "",
			"&#143" => "",
			"&#144" => "",
			"&#145" => "‘",
			"&#146" => "’",
			"&#147" => "“",
			"&#148" => "”",
			"&#149" => "•",
			"&#150" => "–",
			"&#151" => "—",
			"&#152" => "˜",
			"&#153" => "™",
			"&#154" => "š",
			"&#155" => "›",
			"&#156" => "œ",
			"&#157" => "",
			"&#158" => "",
			"&#159" => "Ÿ",
			"&#160" => " ",
				"&nbsp" => " ",
			"&#161" => "¡",
				"&iexcl" => "¡",
			"&#162" => "¢",
				"&cent" => "¢",
			"&#163" => "£",
				"&pound" => "£",
			"&#164" => "¤",
				"&curren" => "¤",
			"&#165" => "¥",
				"&yen" => "¥",
			"&#166" => "¦",
				"&brvbar" => "¦",
			"&#167" => "§",
				"&sect" => "§",
			"&#168" => "¨",
				"&copy" => "©",
			"&#169" => "©",
			"&#170" => "ª",
				"&ordf" => "ª",
			"&#171" => "«",
				"&laquo" => "«",
			"&#172" => "¬",
				"&not" => "¬",
			"&#173" => "­",
				"&shy" => "­",
			"&#174" => "®",
				"&reg" => "®",
			"&#175" => "¯",
				"&hibar" => "¯",
			"&#176" => "°",
				"&deg" => "°",
			"&#177" => "±",
				"&plusmn" => "±",
			"&#178" => "²",
				"&sup2" => "²",
			"&#179" => "³",
				"&sup3" => "³",
			"&#180" => "´",
				"&acute" => "´",
			"&#181" => "µ",
				"&micro" => "µ",
			"&#182" => "¶",
				"&para" => "¶",
			"&#183" => "·",
				"&middot" => "·",
			"&#184" => "¸",
				"&cedil" => "¸",
			"&#185" => "¹",
				"&sup1" => "¹",
			"&#186" => "º",
				"&ordm" => "º",
			"&#187" => "»",
				"&raquo" => "»",
			"&#188" => "¼",
				"&frac14" => "¼",
			"&#189" => "½",
				"&frac12" => "½",
			"&#190" => "¾",
				"&frac34" => "¾",
			"&#191" => "¿",
				"&iquest" => "¿",
			"&#192" => "À",
				"&Agrave" => "À",
			"&#193" => "Á",
				"&Aacute" => "Á",
			"&#194"  => "Â",
				"&Acirc" => "Â",
			"&#195" => "Ã",
				"&Atilde" => "Ã",
			"&#196" => "Ä",
				"&Auml" => "Ä",
			"&#197" => "Å",
				"&Aring" => "Å",	
			"&#198" => "Æ",
				"&Aelig" => "Æ",
			"&#199" => "Ç",
				"&Ccedil" => "Ç",
			"&#200" => "Ê",
				"&Ecirc" => "Ê",
			"&#201" => "É",
				"&Eacute" => "É",
			"&#202" => "È",
				"&Egrave" => "È",
			"&#203" => "Ë",
				"&Euml" => "Ë",
			"&#204" => "Ì",
				"&Igrave" => "Ì",
			"&#205" => "Í",
				"&Iacute" => "Í",
			"&#206" => "Î",
				"&Icirc" => "Î",
			"&#207" => "Ï",
				"&Iuml" => "Ï",
			"&#208" => "Ğ",
				"&Dstrok" => "Ğ",
			"&#209" => "Ñ",
				"&Ntilde" => "Ñ",
			"&#210" => "Ò",
				"&Ograve" => "Ò",
			"&#211" => "Ó",
				"&Oacute" => "Ó",
			"&#212" => "Ô",
				"&Ocirc" => "Ô",
			"&#213" => "Õ",
				"&Otilde" => "Õ",
			"&#214" => "Ö",
				"&Ouml" => "Ö",
			"&#215" => "×",
			"&#216" => "Ø",
				"&Oslash" => "Ø",
			"&#217" => "Ù",
				"&Ugrave" => "Ù",
			"&#218" => "Ú",
				"&Uacute" => "Ú",
			"&#219" => "Û",
				"&Ucirc" => "Û",
			"&#220" => "Ü",
				"&Uuml" => "Ü",
			"&#221" => "İ",
				"&Yacute" => "İ",
			"&#222" => "Ş",
				"&THORN" => "Ş",
			"&#223" => "ß",
				"&szlig" => "ß",
			"&#224" => "à",
				"&agrave" => "à",
			"&#225" => "á",
				"&aacute" => "á",
			"&#226"  => "â",
				"&acirc" => "â",
			"&#227" => "ã",
				"&atilde" => "ã",
			"&#228" => "ä",
				"&auml" => "ä",
			"&#229" => "å",
				"&aring" => "å",	
			"&#230" => "æ",
				"&aelig" => "æ",
			"&#231" => "ç",
				"&ccedil" => "ç",
			"&#232" => "è",
				"&egrave" => "è",
			"&#233" => "é",
				"&eacute" => "é",
			"&#234" => "ê",
				"&ecirc" => "ê",
			"&#235" => "ë",
				"&euml" => "ë",
			"&#236" => "ì",
				"&igrave" => "ì",
			"&#237" => "í",
				"&iacute" => "í",
			"&#238" => "î",
				"&icirc" => "î",
			"&#239" => "ï",
				"&iuml" => "ï",
			"&#240" => "ğ",
				"&eth" => "ğ",
			"&#241" => "ñ",
				"&ntilde" => "ñ",
			"&#242" => "ò",
				"&ograve" => "ò",
			"&#243" => "ó",
				"&oacute" => "ó",
			"&#244" => "ô",
				"&ocirc" => "ô",
			"&#245" => "õ",
				"&otilde" => "õ",
			"&#246" => "ö",
				"&ouml" => "ö",
			"&#247" => "÷",
			"&#248" => "ø",
				"&oslash" => "ø",
			"&#249" => "ù",
				"&ugrave" => "ù",
			"&#250" => "ú",
				"&uacute" => "ú",
			"&#251" => "û",
				"&ucirc" => "û",
			"&#252" => "ü",
				"&uuml" => "ü",
			"&#253" => "ı",
				"&yacute" => "ı",
			"&#254" => "ş",
				"&thorn" => "ş",
			"&#255" => "ÿ",
				"&yuml" => "ÿ",
			"&apos" => "'",
			);
		while ($char = each($spec)) {
			$text = ereg_replace ($char[0]."[;]?",$char[1],$text);
		}
		#
		#
		# Volgende regel nodig????
		#
		#
		$text = ereg_replace('&#([0-9]+);',chr('\1').' ',$text);
		$text=strtr($text,	"’•",
					"'-");
		return $text;
	}
	
	function striphtml($html,$deletehreftext=false) {
		//replace blank characters by spaces
		$text = ereg_replace("[\r\n\t]+"," ",$html);
		$html=$text;
		# Grab image-alt-tags
		while(eregi("alt=[[:blank:]]*[\'\"][[:blank:]]*([ a-z0-9\xc8-\xcb]+)[[:blank:]]*[\'\"]",$html,$regs)) {
			$html = str_replace($regs[0],"",$html);
			$imagealt .= " ".$regs[1];
		}
		# Delete content of head, script, and style tags
		$text = preg_replace("'<head[^>]*?>.*?</head>'si"," ",$text);
		$text = preg_replace("'.*?<body[^<>]*>'si"," ",$text,1);
	
		# Tags die niet door een spatie moeten worden vervangen verwijderen
		$text = eregi_replace("</span>","",$text);
		$text = eregi_replace("</font>","",$text);
	
		$text = preg_replace("'<!--*?.*?-->'si"," ",$text);
		$text = preg_replace("'<script[^>]*?>.*?</script>'si"," ",$text);
		$text = preg_replace("'<style[^>]*?>.*?</style>'si"," ",$text);
		if($deletehreftext) $text = preg_replace("'<a [^>]*?>.*?</a>'si","",$text);
	
		# Delete select-fields
		$text = preg_replace("'<option[^>]*?>.*?</option>'si","",$text);
		$text = preg_replace("'<select[^>]*?>.*?</select>'si","",$text);
	
		$text = eregi_replace("(<[a-z0-9 ]+>)","\\1 ",eregi_replace("(</[a-z0-9 ]+>)","\\1 ",$text));
	
#		$imagealt=$this->html2text($imagealt);
		$text=$this->html2text($text);
	
		# replace blank characters by spaces
		$text = ereg_replace("[\r\n\t]+"," ",$text);
		$text = eregi_replace("--|[|{}();\"]+"," ",eregi_replace("</[a-z0-9]+>"," ",$text));
	#	$text = ereg_replace("(^|[[:blank:]])([^[:alnum:]])($|[[:blank:]])"," ",$text);
	
		//replace any group of blank characters by a unique space
		$text = ereg_replace("[[:blank:]".chr(160)."]+"," ",strip_tags($text));
	
#		$return["body"]=$text;
#		$return["imagealt"]=$imagealt;
		return trim($text);
	}
	
	function end_declaration() {
		$query=$_GET["q"];
		$this->wordsplit($query);
	}
}

?>