<?php


//
// this file is included via auto_prepend_file
//

define("wt_server_name", "web02");
define("wt_server_id", 2);

// redis-server
define("wt_redis_host", "87.250.157.203");

// MS-mailproxy disabled on 19 May 2015 (requested by Wouter)
// always use WebTastic-mailproxy for Microsoft-mail
// define("wt_mail_proxy_microsoftmail_always", true);
// define("wt_mail_proxy_microsoftmail_copy", "testchalet@outlook.com");

define('CH_MONGODB_MASTER', 'mongodb://87.250.157.214:27017,87.250.157.200:27017,87.250.157.201:27017');
define('CH_MONGODB_FILES_DB', 'prod_files');
define('CH_MONGODB_FILES_REPLICASET', 'rs1');
