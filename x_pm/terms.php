<?php include_once __DIR__ . "/functions.php"; ?>
<html>
<head>
	<title>Terms of Service</title>
</head>
<body style="font-family: monospace; line-height: 150%;">
	<h1 style="padding-top: 10px">Terms of Service</h1><hr>
<p>
	This website, <?= WEBSITE_NAME ?>, is a free service developed by <?= AUTHOR ?>
	and hosted by <?= HOST ?>.<br>
	All services on this website are provided absolutely free of charge.
	The source of this website can be found at
	<a href="https://github.com/PEMapModder/web-server-source/tree/master/x_pm/">
		https://github.com/PEMapModder/web-server-source/tree/master/x_pm/
	</a>. The source is open for reading, forking, editing and being used on other servers.<br>
	The tools on this website may generate results from some probably copyrighted work:
</p>
<ul>
	<li>Part of the PocketMine-MP source</li>
	<li>Work owned by the author or the host of this website </li>
	<li>Some response from the server software that hosts this website</li>
	<li>User-uploaded content</li>
</ul>
<p>
	We are not to be responsible for any copyright breaches due to creating results from user-uploaded content.<br>
	Data on this website may be lost or be unavailable. This service is not guaranteed to be always available. We are not to be responsible for any data loss for data submitted to this service.
</p>
<p>Click <a href="agreeTerms.php">here</a> <?= isset($_SESSION["pm_terms_agreed"]) ? "" : "if you agree with the terms and would like" ?> to continue.</p>
</body>
</html>
