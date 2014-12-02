<?php

const DATA_PATH = "C:\\Apache24\\htdocs\\data\\";
@mkdir(DATA_PATH, 0777, true);
@mkdir(DATA_PATH . "phars");
const TMP_PATH = "C:\\Apache24\\tmp\\";
@mkdir(TMP_PATH, 0777, true);

const MAKEPHAR_ERROR_NO = 0;
const MAKEPHAR_ERROR_OPENZIP = 1;
const MAKEPHAR_ERROR_EXTRACTZIP = 2;
const MAKEPHAR_ERROR_NO_PLUGIN_YML = 3;

$MAKEPHAR_ERROR_MESSAGES = [
	MAKEPHAR_ERROR_NO => "No errors",
	MAKEPHAR_ERROR_OPENZIP => "Failed opening ZIP",
	MAKEPHAR_ERROR_EXTRACTZIP => "Failed extracting ZIP",
	MAKEPHAR_ERROR_NO_PLUGIN_YML => "Cannot find <code>plugin.yml</code> anywhere inside the ZIP"
];
spl_autoload_register(function($class){
	require_once dirname(__FILE__) . "\\" . $class . ".php";
});

if(is_file("gc_last")){
	$last = (int) file_get_contents("gc_last");
	$diff = time() - $last;
	if($diff > 3600){
		exec_gc();
		unlink("gc_last");
	}
}
else{
	exec_gc();
}

function exec_gc(){
	$exp = time() - 7200;
	foreach(scandir(DATA_PATH . "phars/") as $file){
		if(substr($file, -9) === "index.php"){
			continue;
		}
		$file = DATA_PATH . "phars/$file";
		if(is_file($file)){
			$time = filemtime($file);
			if($time < $exp){
				unlink($file);
			}
		}
	}
	deltmp();
	file_put_contents("gc_last", (string) time());
}

function deltmp(){
	if(!is_dir(TMP_PATH)){
		return;
	}

}
function deldir($dir){
	$dir = rtrim($dir, "/\\") . "/";
	foreach(scandir($dir) as $file){
		$file = trim($file, "/\\");
		if(is_dir($dir . $file) and $file !== "." and $file !== ".."){
			deldir($dir . $file);
		}
		elseif(is_file($dir . $file)){
			unlink($dir . $file);
		}
	}
	rmdir($dir);
}

const PRIV_DATA = "C:\\Apache24\\privdata\\";
@mkdir(PRIV_DATA);

function start_session($data = [], $timeout = 7200){
	while(is_file($path = PRIV_DATA . ($id = randomClass(32, "")) . ". json"));
	$array = [
		"creation" => time(),
		"lastUpdate" => time(),
		"timeout" => $timeout,
		"id" => $id,
		"data" => $data
	];
	file_put_contents($path, json_encode($array, JSON_PRETTY_PRINT));
	return $id;
}
function save_session($id, $data){
	$path = PRIV_DATA . "$id.json";
	if(!is_file($id)){
		return false;
	}
	$array = json_decode(file_get_contents($path), true);
	$array["data"] = $data;
	$array["lastUpdate"] = time();
	file_put_contents($path, json_encode($array, JSON_PRETTY_PRINT));
	return true;
}
function read_session($id){
	if(!is_file($path = PRIV_DATA . "$id.json")){
		return false;
	}
	return file_get_contents($path);
}

function randomClass($length, $init = "_"){
	$output = $init;
	for($i = 1; $i < $length; $i++){
		$output .= randClassChar();
	}
	return $output;
}
function randClassChar(){
	return rand_intToChar(mt_rand(0, 62));
}
function rand_intToChar($int){
	$int %= 63;
	while($int < 0) $int += 63;
	if($int < 26){
		return chr(ord("A") + $int);
	}
	$int -= 26;
	if($int < 26){
		return chr(ord("a") + $int);
	}
	$int -= 26;
	if($int < 10){
		return chr(ord("0") + $int);
	}
	return "_";
}

function utils_getURL($page, $timeout = 2){
	echo "initializing curl channel for $page... ";
	$ch = curl_init($page);
	echo "done\r\n";
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Apache/2.4.10 (Win32) OpenSSL/1.0.1h PHP/5.6.3");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, (int) $timeout);
	echo "getting $page...";
	$ret = curl_exec($ch);
	echo " done, closing... ";
	curl_close($ch);
	echo "done\r\n";
	echo "Length: " . strlen($ret) . "\r\n";
	return $ret;
}

