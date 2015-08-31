<?php
if(isset($_GET["path"]) and strpos($path = $_GET["path"], "/") === false and strpos($path, "\\") === false){
	header("Content-Type: application/octet-stream");
	echo file_get_contents($path);
}else{
	http_response_code(400);
}
