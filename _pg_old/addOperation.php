<?php
include_once "pgFx.php";
if(!isInitialized()){
	header("Location: pg.php");
	return;
}
if(!isset($_GET["name"], $_GET["class"])){
	header("Location: main.php");
	return;
}
$plugin = getPlugin();
$name = $_GET["name"];
$class = $_GET["class"];
$escapedName = var_export($name, true);
if($class === "cmd"){
	if(!isset($plugin->cmds[$name])){
		echo <<<EOP
<html>
<head>
<title>Error | Add Operation | Edit Command | Plugin Generator</title>
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
}
elseif($class === "event"){
	// TODO
}
else{
	$escapedClass = var_export($class,  true);
	echo <<<EOP
<html>
<head>
<title>Error | Add Operation | Edit Command | Plugin Generator</title>
</head>
<body>
<script>
alert("Unknown class: " + $escapedClass);
location = "main.php";
</script>
</body>
</html>
EOP;
	return;
}
$name = htmlspecialchars($name);
?>
<html>
<head>
	<title>Add Operation | Edit Command | Plugin Generator</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="js/addOperation.js"></script>
</head>
<body>
<h1>Add Operation</h1>
<form name="result" action="addOpCallback.php" method="get">
	<input type="hidden" name="class" value="<?php echo $_GET["class"]; ?>">
	<input type="hidden" name="name" value="<?php echo $_GET["name"]; ?>">
	<p>1. Choose what type of operation to add.</p>
	<select name="type">
		<option value="sendMsg">Send a message to a player or the command sender</option>
		<option value="broadcast">Broadcast a message</option>
	</select>
	<br>
	<input type="submit" value="Add">
</form>
</body>
</html>
