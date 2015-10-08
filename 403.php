<?php

$vars["wt_htmlentities_cp1252"] = true;
require_once("admin/allfunctions.php");

$geen_tracker_cookie = true;
$vars["verberg_breadcrumbs"] = true;
$title["403"]="";
include("admin/vars.php");

wt_404(true);
//header('HTTP/1.0 403 Forbidden');

include "content/opmaak.php";
