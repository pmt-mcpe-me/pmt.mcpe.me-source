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

if(!isset($_POST["cmd"], $_POST["name"], $_POST["value"])){
	$data["status"] = "insufficientPostFields";
	goto hell;
}

$property = $_POST["name"];
$value = $_POST["value"];
if(!in_array($property, ["desc", "usage"])){
	$data["status"] = "unknownProperty";
	goto hell;
}

$plugin = getPlugin();
if(!isset($plugin->cmds[$cmdName = $_POST["cmd"]])){
	$data["status"] = "cmdNotFound";
	goto hell;
}

$cmd = $plugin->cmds[$cmdName];
$cmd->$property = $value;

$htmlDesc = var_export($value, true);
$data["evalCode"] = "document.getElementById('property_$property').textContent = $htmlDesc;";

hell:
echo json_encode($data);
