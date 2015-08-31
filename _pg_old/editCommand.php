<?php
include_once "pgFx.php";
if(!isInitialized()){
	header("Location: pg.php");
	return;
}
if(!isset($_GET["name"])){
	header("Location: main.php");
	return;
}
$plugin = getPlugin();
$name = $_GET["name"];
$escapedName = var_export($name, true);
if(!isset($plugin->cmds[$name])){
	echo <<<EOP
<html>
<head>
<title>Error | Edit Command | Plugin Generator</title>
</head>
<body>
<script>
alert("Unknown command: " + $escapedName);
location = "main.php";
</script>
</body>
</html>
EOP;
	return;
}
$cmd = $plugin->cmds[$name];
$name = htmlspecialchars($name);
?>
<html>
<head>
	<title>Edit Command | Plugin Generator</title>
	<script src="js/editCommandRequests.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</head>

<body>
<table width="100%">
	<tr>
		<td>
			<h1>Editing command: <code>/<?php echo $name ?></code></h1>
		</td>
		<td align="right" valign="top">
			<button onclick="location = './'">Return to Main Page</button>
		</td>
	</tr>
</table>
<hr>

<p><b>Name</b>: <code>/<?php echo $name ?></code></p>
<p><button onclick="changeCmdName(<?php var_export($name) ?>);">Change command name</button></p>

<p><b>Description</b>: <a id="property_desc"><?php echo htmlspecialchars($cmd->desc) ?></a></p>
<p><button onclick="changeCmdProperty(<?php var_export($name) ?>, 'desc', document.getElementById('property_desc').textContent);">Change command description</button></p>

<p><b>Usage</b>: <a id="property_usage"><?php echo htmlspecialchars($cmd->usage) ?></a></p>
<p><button onclick="changeCmdProperty(<?php var_export($name) ?>, 'usage', document.getElementById('property_usage').textContent);">Change command usage</button></p>
<hr>
<h2>Operations run when the command is issued:</h2>
<?php
$cmd->toHtml();
?>
<button onclick="location = 'addOperation.php?class=cmd&name=' + <?php echo $escapedName; ?>">Add Operation</button>
<br>
</body>
</html>
