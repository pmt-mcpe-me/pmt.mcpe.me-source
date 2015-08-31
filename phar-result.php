<?php
include "functions.php";
?>
<html><head>
	<title>Phar creation result</title>
</head><body><font face="Comic Sans MS">
<?php

use inspections\BadPracticeInspection;
use inspections\ClasspathInspection;
use inspections\SyntaxErrorInspection;

if(!isset($_FILES["file"])){
	http_response_code(400);
	echo "Page must be accessed by POST with upload file entry 'file'";
	return;
}
$file = $_FILES["file"];
if($file["error"] !== 0){
	echo "<h1>Failure</h1>";
	echo "Invalid upload: ";
	switch($err = $file["error"]){
		case UPLOAD_ERR_INI_SIZE:
			echo "file is too large UPLOAD_ERR_INI_SIZE($err)";
			break;
		case UPLOAD_ERR_FORM_SIZE:
			echo "file is too large UPLOAD_ERR_FORM_SIZE($err)";
			break;
		case UPLOAD_ERR_PARTIAL:
			echo "file is only partially uploaded UPLOAD_ERR_PARTIAL($err)";
			break;
		case UPLOAD_ERR_NO_FILE:
			echo "no file is uploaded UPLOAD_ERR_NO_FILE($err)";
			break;
		case UPLOAD_ERR_NO_TMP_DIR:
			echo "Missing a temporary folder UPLOAD_ERR_NO_TMP_DIR($err)";
			break;
		case UPLOAD_ERR_CANT_WRITE:
			echo "Failed to write file to disk UPLOAD_ERR_CANT_WRITE($err)";
			break;
		case UPLOAD_ERR_EXTENSION:
			echo "A PHP extension stopped the file upload UPLOAD_ERR_EXTENSION($err)";
			break;
	}
	goto the_end;
}
$args = new TuneArgs;
$tno = "tune_top_namespace_optimization";
$obs = "tune_obfuscation";
$args->topNamespaceBackslash = isset($_POST[$tno]) ? ($_POST[$tno] === "on") : false;
$args->obfuscate = isset($_POST[$tno]) ? ($_POST[$tno] === "on") : false;
$result = phar_buildFromZip($file["tmp_name"], substr($file["name"], 0, -4), $args);
if($result["error"] !== MAKEPHAR_ERROR_NO){
	echo "<h1>Failed to create phar</h1>";
	echo "<p>Error: ";
	echo $MAKEPHAR_ERROR_MESSAGES[$result["error"]];
	echo "<br>";
	echo "<code>" . $result["error_name"] . "(" . $result["erorr_id"] . ")</code>: ";
	echo $result["error_msg"];
	echo "</p>";
	goto the_end;
}
$url = $result["pharpath"];
$basename = urlencode(substr($url, 12));
echo <<<EOP
<h1>Phar has been successfully created.</h1>
<p><a href="$url">Download the phar here</a>, or download with an alternative name:</p>
<iframe width="500" src="/data/dlPhar.php?path=$basename"></iframe>
<p>The download link is available for at least two hours.</p>
EOP;
$cnt = usage_inc("pharbuild", $time);
$diff = time() - $time;
$itv = "";
if($diff >= 3600 * 24){
	$itv .= ((int) ($diff / (3600 * 24))) . " day(s), ";
	$diff %= 3600 * 24;
	while($diff < 0){
		$diff += 3600 * 24;
	}
}
if($diff >= 3600){
	$itv .= ((int) ($diff / 3600)) . " hour(s), ";
	$diff %= 3600;
	while($diff < 0){
		$diff += 3600;
	}
}
if($diff >= 60){
	$itv .= ((int) ($diff / 60)) . " minute(s), ";
	$diff %= 60;
	while($diff < 0){
		$diff += 60;
	}
}
$itv .= "$diff second(s)";
echo "<p>In the past $itv, $cnt phars have been created.</p>";
echo "<hr>";
echo "<h2>Inspections</h2>";
echo "<ul>";
/** @var inspections\Inspection[] $inspections */
$inspections = [];
$dir = $result["extractpath"];
foreach(["inspection_classpath", "inspection_bad_practice", "inspection_lint"] as $field){
	if(!isset($_POST[$field])){
		$_POST[$field] = "off";
	}
}
if($_POST["inspection_classpath"] === "on"){
	$inspections[] = new ClasspathInspection($dir);
}
if($_POST["inspection_bad_practice"] === "on"){
	$inspections[] = new BadPracticeInspection($dir);
}
if($_POST["inspection_lint"] === "on"){
	$inspections[] = new SyntaxErrorInspection($dir);
}
foreach($inspections as $inspection){
	$result = $inspection->run();
	$result->htmlEcho();
}
echo "</ul>";
echo "<p>End of inspections</p>";
?>
<p>You are also recommended to check the phar file at <a href="http://www.pocketmine.net/pluginReview.php"
		target="_blank">the official PocketMine plugin reviewing tool</a> to check your bad practices and
	the permissions that your plugin uses.</p>
<?php the_end: ?>
</font></body></html>
