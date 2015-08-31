<?php

namespace inspections;

class BadPracticeInspection implements Inspection{
	private $dir;
	public function __construct($dir){
		$this->dir = rtrim($dir, "\\/") . DIRECTORY_SEPARATOR;
	}
	/**
	 * @return InspectionResult
	 */
	public function run(){
		$result = new InspectionResult("Bad practice");
		$pluginYml = file_get_contents($this->dir . "plugin.yml");
		$manifest = yaml_parse($pluginYml);
		if($manifest === false){
			$result->error("Error parsing <code>plugin.yml</code>");
			return $result;
		}
		$mainFile = realpath($this->dir . "src/" . str_replace("\\", "/", $manifest["main"]) . ".php");
		/** @var \SplFileInfo $file */
		foreach(new \RegexIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->dir)), "#\\.php\$#") as $file){
			$file = $file->getPathname();
			$contents = file_get_contents($file);
			$isMain = $file === $mainFile;
			if(stripos($contents, "server::getinstance()") !== false){
				$result->warning("<code>Server::getInstance()</code> scanned in file <code>" .
					substr($file, strlen($this->dir)) . "</code><br>
					<ul><li>The PHP extensions that PocketMine-MP uses have some issues with
					static properties. You are recommended try using other methods to get
					the <code>Server</code> instance.</li>
					<li>" .
					($isMain ?
						"You can use <code>\$this->getServer()</code> to get the server object instead." :
						(stripos($contents, "extends PluginTask") !== false ?
							"You can use <code>\$this->getOwner()->getServer()</code> to get
							the <code>Server</code> instance instead.":
							"You can pass <code>\$this->getServer()</code> from the plugin object to
							your current class's constructor.")
					)
					. "</li></ul>");
			}
			if($isMain){
				if(preg_match_all(
					"#\\\$this->config[ \t\r\n]?=[ \t\r\n]?new[ \t\r\n]+config[ \t\r\n]?\\(#i"
					, $contents)){
					$result->warning("<code>PluginBase::\$config</code> is already defined in PocketMine
						PluginBase class. Strange errors will occur if you override
						<code>\$this->config</code> yourself. For example, when you decide to use
						<code>\$this->saveDefaultConfig()</code> later, it will not work.<br>
						<ul><li>You are recommended to improve this by renaming <code>\$this->config</code>
						to something else, or to use <code>\$this->saveDefaultConfig()</code> and related
						functions.</li></ul>");
				}
			}
		}
		return $result;
	}
}
