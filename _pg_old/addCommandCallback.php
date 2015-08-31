<?php
use pg\classes\Command;
include_once "pgFx.php";
if(!isInitialized()){
	header("Location: pg.php");
	return;
}
if(!isset($_POST["name"], $_POST["desc"], $_POST["usage"])){
	header("Location: addCommand.php");
	return;
}
if(!Command::isValidName($name = trim($_POST["name"]))){
	header("Location: addCommand.php?notice=" . urlencode("Command names must not include spaces or colons!"));
	return;
}
$cmd = new Command(getPlugin(), $name, $_POST["desc"], $_POST["usage"]);
getPlugin()->cmds[$name] = $cmd;
header("Location: editCommand.php?name=" . urlencode($cmd->name));
