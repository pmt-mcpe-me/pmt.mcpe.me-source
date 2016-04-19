<?php
include("lang/setlang.php");
define('PACKAGE', 'index');

// gettext setting
bindtextdomain(PACKAGE, 'lang'); // or $your_path/lang, ex: /var/www/test/lang
textdomain(PACKAGE);
?>
<html>
<head>
	<title><?php echo _('Crash dump parser'); ?></title>
	<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script>
		$(document).ready(function(){

		});
	</script>
</head>
<body>
<p><?php echo _('Please choose a method to send the crash dump.'); ?></p>
<hr>
<form action="result.php" method="post" enctype="multipart/form-data">
	<input type="radio" name="method" value="buffer" checked> <?php echo _('Paste crash dump'); ?>
	<br>
	<pre>----------------------<?php echo _('REPORT THE DATA BELOW THIS LINE'); ?>-----------------------
===<?php echo _('BEGIN CRASH DUMP'); ?>===
<textarea name="buffer" cols="100" rows="30"></textarea>
===<?php echo _('END CRASH DUMP'); ?>===
	</pre>
	<hr>
	<input type="radio" name="method" value="file"> <?php echo _('{EXPERIMENTAL} Upload crash dump file'); ?><br>
	<input type="file" name="file"><br>
	<hr>
	<input type="radio" name="method" value="github"> <?php echo _('{EXPERIMENTAL} GitHub link'); ?> <br>
	<hr>
	<input type="submit">
	<input type="checkbox" name="api"> <?php echo _('output as JSON'); ?>
</form>
</body>
</html>

