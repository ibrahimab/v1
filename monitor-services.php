<?php

//
// script to monitor the services of www.chalet.nl
//
// usage:
//		these URL's should return "OK". If not, there is an error in the used service.
//
//		https://www.chalet.nl/monitor-services.php?type=httpd
//		http://web01.chalet.nl/monitor-services.php?type=httpd
//		http://web02.chalet.nl/monitor-services.php?type=httpd
//
//		https://www.chalet.nl/monitor-services.php?type=mysql
//		http://web01.chalet.nl/monitor-services.php?type=mysql
//		http://web02.chalet.nl/monitor-services.php?type=mysql
//
//		https://www.chalet.nl/monitor-services.php?type=redis
//		http://web01.chalet.nl/monitor-services.php?type=redis
//		http://web02.chalet.nl/monitor-services.php?type=redis
//
//		https://www.chalet.nl/monitor-services.php?type=mongodb
//		http://web01.chalet.nl/monitor-services.php?type=mongodb
//		http://web02.chalet.nl/monitor-services.php?type=mongodb
//
//


include("admin/vars.php");
header("Content-Type: text/plain");

$service_okay = false;

$current_time = date("Y-m-d H:i:s");
$random_hash = md5( mt_rand() );

$check_for_string = $current_time."_".$random_hash;

switch ( $_GET["type"] ) {


	// Apache2 / httpd
	case "httpd":
		$service_okay = true;
		break;

	// MySQL
	case "mysql":

		$db->query("UPDATE diverse_instellingen SET monitor_mysql='".$check_for_string."' WHERE diverse_instellingen_id=1;");

		$db->query("SELECT monitor_mysql FROM diverse_instellingen WHERE diverse_instellingen_id=1;");
		if( $db->next_record() and $db->f( "monitor_mysql" )==$check_for_string ) {
			$service_okay = true;
		}
		break;

	// redis
	case "redis":

		$wt_redis = new wt_redis;
		$wt_redis->set("monitor_redis", $check_for_string);
		$redis_value = $wt_redis->get("monitor_redis");

		if( $redis_value==$check_for_string ) {
			$service_okay = true;
		}
		break;

	case "mongodb":

		break;

}

if( $service_okay ) {
	echo "OK";
} else {
	echo "Error in service ".$_GET["type"];
}


?>