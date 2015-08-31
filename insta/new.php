<?php

session_start();
if(!isset($_SESSION["access_token"])){
	header("Location: ./");
	die;
}
function urlGet($url, $post = false, $postFields = []){
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, $post ? 1 : 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0 pmt.mcpe.me-insta/1.0", "Authorization: bearer " . $_SESSION["access_token"]]);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

$name = $_REQUEST["name"];
$version = $_REQUEST["version"];
$api = $_REQUEST["api"];

$files = [
	"plugin.yml" => ["content" => yaml_emit([
		"name" => $name,
		"author" => $_SESSION["github_name"],
		"version" => $version,
		"api" => [$api],
		"main" => $main = ($namespace = "_" . str_replace("-", "_", $_SESSION["github_login"]) . "\\" . $name) . "\\" . "Main",
		"commands" => [],
		"permissions" => []
	])],
	"src--" . str_replace("\\", "--", $main) . ".php" => ["content" => <<<EOF
<?php
namespace $namespace;
use pocketmine\\command\\CommandSender;
use pocketmine\\command\\Command;
use pocketmine\\event\\Listener;
use pocketmine\\plugin\\PluginBase;

class Main extends PluginBase{
  public function onEnable(){
    // \$this->getServer()->getPluginManager()->registerEvents(\$this, \$this);
  }
  public function onCommand(CommandSender \$issuer, Command \$cmd, \$label, array \$params){
    switch(\$cmd->getName()){
    }
    return false;
  }
}
EOF
	]
];

$data = json_decode(urlGet("https://api.github.com/gists", true, json_encode([
	"description" => "$name - Auto-generated gist plugin stub by pmt.mcpe.me InstaPlugin",
	"files" => $files
])));
header("Location: https://gist.github.com/" . $_SESSION["github_login"] . "/$data->id/edit");
