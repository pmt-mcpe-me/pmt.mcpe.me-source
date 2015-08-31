<?php

/**
 * Indicates whether the server is under maintenance.
 */
const IS_UNDER_MAINTENANCE = false;

const WEBSITE_NAME = "http://imcpe.com/pm/";
const PLUGIN_GENERATOR_PATH = "/pg/";
const HOST = "imcpe.com";
const AUTHOR = "PEMapModder";

/////////////////////
// OTHER CONSTANTS //
/////////////////////
const JQUERY = '<script src="//code.jquery.com/jquery-1.10.2.min.js"></script><script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script><link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.min.css" rel="stylesheet" />';
define("htdocs", $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR, true);
define("SERVER_DIR", dirname(htdocs) . DIRECTORY_SEPARATOR);
define("mydir", htdocs . "x_pm/", true);
define("PUBLIC_DATA_PATH", mydir . "pdata/");
define("TMP_PATH", SERVER_DIR . "pm/tmp/");

/////////////////
// INIT STUFFS //
/////////////////
if(IS_UNDER_MAINTENANCE){
	header("Content-Type: text/plain");
	echo "Sorry, this website is under maintenance. Please come back later.";
	exit;
}
if(!is_dir(TMP_PATH)){
	mkdir(TMP_PATH, 0777, true);
}
if(!is_file(PUBLIC_DATA_PATH . "gc_last")){
	file_put_contents(PUBLIC_DATA_PATH . "gc_last", (string) time());
}else{
	$lastGc = (int) file_get_contents(PUBLIC_DATA_PATH . "gc_last");
	if(time() - $lastGc){
		unlink(PUBLIC_DATA_PATH . "gc_last");
		foreach(new RegexIterator(new DirectoryIterator(PUBLIC_DATA_PATH), '/^.*\.phar/') as $phar){
			unlink($phar);
		}
	}
}
register_shutdown_function(function(){
//	utils_recDelDir(TMP_PATH);
});
$sourcePaths = [];
function registerSourcePath($path){
	global $sourcePaths;
	$sourcePaths[] = rtrim($path, "/\\") . "/";
}
spl_autoload_register(function($class){
	global $sourcePaths;
	foreach($sourcePaths as $path){
		if(substr($class, 0, 3) === "pm\\"){
			$class = "x_" . $class;
		}
		if(is_file($file = $path . str_replace("\\", DIRECTORY_SEPARATOR, $class . ".php"))){
			include_once $file;
		}
	}
});
registerSourcePath(htdocs);
session_start();

/////////////////////////
// UTILITIES FUNCTIONS //
/////////////////////////
function utils_recDelDir($dir){
	exec("rm -r -f $dir");
	if(!is_dir($dir)){
		return;
	}
	$dir = rtrim($dir, "/\\") . "/";
	foreach(scandir($dir) as $file){
		$file = trim($file, "/\\");
		if(is_dir($dir . $file) and $file !== "." and $file !== ".."){
			utils_recDelDir($dir . $file);
		}
		elseif(is_file($dir . $file)){
			unlink($dir . $file);
		}
	}
	rmdir($dir);
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
function tmpalloc(){
	for($i = 0; file_exists($path = TMP_PATH . $i . "/"); $i++);
	return $path;
}
function generateRandomChars($length){
	for($string = ""; $length > 0; $length--){
		$string .= generateRandomChar();
	}
	return $string;
}
function generateRandomChar(){
	$int = mt_rand(0, 62);
	$int %= 63;
	while($int < 0){
		$int += 63;
	}
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
function redirect($page){
	header("Location: $page");
	die;
}
function forceTerms(){
	if(!isset($_SESSION["pm_terms_agreed"])){
		redirect("/" . substr(mydir, strlen(htdocs)) . "terms.php");
	}
}
