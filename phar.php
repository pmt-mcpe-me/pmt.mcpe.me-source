<html>
<head>
	<title>Create phar</title>
</head>
<body><font face="Comic Sans MS">
	<h1>Phar maker</h1>
	<h3>How to use</h3>
	<ol>
		<li>Write your plugin, of course :D</li>
		<li>Put your files in the correct structure (according to namespaces, etc.)</li>
		<li>Create a ZIP file and throw your structure into it. You can put it anywhere inside the ZIP file, but only the folder with plugin.yml (and subfolders) will be included.</li>
		<li>Upload that file below :)</li>
	</ol>
	<form method="post" action="/phar-result.php" enctype="multipart/form-data">
		<p><input type="file" name="file"></p>
		<p>Stub (Leave as default unless you know what it is):
			<?php
			echo "<input type=\"text\" name=\"stub\" value=\"";
			echo '<?php __HALT_COMPILER();';
			echo "\" size=\"32\">";
			?>
		</p>
		<p>Tune the plugin using the following methods: <br>
			<input type="checkbox" name="tune_top_namespace_optimization">
				Optimize constant references by adding a
				<code>\</code> prefix to indicate that it is a top namespace reference.<br>
			<input type="checkbox" name="tune_obfuscation"> Obfuscate code
		</p>
		<p><font color="#8b0000">Warning: Using any of the tunes may stripe out all the
			comments in your code <em>except</em> PHP doc comments
			or line comments.</font></p>
		<p>
			Carry out the following inspections too: <br>
			<input type="checkbox" name="inspection_classpath"> Check classpath<br>
			<input type="checkbox" name="inspection_bad_practice"> Scan for bad practice<br>
			<input type="checkbox" name="inspection_lint"> Syntax error lint scan
		</p>
		<p><input type="submit" value="create phar"></p>
	</form>
	<p>New: use the frames page <a href="pm.php" target="_parent">here</a> if you are not already using.</p>
<pre>
	Disclaimer:
	This service is provided absolutely free of charge and is not guaranteed always available.
	This page (phar.php) extracts ZIP files uploaded by users into a location in the server's filesystem that is not available to users, executes tuning and packs the result into a *.phar file that is available for public download. This process is 100% automated.
	This website (http://pmt.mcpe.me) is in no ways affiliated with PocketMine-MP (http://pocketmine.net), an open-source project developed by the PocketMine Team, or Minecraft: PE, a game software developed by Mojang.
	We (owner of this website) are not to be held responsible for any acts related to copyright breaches and other illegal acts. All contents in the downloaded phar are either generated using the uploaded file or the software used for this website.
</pre>
</font></body>
</html>
