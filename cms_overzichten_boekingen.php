<?php

$mustlogin=true;

$vars["types_in_vars"]=true;
$vars["acc_in_vars"]=true;

include("admin/vars.php");

if(!$login->has_priv("22")) {
	header("Location: cms.php");
	exit;
}

# frm = formname (mag ook wat anders zijn)
$form=new form2("frm");
$form->settings["fullname"]="Periode";
$form->settings["layout"]["css"]=true;
$form->settings["type"]="get";

$form->settings["message"]["submitbutton"]["nl"]="OK";

#_field: (obl),id,title,db,prevalue,options,layout

$begin=mktime(0,0,0,12,1,2006);
$eind=mktime(0,0,0,date("m"),date("d"),date("Y"));

$form->field_htmlrow("","<i>Zoeken op datum is op basis van aankomstdatum</i>");
$form->field_date(1,"van","Van","",array("time"=>$begin),array("startyear"=>2006,"endyear"=>date("Y")+1),array("calendar"=>true));
$form->field_date(1,"tot","Tot en met","",array("time"=>$eind),array("startyear"=>2006,"endyear"=>date("Y")+1),array("calendar"=>true));

$form->check_input();

if($form->input["van"]["unixtime"]>$form->input["tot"]["unixtime"]) $form->error("tot","moet later zijn dan de eerste");

$form->end_declaration();

$layout->display_all($cms->page_title);

?>