<?php
set_error_handler(function($n, $msg, $file, $line){
	header("Content-Type: text/plain");
	echo "Error: $msg at $file#$line";
	http_response_code(500);
	exit;
});
if(isset($_FILES["phar"])){
	$newApi = "2.0.0";
	if(isset($_REQUEST["api"])) $newApi = $_REQUEST["api"];
	$path = $_FILES["phar"]["tmp_name"];
	$extended = $path . ".phar";
	move_uploaded_File($path, $extended);
	$phar = new Phar($extended);
	$phar->startBuffering();
	if(!isset($phar["plugin.yml"])){
		header("Content-Type: text/plain");
		echo "plugin.yml missing";
		exit;
	}
	$contents = file_get_contents($phar["plugin.yml"]);
	$yaml = yaml_parse($contents);
	if(!is_array($yaml["api"])) $yaml["api"] = [$yaml["api"]];
	if(in_array($newApi, $yaml["api"])){
		header("Content-Type: text/plain");
		echo "API version $newApi is already declared.";
		exit;
	}
	$yaml["api"][] = "2.0.0";
	$contents = yaml_emit($yaml);
	$phar->addFromString("plugin.yml", $contents);
	$phar->stopBuffering();
	header("Content-Type: application/octet-stream");
	echo file_get_contents($extended);
	exit;
}
?>
<html>
<head>
	<title>Plugin API version bumper</title>
</head>
<body>
	<h1>API 2.0.0 injection</h1>
	<p>Before using this tool, please carefully read the following cautions:</p>
	<ul>
		<li>This tool only <em>forces</em> the plugin to say that it supports API 2.0.0 (PHP 7 update). It will not fix the actual incompatibility issues.</li>
		<li>If errors happen after using phars downloaded from this page, unintsall it immediately.</li>
		<li>Click <button onclick='document.getElementById("upload").disabled = false;'>this button</button> if you have read the above.</li>
	</ul>
	<hr>
	<form action="updated.phar" method="post" enctype="multipart/form-data">
		<input type="file" name="phar"><br>
		No, I don't want API <code>2.0.0</code>. I want something else: <input type="text" name="api" value="2.0.0"><br>
		<input type="submit" id="upload" value="Inject" disabled>
	</form>
</body>
</html>

