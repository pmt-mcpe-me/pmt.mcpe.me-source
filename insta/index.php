<?php

session_start();
if(!isset($_SESSION["access_token"])){
	$_SESSION["state"] = $state = bin2hex(openssl_random_pseudo_bytes(8));
	header("Location: https://github.com/login/oauth/authorize?client_id=" . file_get_contents("/var/www/cid.txt") . "&scope=gist,user:email&state=$state");
	die;
}

$ch = curl_init("https://api.github.com/gists");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0 pmt.mcpe.me-insta/1.0", "Accept: application/json", "Authorization: bearer " . $_SESSION["access_token"]]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$ret = json_decode(curl_exec($ch), true);
curl_close($ch);

function urlGet($url){
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0 pmt.mcpe.me-insta/1.0", "Authorization: bearer " . $_SESSION["access_token"]]);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
?>
<html>
<head>
	<title>InstaPlugin</title>
	<script src="//code.jquery.com/jquery-1.10.2.min.js"></script><script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script><link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.min.css" rel="stylesheet" />
	<script>
		$(document).ready(function(){
			$("#newgen-dialog").dialog({
				autoOpen: false
			});
		});
	</script>
	<style type="text/css" rel="stylesheet">
		<?= file_get_contents("style.css") ?>
	</style>
	<script>
		<?= file_get_contents("style.js") ?>
	</script>
</head>
<body>
<h1 class="title">InstaPlugin</h1>
<header>Gists your gist as a plugin.</header>
<p style="padding: 10px;">Welcome back, <?= $_SESSION["github_name"] ?>!</p>
<p>
	This website replaces <code>--</code> in your gist's filenames into directory separators (<code>/</code>).<br>
	For example, the file <code>src--name--space--Main.php</code> will be interpreted as <code>src/name/space/Main.php</code>.
</p>
<p><button onclick='$("#newgen-dialog").dialog("open");'>Generate new gist plugin</button></p>

<p>
	Please select the gist to create plugin from.<br>
	<em>Note: Your gist must contain a <code>plugin.yml</code> file to be identified here.</em><br>
</p>
<ul class=gistlist>
	<?php
	foreach($ret as $gist){
		$files = $gist["files"];
		if(isset($files["plugin.yml"])){
			$manifest = yaml_parse_url($files["plugin.yml"]["raw_url"]);
			$langs = [];
			foreach($files as $file){
				if(!isset($langs[$file["language"]])){
					$langs[$file["language"]] = 1;
				}else{
					$langs[$file["language"]]++;
				}
			}
			$sum = array_sum($langs);
			?>
			<li class="gistlistitem">
				<strong><?= $manifest["name"] ?></strong>
				<br>
				Languages:
				<?php foreach($langs as $lang => $cnt): ?>
					<span class="lang-data" title="<?= $cnt / $sum * 100 ?>%" style="font-size: <?= 50 + $cnt / $sum * 150 ?>%"><?= $lang ?></span>
				<?php endforeach; ?><br>
				<button class="make" onclick='window.location = "make.php?id=<?= $gist["id"] ?>";'>Make into plugin!</button>
			</li>
			<?php
		}
	}
	?>
</ul>
<div id="newgen-dialog" title="Generate new gist plugin">
	<form target="_blank" action="new.php" method="post">
		Plugin Name: <input type="text" name="name" placeholder="Only put A-Z a-z 0-9 or underscore!"><br>
		Plugin Version: <input type="text" name="version" value="1.0"><br>
		PocketMine API version: <input type="text" name="api" value="1.12.0"><br>
		<input type="submit" value="Generate" onclick='$("#newgen-dialog").dialog("close");'>
	</form>
</div>
<footer>
	<button onclick='window.location = "logout.php";'>Logout from GitHub</button>
	<button onclick='window.location = "logout.php?redirect=<?= urlencode("https://github.com/settings/applications") ?>";'>Revoke GitHub authorization</button>
</footer>
</body>
</html>
