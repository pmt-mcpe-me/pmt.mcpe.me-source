<?php
include("lang/setlang.php");
define('PACKAGE', 'varDump');

// gettext setting
bindtextdomain(PACKAGE, 'lang'); // or $your_path/lang, ex: /var/www/test/lang
textdomain(PACKAGE);
?>
<html><head><title><?php echo _('var_dump parser'); ?></title></head>
<body>
<h1><?php echo _('<code>var_dump</code> parser'); ?></h1>
<form action="varDumpResult.php" method="post"><input type="submit"><?php echo _('&nbsp;Please paste your <code>var_dump</code> output below and click the submit button.'); ?><br>
	<b><?php echo _('Warning:'); ?> </b><?php echo _("Make sure you don't convert line endings from"); ?> <code>\r\n</code> <?php echo _('to'); ?> <code>\n/\r</code> <?php echo _('or from'); ?> <code>\n/\r</code> <?php echo _('to'); ?> <code>\r\n</code>!<br>
	<textarea name="dump" rows="30" cols="150"></textarea>
</form></body></html>
