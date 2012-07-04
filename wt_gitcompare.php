<?php


if($_GET["gewijzigddoor"]=="jeroen") {
	$name1="miguelChalet";
	$name2="Jeroen Boschman";
} elseif($_GET["gewijzigddoor"]=="miguel" or !$_GET["gewijzigddoor"]) {
	$name1="Jeroen Boschman";
	$name2="miguelChalet";
}

$xml=simplexml_load_file("https://github.com/Chalet/Website-Chalet.nl/commits/testing.atom?login=Boschman&token=b24645dd74ea0c2e0e61d8bce51cb90e");
if(is_object($xml)) {
	foreach($xml->entry as $value) {
		if($sha1_1) {
			if($value->author->name==$name1) {
				$sha1_2=preg_replace("/.*:Commit\//","",$value->id);
				break;
			}
		
		} else {
			if($value->author->name==$name2) {
				$sha1_1=preg_replace("/.*:Commit\//","",$value->id);
			} else {
				echo "<pre>Er zijn geen commits van ".htmlentities($name2)." sinds de laatste push van ".htmlentities($name1).".</pre>";
				exit;
			}
		}
	}
}

if($sha1_1 and $sha1_2) {
	header("Location: https://github.com/Chalet/Website-Chalet.nl/compare/".$sha1_2."...".$sha1_1);
	exit;
}

?>