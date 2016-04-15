<?php
include "functions.php";
?><html>
<head>
	<title>Phar extraction result</title>
	<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script>
		$(document).ready(function(){
			var dl = $("#dlButton");
			if(typeof pmt.agree.lock !== typeof undefined){
				var agreeKey = "yes";
				if(typeof pmt.agree.key !== typeof undefined){
					agreeKey = pmt.agree.key;
				}else{
					agreeLock += "\nType \"yes\" if you agree with the above terms to continue";
				}
				if(prompt(pmt.agree.lock).toLowerCase() != agreeKey.toLowerCase()){
					alert("You must agree with the terms to download the zip!");
					window.location.replace("/unphar.php");
					return;
				}
			}
			dl.css("display", "block");
		});
	</script>
</head>
<body><font face="Helvetica">
<?php
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
unphar_toZip($file["tmp_name"], $result, substr($file["name"], 0, -5));
$pmt = [];
/** @var string|null $tmpDir */
/** @var string|null $zipPath */
/** @var string|null $zipRelativePath */
/** @var string|null $basename */
/** @var bool $error */
extract($result);
if(!is_array($pmt)) $pmt = [];
if($error){
	goto end;
}
usage_inc("unphar", $timestamp);
echo "<script>var pmt = " + json_encode($pmt) + ";</script>";
echo <<<EOS
<h1>Success</h1>
<p>Phar has been successfully converted to zip.<br>
Download the ZIP file <a id="dlButton" href="$zipRelativePath">here</a>, or download with an alternative name:</p>
<p><i><font color="#2f4f4f">The altname download is currently not available.</font></i></p>
<!--<iframe width="500" src="/data/dlPhar.php?path=$basename"></iframe>-->
<p>The download link is available for <i>at least</i> <b>2 hours</b>.</p>
EOS;
end:
?>
</font></body>
</html>
