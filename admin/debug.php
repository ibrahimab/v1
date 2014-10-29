<?php

include "/home/webtastic/html/vendor/autoload.php";

use DebugBar\StandardDebugBar;
$wt_debugbar = new StandardDebugBar();
$wt_debugbar->setStorage(new DebugBar\Storage\FileStorage('/tmp/phpdebugbar'));
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

$wt_debugbar->addCollector(new wt_add_collector("Database"));
// $wt_debugbar->addCollector(new wt_add_collector("rpc-json"));

