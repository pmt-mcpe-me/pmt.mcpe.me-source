<?php

namespace inspections;

class SyntaxErrorInspection{
	private $dir;
	public function __construct($dir){
		$this->dir = rtrim($dir, "\\/") . DIRECTORY_SEPARATOR;
	}
	/**
	 * @return InspectionResult
	 */
	public function run(){
		$result = new InspectionResult("Syntax errors");
		$good = 0;
		$bad = 0;
		foreach(new \RegexIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->dir)), "#\\.php\$#") as $file){
			exec("php -l $file 2>&1", $out);
			$lint = implode("<br>", $out);
			if(strpos($lint, "No syntax errors detected in") === 0){
				$good++;
			}else{
				$bad++;
				$result->error($lint);
			}
		}
		$result->info("$good good PHP file(s) and $bad bad PHP file(s) found.");
		$result->info("Checked with <code>PHP " . `php -r 'echo PHP_VERSION;'` . "</code>");
		return $result;
	}
}
