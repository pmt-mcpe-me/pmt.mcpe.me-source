<?php
include("lang/setlang.php");
define('PACKAGE', 'pocketmine-frame-up');

// gettext setting
bindtextdomain(PACKAGE, 'lang'); // or $your_path/lang, ex: /var/www/test/lang
textdomain(PACKAGE);
?>
<html><head><title><?php echo _('PocketMine Plugin Making Tools'); ?></title></head>
<body><font face="Helvetica">
	<font style="font-size: 24px; font-weight: bolder;">
		<a name="title"><?php echo _('PocketMine-MP Plugin Making Tools'); ?></a>
	</font>
	<br>
	<?php echo _('This project is open-source on
	<a href="https://github.com/PEMapModder/web-server-source" target="_blank">GitHub</a>.'); ?>
	<br>
	<?php echo _('Please feel free to report any bugs to
	<a href="https://github.com/PEMapModder/web-server-source/issues" target="_blank">the issue tracker</a>.
	When you create a report, please provide as much information as possible.'); ?>
	<br>
	<?php echo _('This website is authored by <a href="https://github.com/PEMapModder">PEMapModder</a> (with help from many people) and is hosted by <a href="https://www.techplayer.org" target="_blank">TechPlayer, LLC</a>.'); ?>
</font>
<a name="bottom"></a>
</body></html>
