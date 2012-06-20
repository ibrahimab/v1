<?php
$unixdir="../";
include("../admin/vars.php");
$db->query("SELECT DISTINCT land_id, land FROM view_accommodatie WHERE atonen = '1' AND ttonen= '1' AND wzt='".addslashes($vars["seizoentype"])."'");
if($db->num_rows()){
	$xmloutput = "";
	$xmloutput .= "<!--?xml version='1.0' encoding='UTF-8'?-->";
    $xmloutput .= "<landen>";
    while($db->next_record()){
    	$xmloutput .="<land>";
        $xmloutput .="<landID>";
        $xmloutput .= $db->f("land_id");
        $xmloutput .= "</landID>";
        $xmloutput .= "<landNaam>";
        $xmloutput .= $db->f("land");
        $xmloutput .= "</landNaam>";
        $xmloutput .="<landUrl>";
        $xmloutput .= $vars["basehref"].txt("menu_land")."/".wt_convert2url($db->f("land"))."/";
        $xmloutput .= "</landUrl>";
        $xmloutput .= "<skigebiedID>";
        $xmloutput .= $db->f("skigebied_id");
        $xmloutput .= "</skigebiedID>";
        $xmloutput .= "<skigebiedNaam>";
        $xmloutput .= $db->f("skigebied");
        $xmloutput .= "</skigebiedNaam>";
        $xmloutput .= "<skigebiedUrl>";
        $xmloutput .= $vars["basehref"].txt("menu_skigebied")."/".wt_convert2url($db->f("skigebied"))."/";
        $xmloutput .= "</skigebiedUrl>";
        $xmloutput .= "<plaatsID>";
        $xmloutput .= $db->f("plaats_id");
        $xmloutput .= "</plaatsID>";
        $xmloutput .= "<plaatsNaam>";
        $xmloutput .= $db->f("plaats");
        $xmloutput .= "</plaatsNaam>";
        $xmloutput .= "<plaatsUrl>";
        $xmloutput .= $vars["basehref"].txt("menu_plaats")."/".wt_convert2url($db->f("plaats"))."/";
        $xmloutput .= "</plaatsUrl>";
        $xmloutput .="</land>"; 
    }
    $xmloutput .= "</landen>";
    print($xmloutput);
}
?>