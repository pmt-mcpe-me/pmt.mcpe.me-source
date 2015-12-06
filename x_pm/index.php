<?php
include_once __DIR__ . "/functions.php";
forceTerms();
?>
<html>
<head>
	<title>PocketMine Plugin Tools</title>
	<link rel="stylesheet" href="style.css" type="text/css">
	<?= JQUERY ?>
	<script>
		$(".link").keypress(function(e){
			alert("KeyPress");
			if(e.which == 13){
				alert("Enter");
				$(this).click();
			}
		});
	</script>
</head>
<body>
<h1 class="title">PocketMine Plugin Tools</h1>
<hr>
<ul class="list">
	<li onclick="window.open('phar.php', '_blank');" tabindex="100" class="link">
		<span class="link">Zip-To-Phar Converter</span>
	</li>
	<li onclick="window.open('unphar.php', '_blank');" tabindex="101" class="link">
		<span class="link">Phar-to-Zip Converter</span>
	</li>
</ul>
</body>
</html>
