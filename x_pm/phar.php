<?php
include_once __DIR__ . "/functions.php";
forceTerms();
?>
<html>
<head>
	<title>Zip-to-Phar Converter</title>
	<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<h1 class="title">Zip-to-Phar Converter</h1>
<hr>
<form action="pharResult.php" method="post" enctype="multipart/form-data">
	<p>This tool allows you to create *.phar files from your source files.</p>
	<p>
		First, create an empty directory on your device anywhere. <span class="hint">(If you are Android, an app called <a href="https://play.google.com/store/apps/details?id=it.medieval.blueftp">Bluetooth File Transfer</a> is recommended for file management)</span>. Let's call it <code class="code">/path/to/my/dir</code>.<br>
		Second, inside that directory, create a file called <code class="code">plugin.yml</code>. Fill it with the information about the plugin. You can find official documentation about <code class="code">plugin.yml</code> <a href="https://github.com/PocketMine/Documentation/wiki/Plugin-Tutorial#creating-the-pluginyml">here</a>.<br>
		Third, write your code for the plugin. You can find documentation for the PocketMine plugin API <a href="https://github.com/PocketMine/Documentation/wiki/Plugin-Tutorial">here</a>. <span class="hint">If you don't want to write code, use the <a href="<?= PLUGIN_GENERATOR_PATH ?>">plugin generator developed by PEMapModder</a> instead.</span> <em>Make sure your PHP files are arranged according to <a href="http://www.php-fig.org/psr/psr-4/">the PSR-4 class files standard</a>.</em> The source directory should be <code class="code">/path/to/my/dir/src/</code> if <code class="code">plugin.yml</code> is at <code class="code">/path/to/my/dir/plugin.yml</code>.<br>
		Fourth, create a ZIP file that contains your <code class="code">plugin.yml</code> file and <code class="code">src/</code> directory, and the <code class="code">resources/</code> directory if there is one. Where in the ZIP file the <code class="code">plugin.yml</code> file and other files are does not matter; just ensure that they are at the same directory inside the ZIP. The converter will automatically detect the <code class="code">plugin.yml</code> file and build a plugin based on it.<br>
		Fifth, submit your ZIP file by clicking the buttons below!
	</p>
	<hr>
	<input type="file" name="zip" class="button">
	<hr>
	<p><strong>Execute the following inspections:</strong></p>
	<ul>
		<li class="checkbox">
			<input type="checkbox" name="inspection_classpath" checked>&nbsp;&nbsp;&nbsp;&nbsp;
			<label for="inspection_classpath">Check if the class path is correct</label>
		</li>
		<li class="checkbox">
			<input type="checkbox" name="inspection_badpractice" checked>&nbsp;&nbsp;&nbsp;&nbsp;
			<label for="inspection_badpractice">Scan for bad practices</label>
		</li>
	</ul>
	<p><strong>Tune the code with:</strong></p>
	<ul>
		<li class="checkbox">
			<input type="checkbox" name="tune_topnamespace">&nbsp;&nbsp;&nbsp;&nbsp;
			<label for="tune_topnamespace">Add a <code class="code">\</code> before calls of top namespace functions (e.g. <code class="code">count()</code>, <code class="code">substr()</code>, etc.)</label>
		</li>
	</ul>
	<p><strong>And output the code with:</strong></p>
	<ul>
		<li class="checkbox">
			<input type="checkbox" name="presentor_obfuscate">&nbsp;&nbsp;&nbsp;&nbsp;
			Obfuscation
		</li>
	</ul>
	<input type="submit" class="button" value="Generate!">
</form>
<hr>
<p align="center"><a href="terms.php">Terms</a> | <a href="./">Home</a></p>
</body>
</html>
