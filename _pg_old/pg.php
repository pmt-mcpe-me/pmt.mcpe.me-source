<?php
include_once "pgFx.php";
if(isInitialized()){
	header("Location: main.php");
	return;
}
?>
<html>
<head>
	<title>Plugin Generator</title>
</head>
<body>
<h1>Plugin Generator</h1>
<hr>
<?php
if(isset($_GET["notice"])){
	printf("<p>%s</p>", $_GET["notice"]);
	echo "<hr>";
}
?>
<form action="init.php" method="post">
	<h3><strong>Plugin Basic Information</strong></h3>
	<table>
		<tr>
			<th align="right">Plugin Name</th>
			<td align="left">
				<input name="name" type="text">
			</td>
		</tr>
		<tr>
			<th align="right">Version</th>
			<td align="left">
				<input name="version" type="text" value="1.0.0">
			</td>
		</tr>
		<tr>
			<th align="right">Author</th>
			<td align="left">
				<input name="author" type="text" value="">
			</td>
			<td aligh="left">
				<em>Separate multiple authors by commas (<code>,</code>)</em>
			</td>
		</tr>
	</table>
	<input type="submit" value="Start">
</form>
</body>
</html>
