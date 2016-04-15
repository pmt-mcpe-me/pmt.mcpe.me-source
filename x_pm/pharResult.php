<?php
use pm\BadPracticeInspection;
use pm\ClasspathInspection;
use pm\PharBuilder;
use pm\tuner\TokenPresentor;
use pm\tuner\TopNamespaceTuner;

$pageStartTime = microtime(true);

include_once __DIR__ . "/functions.php";

function failWithMsg($title, $msg){
	?>
	<html>
	<head>
		<title><?= $title ?></title>
		<link rel="stylesheet" href="style.css" type="text/css">
		<?= JQUERY ?>
		<!--suppress ThisExpressionReferencesGlobalObjectJS -->
		<script>
			$(this).ready(function(){
				$("<p><?= $msg ?></p>").appendTo(document.body);
				setTimeout(function(){
					$("<p>You will be redirected back to the main page in 3 seconds.</p>").appendTo(document.body);
				}, 2000);
				setTimeout(function(){
					window.location = "phar.php";
				}, 5000);
			});
		</script>
	</head>
	</html>
	<?php
	die;
}

if(!isset($_FILES["zip"])){
	redirect("phar.php");
}
$filePath = $_FILES["zip"]["tmp_name"];
$fileName = $_FILES["zip"]["name"];
$fileError = $_FILES["zip"]["error"];
$inspections = [];
if(isset($_POST["inspection_classpath"]) and $_POST["inspection_classpath"] === "on"){
	$inspections[] = new ClasspathInspection;
}
if(isset($_POST["inspection_badpractice"]) and $_POST["inspection_badpractice"] === "on"){
	$inspections[] = new BadPracticeInspection;
}
$tunes = [];
if(isset($_POST["tune_topnamespace"]) and $_POST["tune_topnamespace"] === "on"){
	$tunes[] = new TopNamespaceTuner;
}
$presentFlags = TokenPresentor::FLAG_APPEND_ORIGINAL_BASE64;
if(isset($_POST["presentor_obfuscation"]) and $_POST["presentor_obfuscation"] === "on"){
	$presentFlags |= TokenPresentor::FLAG_OBFUSCATE;
}
if(!is_file($filePath)){
	failWithMsg("Invalid file", "Please provide a valid ZIP file");
}
if($fileError > 0){
	$failMsg = "$fileError (Unknown)";
	switch($fileError){
		case 1:
			$failMsg = "UPLOAD_ERR_INI_SIZE 1 (The file exceeded the max size: " . ini_get("upload_max_filesize") . ")";
			break;
		case 2:
			$failMsg = "UPLOAD_ERR_FORM_SIZE 2 (The file is too large)";
			break;
		case 3:
			$failMsg = "UPLOAD_ERR_PARTIAL 3 (The file is only partially uploaded, please retry with a better connection)";
			break;
		case 4:
			$failMsg = "UPLOAD_ERR_NO_FILE 4 (No files uploaded)";
			break;
		case 6:
			$failMsg = "UPLOAD_ERR_NO_TMP_DIR 6 (Server internal error)";
			break;
		case 7:
			$failMsg = "UPLOAD_ERR_CANT_WRITE 7 (Server internal error)";
			break;
		case 8:
			$failMsg = "UPLOAD_ERR_EXTENSION 8 (Server internal error)";
			break;
	}
	failWithMsg("Error during upload", "An error occurred during upload:<br>$failMsg");
}

$builder = new PharBuilder($filePath);
$builder->extract();
foreach($inspections as $insp){
	if($builder->errorId === PharBuilder::NO_ERROR){
		$builder->inspect($insp);
	}else{
		break;
	}
} // inspect
if($builder->errorId === PharBuilder::NO_ERROR){
	$builder->tune($tunes, new TokenPresentor($presentFlags));
} // tune
if($builder->errorId === PharBuilder::NO_ERROR){
	$builder->build($fileName);
} // build
if($builder->errorId !== PharBuilder::NO_ERROR){
//	switch($builder->errorId){
//		case PharBuilder::ERROR_OPENZIP:
//			failWithMsg("Error opening ZIP file", "Failed opening ZIP file: $builder->errorMsg");
//			break;
//		case PharBuilder::ERROR_EXTRACT:
//			failWithMsg("Error extracting ZIP", "Failed extracting ZIP: $builder->errorMsg");
//			break;
//		case PharBuilder::ERROR_NOPLUGINYML:
//			failWithMsg("Missing plugin.yml", "Cannot locate the <code class='code'>plugin.yml</code> in your plugin!");
//			break;
//		case PharBuilder::ERROR_TUNING:
//			failWithMsg("Error tuning plugin", "Error tuning plugin: $builder->errorMsg");
//	}
	failWithMsg(preg_replace('/<[^>]+>/', "", strstr($builder->errorMsg, ": ", true)), $builder->errorMsg);
} // show error and fail gracefully
?>

<html>
<head>
	<title>Zip-To-Phar</title>
	<link rel="stylesheet" href="style.css" type="text/css">
	<?= JQUERY ?>
	<script>
		function onDlClick(){
			var value = $("#name").val();
			window.location = <?= json_encode("/" . substr(PUBLIC_DATA_PATH, strlen(htdocs)) . "index.php/") ?> + value + <?= json_encode("?path=" . urlencode($fileName . "." . $builder->randomName . ".phar")) ?>;
		}
	</script>
</head>
<body>
<h1 class="title">Zip-To-Phar Converter</h1>
<p>Download with filename:</p>
<input type="text" name="path" value="<?= htmlspecialchars(substr($fileName, 0, -4) . ".phar") ?>" id="name">
<button id="download" onclick="onDlClick();">Download</button>

<hr>
<footer>Request processed in <?= 1000 * (microtime(true) - $pageStartTime) ?> ms</footer>

</body>
</html>
