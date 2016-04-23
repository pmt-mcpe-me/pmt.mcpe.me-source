<?php

const IS_UNDER_MAINTENANCE = false;
if(IS_UNDER_MAINTENANCE and $_SERVER["REMOTE_ADDR"] !== "14.199.247.137"){
	header("Content-Type: text/plain");
	echo "Sorry we are under maintenance. Please come back later.";
	exit;
}

const STYLE_DATE = "j<\\s\\u\\p>S</\\s\\u\\p> F, Y \\a\\t H:i:s";
define("START_TIME", microtime(true));
register_shutdown_function(function(){
	if(defined("NO_PAGE_GEN_FOOTER") and constant("NO_PAGE_GEN_FOOTER")){
		return;
	}
	$time = microtime(true) - START_TIME;
	echo "<br><hr><p>Page generated in $time second(s)</p>";
	if(!IS_UNDER_MAINTENANCE){
		deldir(TMP_PATH);
	}
});

define("htdocs", "/var/www/html/", true);
define("SERVER_PATH", "/var/www/", true);

define("DATA_PATH", "/var/www/html/data/");
@mkdir(DATA_PATH, 0777, true);
@mkdir(DATA_PATH . "phars");
define("TMP_PATH", SERVER_PATH . "tmp/");
@mkdir(TMP_PATH, 0777, true);

define("GITHUB_OAUTH_TOKEN", file_get_contents(SERVER_PATH . "token.txt"));

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
	$file = __DIR__ . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
	if(is_file($file)){
		require_once $file;
	}
});

define("gc_last", htdocs . "gc_last", true);
if(is_file(gc_last)){
	$last = (int) file_get_contents(gc_last);
	$diff = time() - $last;
	if($diff > 3600){
		exec_gc();
		unlink(gc_last);
	}
}
else{
	exec_gc();
}

