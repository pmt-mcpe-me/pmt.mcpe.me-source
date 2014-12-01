<html>
	<head><title>Create phar</title></head>
	<body>
		<h2>Phar maker</h2>
		<h3>How to use</h3>
		<ol>
			<li>Write your plugin, of course :D</li>
			<li>Put your files in the correct structure (according to namespaces, etc.)</li>
			<li>Create a ZIP file and throw your structure into it. You can put it anywhere inside the ZIP file, but only the folder with plugin.yml (and subfolders) will be included.</li>
			<li>Upload that file below :)</li>
		</ol>
		<form method="post" action="phar-result.php" enctype="multipart/form-data">
			<p><input type="file" name="file"></p>
			<p>Stub (Leave as default unless you know what it is):
				<?php
				echo "<input type=\"text\" name=\"stub\" value=\"";
				echo '<?php __HALT_COMPILER();';
				echo "\" size=\"32\">";
				?>
			</p>
			<p>Carry out the following inspections too: <br>
			<input type="checkbox" name="inspection_classpath"> Check classpath
			</p>
			<p><input type="submit" value="create phar"></p>
		</form>
		<p>New: use the frames page <a href="pm.php" target="_parent">here</a> if you are not already using.</p>
		<!--Server under maintenance. Please come back later.-->
	</body>
</html>
