<html><head>
	<title>Phar creation result</title>
</head><body>
<?php
include "C:\\Apache24\\utils\\functions.php";
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
$zip = new ZipArchive;
if(($err = $zip->open($file["tmp_name"])) !== true){
	echo "<h1>Error extracting your ZIP file.</h1>";
	echo "<p>Error: ";
	switch($err){
		case ZipArchive::ER_EXISTS:
			echo "ER_EXISTS($err) File already exists";
			break;
		case ZipArchive::ER_INCONS:
			echo "ER_INCONS($err) Zip archive inconsistent";
			break;
		case ZipArchive::ER_INVAL:
			echo "ER_INVAL($err) Invalid argument";
		case ZipArchive::ER_MEMORY:
			echo "ER_MEMORY($err) Malloc failure";
			break;
		case ZipArchive::ER_NOENT:
			echo "ER_NOENT($err) No such file";
		case ZipArchive::ER_NOZIP:
			echo "ER_NOZIP($err) This is not a ZIP file";
			break;
		case ZipArchive::ER_OPEN:
			echo "ER_OPEN($err) Cannot open file";
			break;
		case ZipArchive::ER_READ:
			echo "ER_READ($err) Read error";
			break;
		case ZipArchive::ER_SEEK:
			echo "ER_SEEK($err) Seek error";
			break;
		default:
			echo "Unknown open error ";
			var_dump($err);
			exec("start " . $file["tmp_name"]);
			break;
	}
	echo "</p>";
	goto the_end;
}
$dir = TMP_PATH;
for($i = 0; file_exists($dir . "$i\\"); $i++);
mkdir($dir = $dir . "$i\\");
if(!$zip->extractTo($dir)){
	echo "<h1>Error extracting your ZIP file.</h1>";
	goto the_end;
}
if(!is_file($dir . "plugin.yml")){
	echo "<p><b>Warning</b>: Cannot find plugin.yml in ZIP root!</p>";
	$results = [];
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file){
		if(basename($file) === "plugin.yml"){
			$real = realpath($file);
			$include = str_replace("\\", "/", substr($real, strlen(realpath($dir))));
			$slashCount = 0;
			for($pos = 0; ($pos = strpos($include, "/", $pos + 1)) !== false; $slashCount++);
			$htmlInclude = htmlspecialchars($include);
			$results[] = ["real" => $real, "include" => $include, "htmlInclude" => $htmlInclude, "slashCount" => $slashCount];
		}
	}
	if(count($results) === 0){
		echo "<h1>Failed to create phar</h1><p><code>plugin.yml</code> cannot be found in the ZIP file.</p>";
		goto the_end;
	}
	if(count($results) > 1){
		echo "<p>The following occurrences of <code>plugin.yml</code> are found in the ZIP file:";
		echo "<ul>";
		$minReal = null;
		$min = null;
		$minCnt = PHP_INT_MAX;
		foreach($results as $result){
			extract($result);
			echo "<li>$htmlInclude</li>";
			/** @noinspection PhpUndefinedVariableInspection */
			if($minCnt > $slashCount){
				$minCnt = $slashCount;
				$min = $include;
				$minReal = $real;
			}
		}
		echo "</ul>";
		echo "<br>";
		echo "Selecting $min as the <code>plugin.yml</code> to build around with.</p>";
		$dir = dirname($minReal) . "\\";
	}
	else{
		extract($results[0]);
		/** @noinspection PhpUndefinedVariableInspection */
		echo "<p>Selecting $htmlInclude as the <code>plugin.yml</code> to build around with.</p>";
		$dir = dirname($real);
	}
}
while(is_file($file = DATA_PATH . ($subpath = "phars/" . randomClass(16, "phar") . ".phar")));
$phar = new Phar($file);
$phar->setStub($_POST["stub"]);
$phar->setSignatureAlgorithm(Phar::SHA1);
$phar->startBuffering();
$phar->buildFromDirectory($dir);
/** @var Phar $other */
$other = $phar->compress(Phar::GZ);
$gzPath = substr(realpath($other->getPath()), strlen(realpath("C:\\Apache24\\htdocs\\")));
$phar->stopBuffering();
$url = "data/$subpath";
echo <<<EOP
<h1>Phar has been successfully created.</h1>
<p><a href="$url">Download the phar here.</a><br>
<a href="$gzPath">You can also download this GZIP-compressed archive.</a> It is smaller in size to download, but you have to extract it yourself.</p>
EOP;
echo "<hr>";
echo "<h2>Inspections</h2>";
echo "<ul>";
if($_POST["inspection_classpath"] === "on"){
	echo "<li>Check classpath<ul>";
	$pluginYml = $dir . "plugin.yml";
	$res = fopen($pluginYml, "rt");
	$desc = [];
	while(($line = fgets($res)) !== false){
		$line = trim($line);
		list($key, $value) = explode(": ", $line, 2);
		$desc[$key] = $value;
	}
	if(!isset($desc["main"])){
		echo "<li><code>main</code> attribute missing from <code>plugin.yml</code>!</li>";
		echo "<li>Result: Inspection failed</li>";
	}
	else{
		$mainClass = $desc["main"];
		$tokens = explode("\\", $mainClass);
		$namespace = implode("\\", array_slice($mainClass, 0, -1));
		$mainClassName = array_slice($mainClass, -1)[0];
		echo "<li><code>main</code> attribute scanned: <code>$mainClass</code></li>";
		$should = "src/" . str_replace("\\", "/", $desc["main"]) . ".php";
		echo "<li>The main class should be located at <code>$should</code></li>";
		if(is_file($dir . $should)){
			echo "<li>File found at the correct place.</li>";
			echo "<li>Main class namespace checking<ul>";
			$mainClassCode = file_get_contents($dir . $should);
			while(strpos($mainClassCode, "  ") !== false){
				$mainClassCode = str_replace("  ", " ", $mainClassCode);
			}
			$namespaceStmt = "namespace $namespace";
			$useStmt = "use pocketmine\\plugin\\PluginBase";
			$classStmt = "class $mainClassName extends ";
			if(strpos($mainClassCode, $namespaceStmt) === false){
				echo "<li>Warning: expected namespace declaration statement <code>$namespaceStmt</code> not found</li>";
			}
			if(strpos($mainClassCode, $useStmt) === false){
				echo "<li>Notice: the PluginBase alias use statement <code>$useStmt</code> not found</li>";
			}
			if(strpos($mainClassCode, $classStmt) === false){
				echo "<li>Warning: expected class declaration statement <code>$classStmt</code> not found</li>";
			}
			echo "</ul></li>";
		}
	}
	echo "</ul></li>";
}
echo "</ul>";
the_end:
?>
</body></html>
