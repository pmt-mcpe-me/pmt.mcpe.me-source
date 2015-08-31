<?php
include_once "pgFx.php";
if(!isInitialized()){
	header("Location: pg.php");
	return;
}
if(!isset($_GET["name"], $_GET["desc"], $_GET["usage"])){
	$_GET["name"] = "";
	$_GET["desc"] = "";
	$_GET["usage"] = "";
}
?>
<html>
<head>
	<title>Add command | Plugin Generator</title>
	<script>
		var usageChanged = <?php echo ($_GET["usage"] === "") ? "false":"true"; ?>;
		function onUsageChange(){
			usageChanged = true;
		}
		function onNameChange(){
			if(!usageChanged){
				var name = document.getElementById("input_name").value;
				var usage = document.getElementById("input_usage");
				usage.value = "/" + name;
			}
		}
	</script>
</head>
<body>
<h1>Add command</h1>
<hr>
<?php
if(isset($_GET["notice"])){
	echo "<font color='#D02020'>";
	echo $_GET["notice"];
	echo "</font>";
	echo "<hr>";
}
?>
<form action="addCommandCallback.php" method="post">
	<table>
		<tr>
			<td>Name</td>
			<td>
				<input type="text" name="name" value="" id="input_name" onchange="onNameChange();">
			</td>
			<td>
				<em>Notes: The command name <strong>must not</strong> contain spaces or colons.</em>
			</td>
		</tr>
		<tr>
			<td>Description</td>
			<td>
				<input type="text" name="desc">
			</td>
		</tr>
		<tr>
			<td>Usage message</td>
			<td>
				<input type="text" name="usage" id="input_usage" onchange="onUsageChange();">
			</td>
		</tr>
		<tr>
			<td><input type="submit" value="Add"></td>
		</tr>
	</table>
</form>
</body>
</html>
