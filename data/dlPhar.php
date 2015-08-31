<?php
if(!isset($_GET["path"])){
	http_response_code(400);
	exit;
}
?>
<html>
<head>
	<script>
		function download(name){
			var path = <?= json_encode($_GET["path"]) ?>;
			var loc = "/data/phars/" + path + "/" + name;
			window.location = loc;
		}
	</script>
</head>
<body>
<input type="text" id="dlPhar_name" placeholder="File name to download as (with the extension!)">
<button onclick="download(document.getElementById('dlPhar_name').value);">Download</button>
</body>
</html>
