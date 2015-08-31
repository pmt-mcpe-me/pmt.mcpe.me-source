<?php

session_start();
$_SESSION = [];

if(isset($_REQUEST["redirect"])){
	header("Location: " . $_REQUEST["redirect"]);
	die;
}
header("Location: ./");
