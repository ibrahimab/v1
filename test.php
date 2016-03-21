<?php

date_default_timezone_set('Europe/Amsterdam');

require 'admin/vars.php';

$time = \DateTime::createFromFormat('Y-m-d', '2016-03-30');
$time->setTime(0, 0, 0);

$timestamp = komendezaterdag($time->getTimestamp());

$d = new DateTime();
$d->setTimestamp($timestamp);

dump($d);exit;