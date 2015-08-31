<?php
use pg\classes\Plugin;
include "../functions.php";
session_start();
if(isset($_SESSION["plugin"])){
	header("Location: main.php");
	return;
}
if(!isset($_POST["name"], $_POST["version"], $_POST["author"])){
	header("Location: pg.php");
	return;
}
if(!Plugin::validateName($name = $_POST["name"])){
	header("Location: pg.php?notice=" . urlencode("\"$name\" is an invalid plugin name. Plugin names should and should only contain at least two characters of A-Z, a-z, 0-9 and/or _"));
}

$_SESSION["plugin"] = new Plugin($name, $_POST["version"], $_POST["author"]);

header("Location: main.php");
