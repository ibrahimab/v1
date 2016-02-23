<?php
// bootstrapper
$root = dirname(__DIR__);

require_once $root . '/admin/vars_autoload.php';
require_once dirname($root) . '/chalet_php_constants.php';

require_once $root . '/admin/vars_db.php';

$mysqlsettings['charset'] = 'latin1';

require_once $root . '/admin/class.mysql.php';