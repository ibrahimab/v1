<?php

include "/home/webtastic/html/vendor/autoload.php";

// wt_session_start();


use DebugBar\StandardDebugBar;
$wt_debugbar = new StandardDebugBar();
$wt_debugbar->setStorage(new DebugBar\Storage\FileStorage('/tmp/phpdebugbar'));

// $wt_debugbar->stackData();

$debugbarRenderer = $wt_debugbar->getJavascriptRenderer();

// $debugbarRenderer->setOpenHandlerUrl("rpc_json.php");
// $wt_debugbar->sendDataInHeaders();

class wt_add_collector extends DebugBar\DataCollector\MessagesCollector implements DebugBar\DataCollector\Renderable, DebugBar\DataCollector\AssetProvider
{

	public function getAssets()
	{
		return array(
			'css' => 'widgets/sqlqueries/widget.css',
			'js' => 'widgets/sqlqueries/widget.js'
		);
	}
}

if(defined("wt_redis_host")) {
	$wt_debugbar->addCollector(new wt_add_collector("redis"));
}

$wt_debugbar->addCollector(new wt_add_collector("database"));


// $wt_debugbar->addCollector(new wt_add_collector("rpc-json"));



