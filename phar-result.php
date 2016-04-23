<?php
include("lang/setlang.php");
define('PACKAGE', 'phar');

// gettext setting
bindtextdomain(PACKAGE, 'lang'); // or $your_path/lang, ex: /var/www/test/lang
textdomain(PACKAGE);
?>
<?php
include "functions.php";
$jsonExpected = $_SERVER["HTTP_ACCEPT"] === "application/json";
if(!$jsonExpected):
?>
<html>
<head>
	<title><?php echo _('Phar creation result'); ?></title>
</head>
<body>
<font face="Comic Sans MS">
	<?php endif; ?>
	<?php

	use inspections\BadPracticeInspection;
	use inspections\ClasspathInspection;
	use inspections\SyntaxErrorInspection;

	if(!isset($_FILES["file"])){
		http_response_code(400);
		$out = "Page must be accessed by POST with upload file entry 'file'";
		echo $jsonExpected ? json_encode(["error" => $out]) : $out;
		return;
	}
	$file = $_FILES["file"];
	if($file["error"] !== 0){
		echo _("<h1>Failure</h1>");
		echo _("Invalid upload: ");
		switch($err = $file["error"]){
			case UPLOAD_ERR_INI_SIZE:
				$errMsg = "file is too large UPLOAD_ERR_INI_SIZE($err)";
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$errMsg = "file is too large UPLOAD_ERR_FORM_SIZE($err)";
				break;
			case UPLOAD_ERR_PARTIAL:
				$errMsg = "file is only partially uploaded UPLOAD_ERR_PARTIAL($err)";
				break;
			case UPLOAD_ERR_NO_FILE:
				$errMsg = "no file is uploaded UPLOAD_ERR_NO_FILE($err)";
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$errMsg = "Missing a temporary folder UPLOAD_ERR_NO_TMP_DIR($err)";
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$errMsg = "Failed to write file to disk UPLOAD_ERR_CANT_WRITE($err)";
				break;
			case UPLOAD_ERR_EXTENSION:
				$errMsg = "A PHP extension stopped the file upload UPLOAD_ERR_EXTENSION($err)";
				break;
		}
		if(!$jsonExpected){
			goto the_end;
		}else{
			echo json_encode(["error" => $errMsg]);
			die;
		}
	}
	$args = new TuneArgs;
	$tno = "tune_top_namespace_optimization";
	$obs = "tune_obfuscation";
	$args->topNamespaceBackslash = isset($_POST[$tno]) ? ($_POST[$tno] === "on") : false;
	$args->obfuscate = isset($_POST[$tno]) ? ($_POST[$tno] === "on") : false;
	$result = phar_buildFromZip($file["tmp_name"], substr($file["name"], 0, -4), $args);
	if($result["error"] !== MAKEPHAR_ERROR_NO){
		if(!$jsonExpected){
			echo _("<h1>Failed to create phar</h1>");
			echo _("<p>Error: ");
			echo $MAKEPHAR_ERROR_MESSAGES[$result["error"]];
			echo "<br>";
			echo "<code>" . $result["error_name"] . "(" . $result["erorr_id"] . ")</code>: ";
			echo $result["error_msg"];
			echo "</p>";
			goto the_end;
		}else{
			json_encode(["error" => $MAKEPHAR_ERROR_MESSAGES[$result["error"]]]);
		}
	}
	$url = $result["pharpath"];
	$basename = urlencode(substr($url, 12));

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
	if($jsonExpected){
		$jsonData = [
			"phar" => "http://pmt.mcpe.me" . $url,
			"expiry" => time() + 7200,
			"inspections" => []
		];
		foreach($inspections as $inspection){
			$jsonData["inspections"][$result->getName()] = $inspection->run()->jsonResult();
		}
		echo json_encode($jsonData);
		die;
	}
	echo <<<EOP
<h1>Phar has been successfully created.</h1>
<p><a href="$url">Download the phar here</a>, or download with an alternative name:</p>
<iframe width="500" src="/data/dlPhar.php?path=$basename"></iframe>
<p>The download link is available for at least two hours.</p>
EOP;
	echo _("<p>In the past $itv, $cnt phars have been created.</p>");
	echo "<hr>";
	echo _("<h2>Inspections</h2>");
	echo "<ul>";
	foreach($inspections as $inspection){
		$inspection->run()->htmlEcho();
	}
	echo "</ul>";
	echo _("<p>End of inspections</p>");
	?>
	<p><?php echo _('You are also recommended to check the phar file at <a href="http://www.pocketmine.net/pluginReview.php"
	                                                         target="_blank">the official PocketMine plugin reviewing
			tool</a> to check your bad practices and
		the permissions that your plugin uses.'); ?></p>
	<?php the_end: ?>
</font></body>
</html>
