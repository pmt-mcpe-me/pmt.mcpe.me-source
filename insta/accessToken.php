<?php

session_start();
if(!isset($_SESSION["state"]) or isset($_SESSION["access_token"]) or !isset($_REQUEST["state"]) or !isset($_REQUEST["code"])){
	header("Location: ./");
}

$ch = curl_init("https://github.com/login/oauth/access_token");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ["client_id" => trim(file_get_contents("/var/www/cid.txt")), "client_secret" => trim(file_get_contents("/var/www/secret.txt")), "code" => $_REQUEST["code"], ]);
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0 pmt.mcpe.me-insta/1.0", "Accept: application/json"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$ret = curl_exec($ch);
curl_close($ch);

function urlGet($url){
	$ch = curl_init($url);
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

$json = json_decode($ret);
$_SESSION["access_token"] = $json->access_token;
$userData = json_decode(urlGet("https://api.github.com/user"));
$_SESSION["github_login"] = $userData->login;
$_SESSION["github_name"] = $userData->name;
header("Location: ./", true, 302);
