<?php
/*
Plugin Name: Before deleting the posts.
Plugin URI: http://asumaru.com/plugins/asm-B4DelPosts/
Description: This plugin backs up before deleting posts.
Author: Masarki Kondo @ Asumaru Corp.
Version: 0.2
Author URI: http://asumaru.com/
Created: 2014.12.01
Updated: 2014.12.01 (0.1)   We registed to Wordpress.org.
Updated: 2014.12.02 (0.1.1) Bug Fix.
Updated: 2014.12.03 (0.2)   We hanged plugin-file-name from "asm-B4DelPosts" to "before-deleting-the-posts".
                            Because there was an installation error "The plugin does not have a valid header.".
*/

global $asumaru_registed_plugins;
$asumaru_registing_plugin = basename(__FILE__);

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