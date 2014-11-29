<?php

namespace inspections;

class ClasspathInspection implements Inspection{
	private $dir;
	public function __construct($dir){
		$this->dir = $dir;
	}
	/**
	 * @return InspectionResult
	 */
	public function run(){
		$result = new InspectionResult("Classpath");
		$pluginYml = fopen($this->dir . "plugin.yml", "rb");
		while(!feof($pluginYml)){
			$line = trim(fgets($pluginYml));
			if(strpos($line, "main: ") === 0){
				$mainClass = substr($line, 6);
				break;
			}
		}
		if(!isset($mainClass)){
			$result->error("Attribute <code>main</code> is missing in <code>plugin.yml</code>!");
			goto end;
		}
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
			$result->info("Main class file expected at <code>$expectedSub</code> but it is not a file");
			goto end;
		}
		$result->info("Main class file found at <code>$expectedSub</code>");
		$code = preg_replace("/[\r\n\t ]+/m", " ", file_get_contents($mainClassFile));
		$tokens = explode("\\", $mainClass);
		$simpleName = $tokens[count($tokens) - 1];
		$namespace = implode(" ", array_slice($tokens, 0, -1));
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
		end:
		return $result;
	}
}
