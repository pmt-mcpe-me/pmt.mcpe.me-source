<?php

namespace inspections;

class ClasspathInspection implements Inspection{
	private $dir;
	public function __construct($dir){
		$this->dir = rtrim($dir, "\\/") . DIRECTORY_SEPARATOR;
	}
	/**
	 * @return InspectionResult
	 */
	public function run(){
		$result = new InspectionResult("Classpath");
		$pluginYml = yaml_parse_file($this->dir . "plugin.yml");
		if($pluginYml === false){
			$result->error("Error parsing <code>plugin.yml</code>");
			return $result;
		}
		if(!isset($pluginYml["main"])){
			$result->error("Attribute <code>main</code> is missing in <code>plugin.yml</code>!");
			goto end;
		}
		$mainClass = $pluginYml["main"];
		$result->info("Main class scanned: $mainClass");
		$tokenHead = true;
		for($i = 0; $i < strlen($mainClass); $i++){
			$char = substr($mainClass, $i, 1);
			$ord = ord($char);
			if($tokenHead){
				$tokenHead = false;
				if(!(ord("A") <= $ord and $ord <= ord("Z") or ord("a") <= $ord and ord("z") or $char === "_")){
					$result->error("The first character of the class name or after a backslash must be <code>A-Z</code>, <code>a-z</code> or <code>_</code>. Invalid character <code>$char</code> at character $i of class name <code>$mainClass</code> found.");
				}
			}
			else{
				if(!(ord("A") <= $ord and $ord <= ord("Z") or ord("a") <= $ord and ord("z") or $char === "_" or ord("0") <= $ord and $ord <= ord("9") or $char === "\\")){
					$result->error("Invalid character <code>$char</code> found in classmain class name <code>$mainClass</code> at character $i found. A fully qualified class name must only contain <code>A-Z</code>, <code>a-z</code>, <code>_</code>, <code>0-9</code> and <code>\\</code>.");
				}
			}
		}
		$expectedSub = "src/" . str_replace("\\", "/", $mainClass) . ".php";
		$mainClassFile = $this->dir . $expectedSub;
		if(!is_file($mainClassFile)){
			$result->error("Main class file expected at <code>$expectedSub</code> but it is not a file");
			goto end;
		}
		$result->info("Main class file found at <code>$expectedSub</code>");
		$code = preg_replace("/[\r\n\t ]+/m", " ", file_get_contents($mainClassFile));
		$tokens = explode("\\", $mainClass);
		$simpleName = $tokens[count($tokens) - 1];
		$namespace = implode("\\", array_slice($tokens, 0, -1));
		$namespaceDec = "namespace $namespace";
		if(strpos($code, $namespaceDec) === false){
			$result->warning("Namespace declaration <code>$namespaceDec</code> not found");
		}
		$superclass = "(\\\\pocketmine\\\\plugin\\\\)?PluginBase";
		if(preg_match_all("#use pocketmine\\\\plugin\\\\PluginBase as ([A-Za-z0-9_]+) ?;#i", $code, $matches)){
			$alias = $matches[1][0];
			$superclass = "($superclass)|($alias)";
		}
		if(!preg_match_all("#class $simpleName (implements ([A-Za-z0-9_]+, ?)?[A-Za-z0-9]+)?extends $superclass#i", $code)){
			$result->error("Main class <code>$simpleName</code> is not declared as a subclass of pocketmine\\plugin\\PluginBase.");
		}
		$src = rtrim(realpath($this->dir . "src"), "/\\") . "/";
		foreach(new \RegexIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($src)), '#.php$#i') as $file){
			$file = realpath($file);
			$subpath = str_replace("\\", "/", substr($file, strlen($src), -4));
			$contents = file_get_contents($file);
			$namespace = implode("\\", array_slice($explosion = explode("/", $subpath), 0, -1));
			$namespaceEx = str_replace("\\", "\\\\", $namespace);
			if(preg_match_all("#namespace[\t \r\n]+" . $namespaceEx . "[\t \r\n]*[\\{\\;]#i", $contents) === 0){
				$result->warning("Namespace declaration as <code>$namespace</code> for <code>src/$subpath.php</code> missing");
			}
			$class = $explosion[count($explosion) - 1];
			if(preg_match_all("#(class|interface|trait)[\t \r\n]+$class#i", $contents) === 0){
				$result->warning("Class/interface/trait declaration as <code>$namespace\\$class</code> missing at <code>src/$subpath.php</code>");
			}
		}
		end:
		return $result;
	}
}
