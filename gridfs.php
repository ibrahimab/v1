<?php
include 'admin/vars.php';

// getting the files
// $_GET['c']   = collection shortcut name
// $_GET['fid'] = file id in meta data
// $_GET['r']   = rank of file
$mongodb = new MongoWrapper();
$file    = $mongodb->getFile($vars['mongodb']['collections'][$_GET['c']], $_GET['fid'], $_GET['r']);

header('Content-Type: image/jpg');
echo $file->getBytes();
exit;

/**
 * - Caching
 * - eTag/Last Modified (browser caching)
 * - GET size params
 */