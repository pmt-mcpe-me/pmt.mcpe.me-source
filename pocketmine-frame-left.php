<?php
include("lang/setlang.php");
define('PACKAGE', 'pocketmine-frame-left');

// gettext setting
bindtextdomain(PACKAGE, 'lang'); // or $your_path/lang, ex: /var/www/test/lang
textdomain(PACKAGE);
?>
<html>
<head>
	<style type="text/css">
		ul{
			list-style-type: none;
			margin:          0;
			padding:         0 0 20px 0;
		}
		ul li{
			border:           1px solid aliceblue;
			margin:           5px 0 5px 0;
			padding:          0;
			display:          block;
			background-color: aliceblue;
		}
		.disabled{
			color:  #808080;
			cursor: no-drop;
		}
	</style>
</head>
<body>
<font face="Helvetica">
	<ul>
		<li><a href="/phar.php" target="content"><?php echo _('Zip to Phar converter'); ?></a></li>
		<li><a href="/unphar.php" target="content"><?php echo _('Phar to Zip converter'); ?></a></li>
		<li><a href="/insta/" target="_blank"><?php echo _('Instant GistPlugin Generator+Converter'); ?></a></li>
		<!--		<li><a href="/data/builds/" target="content">Development build archive of some plugins</a></li>-->
		<li><a href="/varDump.php" target="content"><?php echo _('<code>var_dump()</code> viewer (<code>xdebug</code>-style dumps are
				not supported yet)'); ?></a></li>
		<li><a href="/crashdump/" target="content"><?php echo _('PocketMine Crash Dump parsing'); ?></a></li>
		<li><a href="/pmb/" target="content"><?php echo _('PocketMine phar build archive'); ?></a></li>
		<li><a href="/api2/" target="content"><?php echo _('Plugin API 2.0.0 Injector'); ?></a></li>
		<li><a href="#" class="disabled"><?php echo _('<strong>[W.I.P.]</strong> Plugin Generator'); ?></a></li>
	</ul>
	<input type="button" value="<?php echo _('Reload content frame'); ?>" onclick="parent.content.location.reload()">
	<br>
</font>
</body>
</html>
