<?php
define("NO_PAGE_GEN_FOOTER", true);
include_once "pgFx.php";
$plugin = getPlugin();

if(!isInitialized()){
	header("Location: pg.php");
	return;
}
if(!isset($_SERVER["PATH_INFO"]) or strtolower(substr($_SERVER["PATH_INFO"], -5)) !== ".phar"){
	header("Location: download.php/{$plugin->name}.phar");
	return;
}
$plugin->build();