function exec_gc(){
	$exp = time() - 7200;
	foreach(scandir(DATA_PATH . "phars/") as $file){
		if(substr($file, -4) === ".php"){
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
	if(!IS_UNDER_MAINTENANCE){
		deltmp();
	}
	file_put_contents(gc_last, (string) time());
}

function deltmp(){
//	if(!is_dir(TMP_PATH)){
//		return;
//	}
//	deldir(TMP_PATH);
	if(is_dir(TMP_PATH)){
		exec("rm -r " . realpath(TMP_PATH));
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

define("PRIV_DATA", SERVER_PATH . "privdata/");
@mkdir(PRIV_DATA);

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
	$ch = curl_init($page);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Apache/2.4.10 (Win32) OpenSSL/1.0.1h PHP/5.6.3");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$prefix = "https://api.github.com";
	if(substr($prefix, 0, strlen($prefix)) === $prefix){
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json", "Authorization: Bearer " . GITHUB_OAUTH_TOKEN]);
	}
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, (int) $timeout);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}

function html_var_dump(...$var){
	echo "<pre>";
	var_dump(...$var);
	echo "</pre>";
}

function phar_buildFromZip($zipPath, $name = "", TuneArgs $args){
	// extract
	$zip = new ZipArchive;
	$result = [
		"phar" => null,
		"pharpath" => "index.php",
		"extractpath" => htdocs . "index.php",
		"error" => MAKEPHAR_ERROR_NO,
		"error_id" => "N/A",
		"error_name" => "N/A",
		"erorr_msg" => "N/A",
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
				break;
			case ZipArchive::ER_MEMORY:
				$result["error_name"] = "ER_MEMORY";
				$result["error_msg"] = "Malloc failure";
				break;
			case ZipArchive::ER_NOENT:
				$result["error_name"] = "ER_NOENT";
				$result["error_msg"] = "No such file";
				break;
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
	$dir = getTmpDir();
	if($zip->extractTo($dir) !== true){
		$result["error"] = MAKEPHAR_ERROR_EXTRACTZIP;
		$result["error_id"] = false;
		$result["error_name"] = "";
		$result["error_msg"] = "Error extracting ZIP";
		return $result;
	}
	if(!is_file($dir . "plugin.yml")){
		$result["warnings"][] = "Cannot find plugin.yml in ZIP root!";
		$results = [];
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file){
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
			$dir = dirname($real) . "/";
		}
	}
	$result["extractpath"] = $dir;
	// tune
	tune($dir, $args);
	// compile
	while(is_file($file = DATA_PATH . ($subpath = "phars/" . randomClass(16, "phar_" . $name . "_") . ".phar")));
	$result["phar"] = $phar = new Phar($file);
	$result["pharpath"] = "/data/$subpath";
	$phar->setStub($_POST["stub"]);
	$phar->setSignatureAlgorithm(Phar::SHA1);
	$phar->startBuffering();
	$phar->buildFromDirectory($dir);
//	$phar->compressFiles(Phar::GZ);
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
		echo "Adding file $file to include path $relative\r\n";
		$phar->addFile($file, $relative);
	}
}
function tune($dir, TuneArgs $args){
	if(!$args->isFilled()){
		return;
	}
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file){
		if(!is_file($file)){
			continue;
		}
		if(strtolower(substr($file, -4)) === ".php"){
			tuneFile($file, $args);
		}
	}
}
function tuneFile($file, TuneArgs $args){
	$tokens = token_get_all(file_get_contents($file));
	foreach($tokens as $i => &$token){
		if(!is_array($token)){
			continue;
		}
		list($type, $src) = $token;
		if($type === T_STRING){
			if(is_array($tokens[$i - 1]) and $tokens[$i - 1][0] === T_NS_SEPARATOR){
				continue;
			}
			if(!isset($tokens[$i + 1])){
				echo "<pre>Error: Cannot optimize file " . basename($file) . " because of a syntax error: unexpected end of file</pre>";
			}
			$next = $tokens[$i + 1];
			if(is_array($next)){
				$next = $next[1];
			}
			$last = $tokens[$i - 1];
			if(is_array($last)){
				$last = $last[1];
			}
			if($args->topNamespaceBackslash and $tokens[$i - 1][0] !== T_FUNCTION){
				if(!in_array($next, ["::", "{", "("]) and !in_array($last, ["::", "->"])){
					if(defined($src)){
						$token[1] = "\\$src";
					}
				}
				elseif($next === "(" and !in_array(strtolower(trim($last)), ["::", "->", "function"]) and function_exists(trim($src))){
					if(function_exists($src)){
//						$token[1] = "\\$src";
					}
				}
			}
		}
	}
	$out = fopen($file, "wb");
	foreach($tokens as $i => $token){
		if(trim(is_array($token) ? $token[1] : $token) === "}" and !isset($tokens[$i + 1])){
			break;
		}
		$linebreak = $args->obfuscate ? "\r\n \t" : " ";
		if(is_array($token)){
			if($token[0] === T_OPEN_TAG){

			}
			if($token[0] === T_COMMENT){
				continue;
			}
			elseif($token[0] === T_WHITESPACE and $args->obfuscate){
				fwrite($out, " ");
				continue;
			}
			if(!$args->obfuscate){
				fwrite($out, $token[1]);
			}
			else{
				fwrite($out, preg_replace("#[$linebreak]+#", " ", $token[1]));
			}
		}
		else{
			if($args->obfuscate){
				fwrite($out, preg_replace("#[$linebreak]+#", " ", $token));
			}else{
				fwrite($out, $token);
			}
		}
	}
	fclose($out);
}

