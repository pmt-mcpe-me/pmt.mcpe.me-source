<?php

const debugOn = false;

set_error_handler(function ($n, $msg, $file, $line){
	header("Content-Type: text/plain");
	echo "Error: $msg at $file#$line";
	http_response_code(500);
	exit;
});
if(isset($_FILES["phar"])){
	$newApi = "2.0.0";
	if(isset($_REQUEST["diffapi"]) and $_REQUEST["diffapi"] === "on" and isset($_REQUEST["api"])){
		$newApi = $_REQUEST["api"];
	}
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
	if(!is_array($yaml["api"])){
		$yaml["api"] = [$yaml["api"]];
	}
	if(in_array($newApi, $yaml["api"])){
		header("Content-Type: text/plain");
		echo "API version $newApi is already declared.";
		exit;
	}
	$yaml["api"][] = "2.0.0";
	$contents = yaml_emit($yaml);
	$phar->addFromString("plugin.yml", $contents);

	if(debugOn){
		header("Content-Type: text/plain");
	}

	$replaceUse = ($_REQUEST["nbt-use"] ?? "off") === "on";
	$replaceFQN = ($_REQUEST["nbt-fqn"] ?? "off") === "on";
	$replaceNew = ($_REQUEST["nbt-new"] ?? "off") === "on";
	$replaceInstanceOf = ($_REQUEST["nbt-instanceof"] ?? "off") === "on";
	if($replaceUse or $replaceFQN or $replaceNew or $replaceInstanceOf){
		$refactorTable = [
			"ByteArray" => "ByteArrayTag",
			"Byte" => "ByteTag",
			"Compound" => "CompoundTag",
			"Double" => "DoubleTag",
			"End" => "EndTag",
			"Float" => "FloatTag",
			"IntArray" => "IntArrayTag",
			"Int" => "IntTag",
			"Enum" => "ListTag",
			"Long" => "LongTag",
			"Short" => "ShortTag",
			"String" => "StringTag",
		];
		foreach(new \RecursiveIteratorIterator($phar) as $localName => $file){
			$fn = $file->getFileName();
			$localName = substr($localName, 8 + strlen($extended));
			if(substr($fn, -4) === ".php"){
				$c = $o = file_get_contents($file->getPathName());
				if($replaceUse){
					$ns = "pocketmine\\nbt\\tag\\";
					$quotedNs = preg_quote($ns);
					$names = implode("|", array_map("preg_quote", array_keys($refactorTable)));
					$c = preg_replace_callback("/use[ \t\n\r]+$quotedNs($names)[ \t\n\r]*;/i", function ($match) use ($ns, $refactorTable){
						return "use $ns" . $refactorTable[$match[1]] . ";";
					}, $c);
				}
				if($replaceFQN){
					foreach($refactorTable as $from => $to){
						$from = "\\pocketmine\\nbt\\tag\\$from";
						$to = "\\pocketmine\\nbt\\tag\\$to";
						$c = str_replace($from, $to, $c);
					}
				}
				if($replaceNew){
					$ns = "pocketmine\\nbt\\tag\\";
					$quotedNs = preg_quote($ns);
					$names = implode("|", array_map("preg_quote", array_keys($refactorTable)));
					$c = preg_replace_callback("/new[ \t\n\r]+($names)[ \t\n\r]*([\(\);,])/i", function ($match) use ($ns, $refactorTable){
						return "new " . $refactorTable[$match[1]] . $match[2];
					}, $c);
				}
				if($replaceInstanceOf){
					$ns = "pocketmine\\nbt\\tag\\";
					$quotedNs = preg_quote($ns);
					$names = implode("|", array_map("preg_quote", array_keys($refactorTable)));
					$c = preg_replace_callback("/instanceof[ \t\n\r]+$quotedNs($names)/i", function ($match) use ($ns, $refactorTable){
						return "instanceof " . $refactorTable[$match[1]];
					}, $c);
				}
				if(debugOn){
					echo $localName, " ($fn):\n", $c, "\n\n";
				}
				if($c !== $o){
					$phar->addFromString($localName, $c);
				}
			}
		}
	}

	$phar->stopBuffering();
	if(debugOn){
		echo base64_encode(file_get_contents($extended));
		die;
	}
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
<p>Before using this tool, please carefully read the following caution:</p>
<ul>
	<li>This tool only <em>forces</em> the plugin to say that it supports API 2.0.0 (PHP 7 update), and optionally,
		blindly replaces some specific backwards-incompatible changes in the phar. It will not fix the actual
		incompatibility issues.
	</li>
	<li>If errors happen after using phars downloaded from this page, unintsall it immediately.</li>
	<li>Click <span
			onclick='alert("Thanks for carefully reading the caution!"); document.getElementById("upload").disabled = false;'>these three words</span>
		if you have read the above.
	</li>
</ul>
<hr>
<form action="updated.phar" method="post" enctype="multipart/form-data">
	<p><input type="file" name="phar"></p>
	<ul>
		<li>
			<input type="checkbox" name="diffapi" onclick='document.getElementById("apiInput").disabled = false;'> No, I
			don't want API <code>2.0.0</code>.
			I want something else: <input id="apiInput" type="text" name="api" value="2.0.0" disabled>
		</li>
		<li>
			Replace the following usage of NBT tags:
			<ul>
				<li><input type="checkbox" name="nbt-use" checked> <code>use pocketmine\nbt\tag\****Tag</code></li>
				<li><input type="checkbox" name="nbt-fqn" checked> fully-qualified
					<code>\pocketmine\nbt\tag\****Tag</code></li>
				<li><input type="checkbox" name="nbt-new" checked> <code>new ****Tag</code></li>
				<li><input type="checkbox" name="nbt-instanceof" checked> <code>instanceof ****Tag</code></li>
			</ul>
		</li>
	</ul>
	<p><input type="submit" id="upload" value="Inject" disabled></p>
</form>
</body>
</html>

