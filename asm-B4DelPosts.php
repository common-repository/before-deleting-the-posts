<?php
/**
 * This file is an emergency measure for a error.
 * It is "The plugin does not have a valid header.".
**/

global $asumaru_registed_plugins;
$asumaru_registing_plugin = "before-deleting-the-posts";

if(isset($asumaru_registed_plugins) && is_array($asumaru_registed_plugins)){
	if(in_array($asumaru_registing_plugin,$asumaru_registed_plugins)){
		return;
	}
}
$asumaru_registed_plugins[] = $asumaru_registing_plugin;
$asumaru_registing_plugin_path = dirname(__FILE__) . '/includes/' . $asumaru_registing_plugin;
if(file_exists($asumaru_registing_plugin_path)){
	include_once($asumaru_registing_plugin_path);
}
?>