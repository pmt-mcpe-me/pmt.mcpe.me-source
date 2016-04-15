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
$namespace = (is_numeric($_SESSION["github_login"]{0}) ? "_" : "") . str_replace("-", "_", $_SESSION["github_login"]) . "\\" . $name;
$main = $namespace . "\\" . "Main";
$config = (isset($_REQUEST["config"]) and $_REQUEST["config"] == "on");
$configCode = $config ? ('    $this->saveDefaultConfig();' . "\n") : "";
$skeletons = (int) $_REQUEST["skeletons"];
$tasks = (int) $_REQUEST["tasks"];
$schedules = [];
$scheduleCode = "";
for($i = 1; $i <= $tasks; $i++){
	$schedules[] = $taskName = "My" . $i . "PluginTask";
	$scheduleCode .= '    $this->getServer()->getScheduler()->scheduleDelayedRepeatingTask(new ' . $taskName . '($this), 20); // TODO update the interval' . "\n";
}
$skeletonNames = [];
for($i = 1; $i <= $tasks; $i++){
	$skeletonNames[] = "My" . $i . "Class";
}

$mainContent = <<<EOF
<?php
namespace $namespace;
use pocketmine\\command\\CommandSender;
use pocketmine\\command\\Command;
use pocketmine\\event\\Listener;
use pocketmine\\plugin\\PluginBase;

class Main extends PluginBase{
  public function onEnable(){
$configCode    // \$this->getServer()->getPluginManager()->registerEvents(\$this, \$this);
$scheduleCode  }
  public function onCommand(CommandSender \$issuer, Command \$cmd, \$label, array \$params){
    switch(\$cmd->getName()){
    }
    return false;
  }
}

EOF;

$files = [
	"plugin.yml" => [
		"content" => yaml_emit([
			"name" => $name,
			"author" => $_SESSION["github_name"],
			"version" => $version,
			"api" => [$api],
			"main" => $main,
			"commands" => [],
			"permissions" => [],
		]),
	],
	"src--" . str_replace("\\", "--", $main) . ".php" => [
		"content" => $mainContent,
	],
];

foreach($schedules as $className){
	$c = <<<EOF
<?php
namespace $namespace;
use pocketmine\\plugin\\PluginTask;

class $className extends PluginTask{
  public function __construct(Main \$main){
    \$this->main = \$main;
  }
  public function onRun(\$ticks){
    // TODO Implement
  }
}

EOF;
	$files["src--" . str_replace("\\", "--", $namespace) . "--$className.php"] = ["content" => $c];
}
if($config){
	$files["resources--config.yml"] = ["content" => "---\n\n...\n"];
}

foreach($skeletonNames as $className){
	$c = <<<EOF
<?php
namespace $namespace;

class $className{
  public function __construct(){
    // TODO Implement
  }
}

EOF;
	$files["src--" . str_replace("\\", "--", $namespace) . "--$className.php"] = ["content" => $c];
}

$data = json_decode($jsonIn = urlGet("https://api.github.com/gists", true, $jsonOut = json_encode([
	"description" => "$name - Auto-generated gist plugin stub by pmt.mcpe.me InstaPlugin",
	"files" => $files,
])));
if(!isset($data->id)){
	header("Content-Type: text/plain");
	echo "Request:\r\n===$jsonOut\r\n";;
	echo "Response:\r\n===$jsonIn\r\n";;
	return;
}
header("Location: https://gist.github.com/" . $_SESSION["github_login"] . "/$data->id/edit");