function unphar_toZip($tmpName, &$result, $name = ""){
	$result = [
		"tmpDir" => null,
		"zipPath" => null,
		"zipRelativePath" => null,
		"basename" => null,
		"error" => false
	];
	rename($tmpName, "$tmpName.phar");
	$tmpName .= ".phar";
	try{
		$phar = new Phar($tmpName);
		$result["tmpDir"] = $tmpDir = getTmpDir();
		$pharPath = "phar://{$phar->getPath()}/";
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pharPath)) as $f){
			$subpath = substr($f, strlen($pharPath));
			@mkdir(dirname($realSubpath = $tmpDir . $subpath), 0777, true);
			copy($f, $realSubpath);
		}
		$zip = new ZipArchive;
		$dir = "/data/phars/";
		while(is_file($file = htdocs . ($rel = $dir . randomClass(16, "zip_" . $name . "_") . ".zip")));
		$result["zipPath"] = $file;
		$result["zipRelativePath"] = $rel;
		$result["basename"] = substr($rel, 12);
		$err = $zip->open($file, ZipArchive::CREATE);
		if($err !== true){
			$msg = "Error creating zip file: ";
			switch($err){
				case ZipArchive::ER_EXISTS:
					$msg .= "ER_EXISTS ($err) File already exists ($file)";
					break;
				case ZipArchive::ER_INCONS:
					$msg .= "ER_INCONS ($err) Zip archive inconsistent.";
					break;
				case ZipArchive::ER_INVAL:
					$msg .= "ER_INVAL ($err) Invalid argument.";
					break;
				case ZipArchive::ER_MEMORY:
					$msg .= "ER_MEMORY ($err) Malloc failure.";
					break;
				case ZipArchive::ER_NOENT:
					$msg .= "ER_NOENT ($err) No such file.";
					break;
				case ZipArchive::ER_NOZIP:
					$msg .= "ER_NOZIP ($err) Not a zip archive.";
					break;
				case ZipArchive::ER_OPEN:
					$msg .= "ER_OPEN ($err) Can't open file.";
					break;
				case ZipArchive::ER_READ:
					$msg .= "ER_READ ($err) Read error.";
					break;
				case ZipArchive::ER_SEEK:
					$msg .= "ER_SEEK ($err) Seek error.";
			}
			throw new RuntimeException($msg . " Dump: " . var_export($result, true));
		}
		$tmpDir = realpath($tmpDir);
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tmpDir)) as $file){
			if(!is_file($file)){
				continue;
			}
			$file = realpath($file);
			$rel = substr($file, strlen($tmpDir) + 1);
			$zip->addFile($file, str_replace("\\", "/", $rel));
		}
		$metadata = $phar->getMetadata();
		if(isset($metadata["me.mcpe.pmt"])){
			$result["pmt"] = $metadata["me.mcpe.pmt"];
			$metadata["me.mcpe.pmt"] = "<pmt.mcpe.me metadata hidden>";
		}
		$zip->setArchiveComment(json_encode($phar->getMetadata(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		$zip->close();
	}
	catch(Exception $e){
		echo "<pre>";
		echo get_class($e) . ": {$e->getMessage()}";
		echo "\r\n\tat {$e->getFile()}:{$e->getLine()}</pre>";
		$result["error"] = true;
	}
}

function usage_inc($key, &$timestamp){
	if(!is_file("data/data.json")){
		$data = ["time" => time()];
	}
	else{
		$data = json_decode(file_get_contents("data/data.json"), true);
	}
	if(!isset($data["time"])){
		$data["time"] = time();
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

function getTmpDir(){
	$dir = TMP_PATH;
	for($i = 5; file_exists($dir . $i); $i++);
	$dir .= "$i/";
	mkdir($dir);
	return $dir;
}
function getTmpFile($extension = ""){
	if(strlen($extension) > 0){
		$extension = ".$extension";
	}
	for($i = 0; file_exists($file = TMP_PATH . $i . $extension); $i++);
	return $file;
}

function getClientTimezone(){
	if(!defined("CLIENT_TIMEZONE")){
		$data = unserialize(utils_getURL("http://ip-api.com/php/" . $_SERVER["REMOTE_ADDR"], 5));
		define("CLIENT_TIMEZONE", (is_array($data) and isset($data["timezone"])) ? $data["timezone"]:"UTC");
	}
	return new DateTimeZone(constant("CLIENT_TIMEZONE") ? constant("CLIENT_TIMEZONE"):"UTC");
}
function style_formatTimestamp($timestamp){
	$date = new DateTime("now", getClientTimezone());
	return $date->setTimestamp($timestamp)->format(STYLE_DATE);
}
function style_formatTimeSpan($seconds){
	$weeks = 0;
	$days = 0;
	$hours = 0;
	$minutes = 0;
	while($seconds >= 604800){
		$seconds -= 604800;
		$weeks++;
	}
	while($seconds >= 86400){
		$seconds -= 86400;
		$days++;
	}
	while($seconds >= 3600){
		$seconds -= 3600;
		$hours++;
	}
	while($seconds >= 60){
		$seconds += 60;
		$minutes++;
	}
	$output = "";
	if($weeks > 1){
		$output .= "$weeks weeks, ";
	}
	elseif($weeks === 1){
		$output .= "a week, ";
	}
	if($days > 1){
		$output .= "$days days, ";
	}
	elseif($days === 1){
		$output .= "a day, ";
	}
	if($hours > 1){
		$output .= "$hours hours, ";
	}
	elseif($hours === 1){
		$output .= "a hour, ";
	}
	if($minutes > 1){
		$output .= "$minutes minutes, ";
	}
	elseif($minutes === 1){
		$output .= "a minute, ";
	}
	if($seconds > 1){
		$output .= "$seconds seconds, ";
	}
	elseif($seconds === 1){
		$output .= "a second, ";
	}
	return $output === "" ? "0 second":substr($output, 0, -2);
}
