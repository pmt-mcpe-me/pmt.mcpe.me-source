<html><head>
	<title>Phar creation result</title>
</head><body>
<?php

use inspections\ClasspathInspection;

include "functions.php";
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
$result = phar_buildFromZip($file["tmp_name"]);
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
$gzPath = $result["gzpharpath"];
echo <<<EOP
<h1>Phar has been successfully created.</h1>
<p><a href="$url">Download the phar here.</a></p>
<p><a href="$gzPath">You can also download this GZIP-compressed archive.</a> It is smaller in size to download, but you have to extract it yourself.</p>
<p>The download link is available for at least two hours.</p>
EOP;
echo "<hr>";
echo "<h2>Inspections</h2>";
echo "<ul>";

/** @var inspections\Inspection[] $inspections */
$inspections = [];
$dir = $result["extractpath"];
if($_POST["inspection_classpath"] === "on"){
	$inspections[] = new ClasspathInspection($dir);
}

foreach($inspections as $inspection){
	$result = $inspection->run();
	$result->htmlEcho();
}

echo "</ul>";
the_end:
?>
</body></html>
