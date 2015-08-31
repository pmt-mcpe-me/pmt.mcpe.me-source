<?php
include_once "pgFx.php";
if(!isInitialized()){
	header("Location: pg.php");
	return;
}
?>
<html>
<head>
	<title>Main | Plugin Generator</title>
	<script src="js/resetScript.js"></script>
</head>
<body>
<h1>Making plugin: <?php echo getPlugin()->name ?></h1>
<hr>
<table width="100%" border="1">
	<tr>
		<th width="15%">Command</th>
		<th>Description</th>
		<th>Usage</th>
		<th></th>
	</tr>
	<?php
	foreach(getPlugin()->cmds as $cmd){
		echo "<tr><td align='center'>/";
		echo htmlspecialchars($cmd->name);
		echo "</td><td align='center'>";
		echo htmlspecialchars($cmd->desc);
		echo "</td><td align='center'>";
		echo htmlspecialchars($cmd->usage);
		echo "</td><td align='center'>";
		echo "<a href='editCommand.php?name=" . urlencode($cmd->name) . "'>Edit</a>";
		echo "</td></tr>";
	}
	?>
</table>
<br>
<button onclick='location = "addCommand.php";'>Add command</button>
<br><br>
<hr>
<br>
<button onclick='location = "download.php";'>Build plugin</button> Clicking this button will not reset your plugin.
<br><br>
<hr>
<br>
<button onclick='confirmReset("<?php echo getPlugin()->name ?>")'>Reset your plugin</button>
<br>
</body>
</html>