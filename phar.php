<?php
include("lang/setlang.php");
define('PACKAGE', 'phar');

// gettext setting
bindtextdomain(PACKAGE, 'lang'); // or $your_path/lang, ex: /var/www/test/lang
textdomain(PACKAGE);
?>
<html>
<head>
	<title><?php echo _('Create phar'); ?></title>
	<script>
	function fillPmStub(){
		document.getElementById("stubInput").value = '<?= "<?php" ?> define("pocketmine\\\\PATH", "phar://". __FILE__ ."/"); require_once("phar://". __FILE__ ."/src/pocketmine/PocketMine.php");  __HALT_COMPILER();';
	}
	</script>
</head>
<body><font face="Helvetica">
	<h1><?php echo _('Phar maker'); ?></h1>
	<h3><?php echo _('How to use'); ?></h3>
	<ol>
		<li><?php echo _('Write your plugin, of course :D'); ?></li>
		<li><?php echo _('Put your files in the correct structure (according to namespaces, etc.)'); ?></li>
		<li><?php echo _('Create a ZIP file and throw your structure into it. You can put it anywhere inside the ZIP file, but only the folder with plugin.yml (and subfolders) will be included.'); ?></li>
		<li><?php echo _('Upload that file below :)'); ?></li>
	</ol>
	<form method="post" action="/phar-result.php" enctype="multipart/form-data">
		<p><input type="file" name="file"></p>
		<p><?php echo _('Stub (Leave as default unless you know what it is):'); ?>
			<?php
			echo '<input type="text" name="stub" value="';
			echo '<?php __HALT_COMPILER();';
			echo '" size="100" id="stubInput">';
			?>
			<button onclick="fillPmStub(); return false;"><?php echo _('Use the PocketMine-MP.phar stub'); ?></button>
		</p>
		<p><?php echo _('Tune the plugin using the following methods:'); ?> <br>
			<input type="checkbox" name="tune_top_namespace_optimization">
				<?php echo _('Optimize constant references by adding a <code>\</code> prefix to indicate that it is a top namespace reference.'); ?><br>
			<input type="checkbox" name="tune_obfuscation"> <?php echo _('Obfuscate code'); ?>
		</p>
		<p><font color="#8b0000"><?php echo _('Warning: Using any of the tunes may stripe out all the comments in your code <em>except</em> PHP doc comments or line comments.'); ?></font></p>
		<p>
			<?php echo _('Carry out the following inspections too:'); ?> <br>
			<input type="checkbox" name="inspection_classpath"> <?php echo _('Check classpath'); ?><br>
			<input type="checkbox" name="inspection_bad_practice"> <?php echo _('Scan for bad practice'); ?><br>
			<input type="checkbox" name="inspection_lint"> <?php echo _('Syntax error lint scan'); ?>
		</p>
		<p><input type="submit" value="<?php echo _('create phar'); ?>"></p>
	</form>
	<p><?php echo _('New: use the frames page <a href="pm.php" target="_parent">here</a> if you are not already using.'); ?></p>
<pre>
	<?php echo _("Disclaimer:
	This service is provided absolutely free of charge and is not guaranteed always available.
	This page (phar.php) extracts ZIP files uploaded by users into a location in the server's filesystem that is not available to users, executes tuning and packs the result into a *.phar file that is available for public download. This process is 100% automated.
	This website (http://pmt.mcpe.me) is in no ways affiliated with PocketMine-MP (http://pocketmine.net), an open-source project developed by the PocketMine Team, or Minecraft: PE, a game software developed by Mojang.
	We (owner of this website) are not to be held responsible for any acts related to copyright breaches and other illegal acts. All contents in the downloaded phar are either generated using the uploaded file or the software used for this website."); ?>
</pre>
</font></body>
</html>
