<?php
include("lang/setlang.php");
define('PACKAGE', 'unphar');

// gettext setting
bindtextdomain(PACKAGE, 'lang'); // or $your_path/lang, ex: /var/www/test/lang
textdomain(PACKAGE);
?>
<html>
<head>
	<title><?php echo _('Unphar'); ?></title>
</head>
<body><font face="Comic Sans MS">
<h1><?php echo _('Unphar'); ?></h1>
<form method="post" action="/unpharResult.php" enctype="multipart/form-data">
	<p><?php echo _('Upload the phar file here:'); ?><br><input type="file" name="file" accept=".phar"></p>
	<p><input type="submit" value="Convert"></p>
</form>
<pre>
	<?php echo _("Disclaimer:
	This service is provided absolutely free of charge and is not guaranteed to be always available.
	This page (unphar.php) only unpacks files uploaded from users from a phar format to a zip format, and in the process, the phar file is extracted into files on the server's filesystem (which is not expected to be accessible from the website). This page unpacks the uploaded files AS-IS, and the process is 100% automated.
	This website (http://pmt.mcpe.me) is in no ways affiliated with PocketMine-MP (http://pocketmine.net), an open-source project developed by the PocketMine Team, or Minecraft: PE, a game software developed by Mojang.
	We (owner of this website) are not to be held responsible for any acts related to copyright breaches and other illegal acts. All contents in the downloaded files are either generated using the uploaded file or the software used for this website."); ?>
</pre>
</font></body>
</html>
