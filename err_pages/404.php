<html>
<head>
	<title>404 Not Found</title>
	<style type="text/css">
		body{
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
		}
	</style>
</head>
<body>
<h1>Oops</h1>
<p>It seems that you have entered a wrong link. This page, <?= $_SERVER["REQUEST_URI"] ?>, does not exist on our server.</p>
<p><?= $referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "Nobody" ?> directed you here.</p>
<p>Considering going <a href="javascript:window.history.back();">back</a>?</p>
</body>
</html>

<?php
touch($log = "/var/www/404.log");
file_put_contents($log, PHP_EOL . date(DATE_ATOM) . " | " . $_SERVER["REQUEST_URI"] . " | " . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "no-referer"), FILE_APPEND);
?>
