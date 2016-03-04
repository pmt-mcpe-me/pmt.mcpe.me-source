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
<html><head>
	<title>Plugin API version bumper</title>
</head>
<body>
	<h1>API 2.0.0 injection</h1>
	<p>Before using this tool, please carefully read the following cautions:</p>
	<ul>
		<li>This tool only <em>forces</em> the plugin to say that it supports API 2.0.0 (PHP 7 update), and optionally, blindly replaces some specific backwards-incompatible changes in the phar. It will not fix the actual incompatibility issues.</li>
		<li>If errors happen after using phars downloaded from this page, unintsall it immediately.</li>
		<li><p onclick="document.getElementById(&quot;upload&quot;).disabled = false;">click this button you read the above.</p></li>
	</ul>
	<hr>
	<form action="updated.phar" method="post" enctype="multipart/form-data">
		<p><input type="file" name="phar"></p>
		<ul>
			<li>
				<input type="checkbox" name="diffapi" onclick="document.getElementById(&quot;apiInput&quot;).disabled = false;"> No, I don't want API <code>2.0.0</code>.
				I want something else: <input id="apiInput" type="text" name="api" value="2.0.0" disabled="">
			</li>
			<li>
				Replace the following usage of NBT tags:
				<ul>
					<li><input type="checkbox" name="nbt-use" checked=""> <code>use pocketmine\nbt\tag\****Tag</code></li>
					<li><input type="checkbox" name="nbt-fqn" checked=""> fully-qualified <code>\pocketmine\nbt\tag\****Tag</code></li>
					<li><input type="checkbox" name="nbt-new" checked=""> <code>new ****Tag</code></li>
					<li><input type="checkbox" name="nbt-instanceof" checked=""> <code>instanceof ****Tag</code></li>
				</ul>
			</li>
		</ul>
		<p><input type="submit" id="upload" value="Inject"></p>
	</form>



</body></html>
