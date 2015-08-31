<?php

/** @noinspection PhpIllegalPsrClassPathInspection */
namespace pm;

use pm\tuner\PhpToken;
use pm\tuner\TokenPresentor;
use pm\tuner\Tuner;

class PharBuilder{
	const NO_ERROR = 0;
	const ERROR_OPENZIP = 1;
	const ERROR_EXTRACT = 2;
	const ERROR_NOPLUGINYML = 3;
	const ERROR_INSPECTION = 4;
	const ERROR_TUNING = 5;
	const ERROR_CREATEPHAR = 6;

	/** @var string $zipPath filepath */
	private $zipPath;
	/** @var int */
	public $errorId = self::NO_ERROR;
	public $errorMsg = "";
	/** @var \Phar|null */
	public $pharObj = null;
	/** @var string */
	public $pharPath;
	/** @var string */
	public $randomName;
	/** @var string */
	public $extractionPluginPath;
	public $warnings = [];
	public function __construct($zipPath){
		$this->zipPath = $zipPath;
	}
	public function extract(){
		$dir = \tmpalloc();
		$zip = new \ZipArchive;
		if(($err = $zip->open($this->zipPath)) !== true){
			$this->errorId = self::ERROR_OPENZIP;
			$this->errorMsg = "Error opening ZIP file: ";
			switch($err){
				case \ZipArchive::ER_EXISTS:
					$this->errorMsg .= "ER_EXISTS(" . \ZipArchive::ER_EXISTS . "): File already exists";
					break;
				case \ZipArchive::ER_INCONS:
					$this->errorMsg .= "ER_INCONS(" . \ZipArchive::ER_INCONS . "): Zip archive inconsistent";
					break;
				case \ZipArchive::ER_INVAL:
					$this->errorMsg .= "ER_INVAL(" . \ZipArchive::ER_INVAL . "): Invalid argument";
					break;
				case \ZipArchive::ER_MEMORY:
					$this->errorMsg .= "ER_MEMORY(" . \ZipArchive::ER_MEMORY . "): Malloc failure";
					break;
				case \ZipArchive::ER_NOENT:
					$this->errorMsg .= "ER_NOENT(" . \ZipArchive::ER_NOENT . "): No such file";
					break;
				case \ZipArchive::ER_NOZIP:
					$this->errorMsg .= "ER_NOZIP(" . \ZipArchive::ER_NOZIP . "): This is not a ZIP file";
					break;
				case \ZipArchive::ER_OPEN:
					$this->errorMsg .= "ER_OPEN(" . \ZipArchive::ER_OPEN . "): Cannot open file";
					break;
				case \ZipArchive::ER_READ:
					$this->errorMsg .= "ER_READ(" . \ZipArchive::ER_READ . "): Read error";
					break;
				case \ZipArchive::ER_SEEK:
					$this->errorMsg .= "ER_SEEK(" . \ZipArchive::ER_SEEK . "): Seek error";
					break;
				default:
					$this->errorMsg .= "Unknown: Unknown error";
					break;
			}
			return;
		}
		if(!$zip->extractTo($dir)){
			$this->errorId = self::ERROR_EXTRACT;
			$this->errorMsg = "Error extracting ZIP: Error extracting ZIP to a server directory";
			return;
		}
		if(!is_file($dir . "plugin.yml")){
			$this->warnings[] = "Cannot find plugin.yml in ZIP root!";
			$results = [];
			foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $file){
				if(strtolower(basename($file)) === "plugin.yml"){
					$real = realpath($file);
					$include = str_replace("\\", "/", substr($real, strlen(realpath($dir))));
					$slashCount = 0;
					for($pos = 0; ($pos = strpos($include, "/", $pos + 1)) !== false; $slashCount++);
					$htmlInclude = htmlspecialchars($include);
					$results[] = ["real" => $real, "include" => $include, "htmlInclude" => $htmlInclude, "slashCount" => $slashCount];
				}
			}
			if(count($results) === 0){
				$this->errorId = self::ERROR_NOPLUGINYML;
				$this->errorMsg = "Missing <code class='code'>plugin.yml</code> file: Could not find a plugin.yml file inside the directory!";
				return;
			}elseif(count($results) > 1){
				echo "<p>";
				$this->warnings[] = "The following occurrences of <code>plugin.yml</code> are found in the ZIP file:";
				$notice = "<ul>";
				$minReal = null;
				$min = null;
				$minCnt = PHP_INT_MAX;
				foreach($results as $resultInfo){
					/** @var string $htmlInclude */
					/** @var string $include */
					/** @var string $real */
					/** @var int $slashCount */
					extract($resultInfo);
					$notice .= "<li>$htmlInclude</li>";
					if($minCnt > $slashCount){
						$minCnt = $slashCount;
						$min = $include;
						$minReal = $real;
					}
				}
				$notice .= "</ul>";
				$this->warnings[] = $notice;
				$this->warnings[] = "Selecting $min as the base <code>plugin.yml</code> to build the plugin with.";
				$dir = dirname($minReal) . "\\";
			}else{
				/** @var string $htmlInclude */
				/** @var string $real */
				extract($results[0]);
				$this->warnings = "<p>Selecting $htmlInclude as the <code>plugin.yml</code> to build around with.</p>";
				$dir = dirname($real) . "/";
			}
		}
		$this->extractionPluginPath = $dir;
	}
	public function inspect(Inspection $inspection){
		$inspection->inspect($this->extractionPluginPath, $this->warnings);
	}
	/**
	 * @param Tuner[] $tuners
	 * @param TokenPresentor $presentor
	 */
	public function tune($tuners, TokenPresentor $presentor){
		foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->extractionPluginPath)) as $file){
			if(!is_file($file)){
				continue;
			}
			if(strtolower(substr($file, -4)) !== ".php"){
				continue;
			}
			$includePath = str_replace("\\", "/", substr($file, strlen($this->extractionPluginPath)));
			$contents = file_get_contents($file);
			/** @var PhpToken[] $tokens */
			$tokens = array_map(function($token){
				return new PhpToken($token);
			}, token_get_all($contents));
			if($tokens[0]->type !== T_OPEN_TAG){
				$this->errorId = self::ERROR_TUNING;
				$this->errorMsg = htmlspecialchars("Error tuning file $includePath: PocketMine-plugin-standard PHP files must start with the PHP open tag (\"<?php\"), but detected first token as " . token_name($tokens[0]->type) . "!");
				return;
			}
			if($tokens[$offset = count($tokens) - 1]->type === T_CLOSE_TAG){
				unset($tokens[$offset]);
				$this->warnings[] = htmlspecialchars("The redundant \"?>\" PHP close tag has been removed.");
			}
			foreach($tuners as $tuner){
				$tuner->tune($file, $contents, $tokens);
			}
			file_put_contents($file, $presentor->present($file, $contents, $tokens));
		}
	}
	public function build($name){
		retry:
		try{
			$this->randomName = generateRandomChars(16);
			$this->pharObj = new \Phar($this->pharPath = PUBLIC_DATA_PATH . $name . "." . $this->randomName . ".phar");
		}catch(\UnexpectedValueException $e){
			$err = true;
		}
		if(isset($err)){
			goto retry;
		}
		$this->pharObj->setStub('<?php __HALT_COMPILER();');
		$this->pharObj->setSignatureAlgorithm(\Phar::SHA512);
		$this->pharObj->startBuffering();
		$this->pharObj->addFile($this->extractionPluginPath . "plugin.yml", "plugin.yml");
		$this->addRecr($this->extractionPluginPath . "src", "src");
		if(is_dir($this->extractionPluginPath . "resources")){
			$this->addRecr($this->extractionPluginPath . "resources", "resources");
		}
		$this->pharObj->compressFiles(\Phar::GZ);
		$this->pharObj->stopBuffering();
	}
	public function addRecr($dir, $localName){
		$localName = rtrim($localName, "/\\") . "/";
		foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $file){
			if(!is_file($file)){
				continue;
			}
			$shortPath = str_replace("\\", "/", ltrim(substr($file, strlen($dir)), "/\\")); // Why, Windows...
			$this->pharObj->addFile($file, $localName . $shortPath);
		}
	}
}
