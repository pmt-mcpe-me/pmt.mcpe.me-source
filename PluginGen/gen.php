<html><head>
	<title>PocketMine-MP Zekkou Cake Plugin Generator</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="js/validateForm.js"></script>
	<script src="js/addFormContent.js"></script>
</head><body>
	<form method="POST" action="step-2.php">
		<h3>Step 1: Plugin Information</h3>
		<p id="p_name">Plugin name: <input type="text" name="name" value="ExamplePlugin" id="_name"></p>
		<p id="p_author">Your name: <input type="text" name="author" value="Anonymous" id="_author"></p>
		<p id="p_version">Version name: <input type="text" name="version" value="1.0.0" id="_version"></p>
		<p id="p_class">Class name:
		<?php
include "functions.php";
echo "<input type='text' name='classname' value='";
echo randomClass(16);
echo "\\";
echo randomClass(16);
echo "' id='_class'>";
		?>
		</p>
		<input type="submit" value="Next" id="_submit">
	</form>
</body></html>
