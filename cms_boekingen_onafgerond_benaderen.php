<?php

//
// show and mail unfinished bookings
//

$mustlogin = true;
$vars["types_in_vars"] = true;
$vars["types_in_vars_wzt_splitsen"] = true;
include("admin/vars.php");

$layout->display_all();
