<?php if(!isset($_GET["main"])) $_GET["main"] = "/phar.php"; ?>
<html><head><title>PocketMine Plugin Making Tools</title></head>
<frameset rows="120,*">
	<frame src="/pocketmine-frame-up.php" name="index" scrolling="on">
	<frame
		src="/pocketmine-frame-down.php?main=<?php echo urlencode($_GET["main"]) ?>"
		name="bodyframe" scrolling="off">
</frameset>
</html>
