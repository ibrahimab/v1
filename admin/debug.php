<?php

include "/home/webtastic/html/vendor/autoload.php";


use DebugBar\StandardDebugBar;

$debugbar = new StandardDebugBar();
$debugbarRenderer = $debugbar->getJavascriptRenderer();

// $debugbar->addCollector(new DebugBar\DataCollector\MessagesCollector());
// $debugbar["messages"]->addMessage("hello world!");
// $debugbar['messages']->info('hello world2');
