<?php

namespace pg\classes;

class Plugin{
	/** @var string */
	public $name, $version;
	/** @var string[] */
	public $authors;
	/** @var Command[] */
	public $cmds = [];
	public $events = [];
	public $perms = [];
	public static function validateName($name){
		return preg_match('/^[A-Za-z0-9_]{2,}$/', $name);
	}
	public function __construct($name, $version, $author){
		$this->name = $name;
		$this->version = $version;
		$this->authors = preg_split("/[ \t]*,[ \t]*/", $author);
	}
	public function build(){
		$file = getTmpFile("phar");
		$phar = new \Phar($file);
		$phar->setStub("<?php __HALT_COMPILER(); ?>");
		$phar->setSignatureAlgorithm(\Phar::SHA1);
		$phar->startBuffering();
		$namespace = "_$this->name" . randomClass(8, "\\_");
		$class = randomClass(8);
		$main = "$namespace\\$class";
		$manifest = [
			"name" => $this->name,
			"version" => $this->version,
			"authors" => $this->authors,
			"api" => "1.9.0",
			"main" => $main,
			"commands" => []
		];
		foreach($this->cmds as $cmd){
			$manifest["commands"][$cmd->name] = [
				"description" => $cmd->desc,
				"usage" => $cmd->usage
			];
		}
		$phar->addFromString("plugin.yml", yaml_emit($manifest));
		$mainClass = <<<EOS
<?php
namespace $namespace;
use pocketmine\\command as cmd;
use pocketmine\\event as evt;
class $class extends \\pocketmine\\plugin\\PluginBase implements evt\\Listener{
public function onCommand(cmd\\CommandSender \$sender, cmd\\Command\ \$cmd, \$lbl, array \$args){
switch(\$cmd->getName()){
EOS;
		foreach($this->cmds as $cmd){
			$mainClass .= "case " . var_export($cmd->name, true) . ":" . PHP_EOL;
			$mainClass .= "return \$this->onCommand_$cmd->name(\$args, \$sender);" . PHP_EOL;
		}
		$mainClass .= "}" . PHP_EOL . "return false;" . PHP_EOL . "}" . PHP_EOL;
		foreach($this->cmds as $cmd){
			$mainClass .= $cmd->executor->toPhp();
		}
		$mainClass .= "}";
		$phar->addFromString("src/" . str_replace("\\", "/", $main) . ".php", $mainClass);
//		$phar->compressFiles(\Phar::GZ);
		$phar->stopBuffering();
		$path = $phar->getPath();
		header("Content-Type: application/octet-stream");
		echo file_get_contents($path);
//		unlink($path);
	}
}
