<?php
define("NO_PAGE_GEN_FOOTER", true);
include_once "../pgFx.php";

$data = [
	"status" => "OK",
	"evalCode" => "alert('Failure: Unknown reason');"
];

if(!isInitialized()){
	$data["status"] = "pluginNotInitialized";
	goto hell;
}

if(!isset($_POST["oldName"], $_POST["newName"])){
	$data["status"] = "insufficientPostFields";
	goto hell;
}

$plugin = getPlugin();
if(!isset($plugin->cmds[$oldName = $_POST["oldName"]])){
	$data["status"] = "cmdNotFound";
	goto hell;
}

$newName = $_POST["newName"];
$cmd = $plugin->cmds[$oldName];

if(isset($plugin->cmds[$newName])){
	$data["evalCode"] = "alert('The command /$newName already exists!');";
	goto hell;
}

$cmd->name = $newName;
unset($plugin->cmds[$oldName]);
$plugin->cmds[$newName] = $cmd;
$data["evalCode"] = "alert('Done! The command name has been changed from /$oldName to /$newName.');
location.replace('editCommand.php?name=" . urlencode($newName) . "');";

hell:
echo json_encode($data);
