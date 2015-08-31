<?php

include_once dirname(dirname(__FILE__)) . "/functions.php";
session_start();

function isInitialized(){
	return isset($_SESSION["plugin"]);
}

/**
 * @return pg\classes\Plugin
 */
function getPlugin(){
	return $_SESSION["plugin"];
}
