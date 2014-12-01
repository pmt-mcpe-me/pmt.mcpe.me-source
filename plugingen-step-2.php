<?php

include "functions.php";

$data = ["pluginYml" => []];
foreach(["name", "author", "version", "class"] as $k){
	if(!isset($_POST[" p_$k"])){
		http_response_code(400);
		return;
	}
	$data["pluginYml"][$k] = $_POST["p_$k"];
}

$sessionId = start_session($data);

?>

<html><head><title>Plugin Generator</title></head><body>
<form action="plugingen-step-3.php" method="post">
<?php
echo '<input type="hidden" name="ses_id" value="$sessionId">';
?>
</form>
</body></html>