function phar_buildFromZip($zipPath){
	$zip = new ZipArchive;
	$result = [
		"phar" => null,
		"pharpath" => "null.php",
		"gzphar" => null,
		"gzpharpath" => "null.php",
		"extractpath" => "C:\\Apache24\\htdocs\\null.php",
		"error" => MAKEPHAR_ERROR_NO,
		"warnings" => [],
		"notices" => [],
	];
	if(($err = $zip->open($zipPath)) !== true){
		$result["error"] = MAKEPHAR_ERROR_OPENZIP;
		$result["error_id"] = $err;
		switch($err){
			case ZipArchive::ER_EXISTS:
				echo "ER_EXISTS($err) File already exists";
				$result["error_name"] = "ER_EXISTS";
				$result["error_msg"] = "File already exists";
				break;
			case ZipArchive::ER_INCONS:
				$result["error_name"] = "ER_INCONS";
				$result["error_msg"] = "Zip archive inconsistent";
				break;
			case ZipArchive::ER_INVAL:
				$result["error_name"] = "ER_INVAL";
				$result["error_msg"] = "Invalid argument";
			case ZipArchive::ER_MEMORY:
				$result["error_name"] = "ER_MEMORY";
				$result["error_msg"] = "Malloc failure";
				break;
			case ZipArchive::ER_NOENT:
				$result["error_name"] = "ER_NOENT";
				$result["error_msg"] = "No such file";
			case ZipArchive::ER_NOZIP:
				$result["error_name"] = "ER_NOZIP";
				$result["error_msg"] = "This is not a ZIP file";
				break;
			case ZipArchive::ER_OPEN:
				$result["error_name"] = "ER_OPEN";
				$result["error_msg"] = "Cannot open file";
				break;
			case ZipArchive::ER_READ:
				$result["error_name"] = "ER_READ";
				$result["error_msg"] = "Read error";
				break;
			case ZipArchive::ER_SEEK:
				$result["error_name"] = "ER_SEEK";
				$result["error_msg"] = "Seek error";
				break;
			default:
				$result["error_name"] = "Unknown";
				$result["error_msg"] = "Unknown error";
				break;
		}
		return $result;
	}
	$dir = TMP_PATH;
	for($i = 0; file_exists($dir . "$i\\"); $i++);
	mkdir($dir = $dir . "$i\\");
	if($zip->extractTo($dir) !== true){
		$result["error"] = MAKEPHAR_ERROR_EXTRACTZIP;
		$result["error_id"] = false;
		$result["error_name"] = "";
		$result["error_msg"] = "Error extracting ZIP";
		return $result;
	}
	if(!is_file($dir . "plugin.yml")){
		$results["warnings"][] = "Cannot find plugin.yml in ZIP root!";
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file){
			if(basename($file) === "plugin.yml"){
				$real = realpath($file);
				$include = str_replace("\\", "/", substr($real, strlen(realpath($dir))));
				$slashCount = 0;
				for($pos = 0; ($pos = strpos($include, "/", $pos + 1)) !== false; $slashCount++);
				$htmlInclude = htmlspecialchars($include);
				$results[] = ["real" => $real, "include" => $include, "htmlInclude" => $htmlInclude, "slashCount" => $slashCount];
			}
		}
		if(count($results) === 0){
			$result["error"] = MAKEPHAR_ERROR_NO_PLUGIN_YML;
			$result["error_id"] = false;
			$result["error_name"] = "";
			$result["error_msg"] = "";
			return $result;
		}
		if(count($results) > 1){
			echo "<p>";
			$result["notices"][] = "The following occurrences of <code>plugin.yml</code> are found in the ZIP file:";
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
			$result["notices"][] = $notice;
			$result["notices"][] = "Selecting $min as the <code>plugin.yml</code> to build around with.";
			$dir = dirname($minReal) . "\\";
		}
		else{
			/** @var string $htmlInclude */
			/** @var string $real */
			extract($results[0]);
			$result["notices"] = "<p>Selecting $htmlInclude as the <code>plugin.yml</code> to build around with.</p>";
			$dir = dirname($real);
		}
	}
	$result["extractpath"] = $dir;
	while(is_file($file = DATA_PATH . ($subpath = "phars/" . randomClass(16, "phar") . ".phar")));
	$result["phar"] = $phar = new Phar($file);
	$result["pharpath"] = "data/$subpath";
	$phar->setStub($_POST["stub"]);
	$phar->setSignatureAlgorithm(Phar::SHA1);
	$phar->startBuffering();
	$phar->buildFromDirectory($dir);
	/** @var Phar $other */
	$result["gzphar"] = $other = $phar->compress(Phar::GZ);
	$result["gzpharpath"] = $gzPath = substr(realpath($other->getPath()), strlen(realpath("C:\\Apache24\\htdocs\\")));
	$phar->stopBuffering();
	return $result;
}
function phar_addDir(Phar $phar, $include, $realpath){
	$realpath = rtrim(realpath($realpath), "/\\") . "/";
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($realpath)) as $file){
		if(!is_file($file)){
			continue;
		}
		$relative = rtrim($include, "/\\") . "/" . ltrim(substr(realpath($file), strlen($realpath)), "/\\");
		$phar->addFile($file, $relative);
	}
}

function usage_inc($key, &$timestamp){
	if(!is_file("data/data.json")){
		$data = ["time" => time()];
	}
	else{
		$data = json_decode(file_get_contents("data/data.json"), true);
	}
	if(!isset($data[$key])){
		$data[$key] = 1;
	}
	else{
		$data[$key]++;
	}
	file_put_contents("data/data.json", json_encode($data, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING));
	$timestamp = $data["time"];
	return $data[$key];
}
