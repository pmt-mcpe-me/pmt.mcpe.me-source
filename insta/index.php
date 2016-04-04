<?php
@define("STDERR", fopen("php://stderr", "wt"));
session_start();
if(!isset($_SESSION["access_token"])){
	$_SESSION["state"] = $state = bin2hex(openssl_random_pseudo_bytes(8));
	header("Location: https://github.com/login/oauth/authorize?client_id=" . trim(file_get_contents("/var/www/cid.txt")) . "&scope=gist,user:email&state=$state");
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

const OPTIMIZE_ENABLED = true;

if(OPTIMIZE_ENABLED){
	ob_start();
}

?>
<html>
<head>
	<title>InstaPlugin</title>
	<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.min.css" rel="stylesheet"/>
	<script>
		function add_stylesheet_once(url){
			var head = $('head');
			if(head.find('link[rel="stylesheet"][href="' + url + '"]').length < 1){
				head.append('<link rel="stylesheet" href="' + url + '" type="text/css" />');
			}
		}
		function reloadMe(){
			location.reload(true);
		}
		var getGistEmbedCallback = function(revealer, hiddenDev){
			return function(data){
				add_stylesheet_once(data.stylesheet);
				hiddenDev.html("<br>" + data.div);
				hiddenDev.find(".gist-meta a").each(function(){
					var $this = $(this);
					var text = $this.text();
					while(text.indexOf("--") > -1){
						text = text.replace("--", "/");
					}
					$this.text(text);
				});
			}
		};
		$(document).ready(function(){
			$("#newgen-dialog").dialog({
				autoOpen: false
			});
			$(".gist-revealer").click(function(){
				var $this = $(this);
				var gistId = $this.attr("data-gist-id");
				var hidden = $(".hidden-gist-content[data-gist-id='" + gistId + "']");
				if(parseInt(hidden.attr("data-initialized")) == 0){
					hidden.attr("data-initialized", 1);
					$.getJSON("https://gist.github.com/" + gistId + ".json?callback=?", getGistEmbedCallback($this, hidden));
				}
				if(hidden.css("display") == "none"){
					hidden.css("display", "block");
					$this.text("Close Preview");
				}else{
					hidden.css("display", "none");
					$this.text("Preview");
				}
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
	For example, the file <code>src--name--space--Main.php</code> will be interpreted as
	<code>src/name/space/Main.php</code>.
</p>

<p>
	<button onclick='$("#newgen-dialog").dialog("open");'>Generate new gist plugin</button>
</p>

<p>
	Please select the gist to create plugin from.<br>
	<em>Note: Your gist must contain a <code>plugin.yml</code> file to be identified here.</em><br>
</p>
<ul class="gistlist" style="list-style-type: none;">
	<?php
	foreach($ret as $gist){
		$files = $gist["files"];
		if(isset($files["plugin.yml"])){
			$yamlCont = urlGet($files["plugin.yml"]["raw_url"]);
			$manifest = yaml_parse($yamlCont);
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
					<span class="lang-data" title="<?= round($cnt / $sum * 100, 2) ?>%"
					      style="font-size: <?= 50 + $cnt / $sum * 150 ?>%"><?= $lang ?></span>
				<?php endforeach; ?><br>
				<button onclick='window.open(<?= json_encode($gist["html_url"]) ?>);'>Open on GitHub Gist</button>
				<button class="gist-revealer" data-gist-id="<?= $gist["id"] ?>">Preview</button>
				<button onclick='window.location = "make.php?id=<?= $gist["id"] ?>";'>Make into plugin!</button>
				<div class="hidden-gist-content" data-gist-id="<?= $gist["id"] ?>" style="display: none;"
				     data-initialized="0">
					<span style="background-color: #B0B0B0">
						Loading...
					</span>
				</div>
			</li>
			<?php
		}
	}
	?>
</ul>
<div id="newgen-dialog" title="Generate new gist plugin">
	<form target="_blank" action="new.php" method="post"
	      onsubmit="setTimeout(window.location.reload, 5000)">
		Plugin Name: <input type="text" name="name" placeholder="Only put A-Z a-z 0-9 or underscore!"><br>
		Plugin Version: <input type="text" name="version" value="1.0"><br>
		PocketMine API Version: <input type="text" name="api" value="1.12.0"><br>
		<input type="checkbox" name="config"> Generate config code<br>
		Generate <input type="number" name="skeletons" value="0"> dummy class(es)<br>
		Generate <input type="number" name="tasks" value="0"> dummy task(s)<br>
		<input type="submit" value="Generate" onclick='$("#newgen-dialog").dialog("close");'>
	</form>
</div>
<footer>
	<button onclick='window.location = "logout.php";'>Logout from GitHub</button>
	<button
		onclick='window.location = "logout.php?redirect=<?= urlencode("https://github.com/settings/applications") ?>";'>
		Revoke GitHub authorization
	</button>
</footer>
</body>
</html>

<?php
if(OPTIMIZE_ENABLED){
	$data = ob_get_contents();
	ob_end_clean();
	echo preg_replace('/[ \t\r\n]+/', " ", $data);
}
?>
