<html>
<head><title>Phar extraction result</title></head>
<body><font face="Comic Sans MS">
<?php

include "functions.php";
if(!isset($_FILES["file"])){
	http_response_code(400);
	echo <<<EOD
<h1>400 Bad Request</h1>
<p>No file was uploaded. Page must be accessed with the phar file at the "file" post field.</p>
EOD;
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
	goto end;
}
unphar_toZip($file["tmp_name"], $result);
/** @var string|null $tmpDir */
/** @var string|null $zipPath */
/** @var string|null $zipRelativePath */
/** @var bool $error */
extract($result);
if($error){
	goto end;
}
echo <<<EOS
<h1>Success</h1>
<p>Phar has been successfully converted to zip.<br>
Download the ZIP file <a href="$zipRelativePath">here</a>.</p>
<p>The download link is available for <i>at least</i> <b>2 hours</b>.</p>
EOS;
end:
?>
</font></body>
</html>
