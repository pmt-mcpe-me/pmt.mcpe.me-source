<?php

if(!isset($_POST["method"])){
	header("Location: ./");
	die;
}
$method = $_POST["method"];
if($method === "buffer"){
	if(!isset($_POST["buffer"])){
		header("Location: ./");
		die;
	}
	$buffer = $_POST["buffer"];
}elseif($method === "file"){
	if(!isset($_FILES["file"])){
		header("Location: ./");
		die;
	}
	$path = $_FILES["file"]["tmp_name"];
	if(is_uploaded_file($path)){
		$buffer = file_get_contents($path);
	}else{
		header("Location: ./");
		die;
	}
}elseif($method === "github"){
	$buffer = "";
}else{
	header("Location: ./");
	die;
}
$buffer = $_POST["buffer"];
if(($start = strpos($buffer, "----------------------REPORT THE DATA BELOW THIS LINE-----------------------
===BEGIN CRASH DUMP===")) !== false){
	if(($end = strpos($buffer, "===END CRASH DUMP===")) !== false){
		$buffer = trim(substr($buffer, $start, $end - $start));
	}
}

$base64 = str_replace(str_split("\r\n\t "), "", $buffer);
$zlib = base64_decode($base64);
$json = zlib_decode($zlib);
$data = json_decode($json, true);
if(!is_array($data)){
	http_response_code(400);
	header("Content-Type: text/plain");
	echo "Data error!";
	die;
}

function formatUrl($url){
	if(preg_match('%^((http(s)?):)?//([^/]+)(/.*)?$%', $url, $matches)){
		return "<a target='_blank' href='$matches[0]'>$matches[4]</a>";
	}
	return $url;
}

function formatArray(array $array){
	if(count($array) === 0){
		return "<em>None</em>";
	}
	return htmlspecialchars(implode(", ", $array));
}

if($_SERVER["HTTP_ACCEPT"] === "application/json" or isset($_REQUEST["api"])){
	header("Content-Type: application/json");
	echo json_encode($data, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING | JSON_UNESCAPED_SLASHES);
	die;
}
const OPTIMIZER_ON = false; // need to disable it in <pre></pre>
ob_start();
?>
<!--suppress CssUnusedSymbol -->
<html>
<head>
	<title>Crash dump viewer</title>

	<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>

	<script>
		$(document).ready(function(){
			var i = 0;
			var spoiling = function(){
				var $this = $(this);
				var autoName = "auto-compiled-spoiler-" + (++i);
				var definedName = $this.attr("data-spoiler-name");
				var html = "<div><button class='spoiler-opener auto-compiled' " +
					"data-spoiler='" + autoName + "' data-spoiler-name='" + definedName + "'>";
				html += "Show " + definedName;
				html += "</button>";
				html += "<div class='spoiler' data-spoiler='" + autoName + "' data-spoiler-name='" + definedName + "'>";
				html += $this.html();
				html += "</div></div>";
				$this.replaceWith(html);
			};
			while($(".spoiling").length > 0){
				$(".spoiling").each(spoiling);
			}
			$(".spoiler-opener").click(function(){
				var $this = $(this);
				var spoiler = $(".spoiler[data-spoiler=\"" + $this.attr("data-spoiler") + "\"]");
				var toShow = spoiler.css("display") === "none";
				spoiler.css("display", toShow ? "block" : "none");
				if($this.hasClass("auto-compiled")){
					$this.text((toShow ? "Hide" : "Show") + " " + $this.attr("data-spoiler-name"));
				}
			});
			$(".spoiler").css("display", "none");
			$("pre.code").each(function(){
				var $this = $(this);
				var showLineNumber = typeof $this.attr("data-hide-line-number") === typeof undefined;
				var firstLine = 1;
				var firstLineString = $this.attr("data-first-line");
				if(typeof firstLineString !== typeof undefined){
					firstLine = parseInt(firstLineString);
				}
				var lines = $this.html().split("\n");
				var html = "<table class='code'>";
				var hightlightLines = $this.attr("data-highlight-line");
				if(typeof hightlightLines === typeof undefined){
					hightlightLines = (String)(firstLine - 1);
				}
				hightlightLines = hightlightLines.split(",");
				for(var line = 0; line < lines.length; line++){
					var isHighlightLine = false;
					for(var num = 0; num < hightlightLines.length; num++){
						if(line + firstLine == hightlightLines[num]){
							isHighlightLine = true;
						}
					}
					html += "<tr";
					if(isHighlightLine){
						html += " class='highlighted'";
					}
					html += ">";
					html += "<td align='right'>";
					if(showLineNumber){
						html += line + firstLine;
					}
					html += "</td>";
					html += "<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
					html += "<td align='left'>";
					html += lines[line];
					html += "</td>";
					html += "</tr>";
				}
				html += "</table>";
				$(this).replaceWith(html);
			});
			$("div.datum").each(function(){
				var $this = $(this);
				$this.before("<hr>");
				$this.before("<h3 class='subheading'>" + $this.attr("data-name") + "</h3>");
			});
			$("table.info").attr("border", "1");
		});
	</script>
	<style>
		body{
			margin:      30px 50px;
			font-family: "Helvetica Neue", Helvetica, Verdana, Arial, sans-serif;
		}
		h1{
			text-align:  center;
			font-weight: bold;
		}
		.code{
			background-color: #303030;
			color:            #FFFFFF;
			font-family:      monospace;
			border-radius:    5px;
		}
		pre.code{
			padding: 10px 0;
		}
		table.code{
			padding: 10px 5px;
		}
		table.code tr.highlighted{
			background-color: #798B47;
		}
		table.info th{
			text-align: right;
			padding:    5px;
		}
		table.info td{
			text-align: left;
			padding:    5px;
		}
	</style>
</head>
<body>
<h1>Crash dump</h1>

<div class="datum" data-name="Date of Crash">
	<script>
		var date = new Date(<?= json_encode($data["time"]) * 1000 ?>);
		document.write(date.toDateString());
	</script>
</div>

<div class="datum" data-name="Error">
	<pre class="code" data-hide-line-number>
<?= htmlspecialchars($data["error"]["type"]) ?> - <?= htmlspecialchars($data["error"]["message"]) ?>
	</pre>
	<h5>Code: <?= htmlspecialchars(substr($data["error"]["file"], 1) . ".php") ?></h5>
	<?php
	foreach($data["code"] as $key => $v){
		if($v === null){
			unset($data["code"][$key]);
		}
	}
	$errorLine = $data["error"]["line"];
	?>
	<div class="spoiling" data-spoiler-name="error code">
		<pre class="code" data-first-line="<?= array_keys($data["code"])[0] ?>" data-highlight-line="<?= $errorLine ?>">
			<?= str_replace(["\t", " "], ["&nbsp;&nbsp;&nbsp;&nbsp;", "&nbsp;"], implode("\n", $data["code"])) ?>
		</pre>
	</div>
</div>

<?php if(isset($data["plugin"]) and isset($data["plugins"][$data["plugin"]])): ?>
	<div class="datum" data-name="Crashing plugin">
		<?php
		$plugin = $data["plugins"][$data["plugin"]];
		if($plugin["website"] !== null){
			echo "<a target='_blank' href='" . $plugin["website"] . "'>";
		}
		?>
		<?= htmlspecialchars($data["plugin"]) ?>
		<?php
		if($plugin["website"] !== null){
			echo "</a>";
		}
		?>
	</div>
<?php endif; ?>

<div class="datum" data-name="Backtrace">
	<?php
	$trace = $data["trace"];
	foreach($trace as &$line){
		$line = substr(strstr($line, " "), 1);
	}
	?>
	<div class="spoiling" data-spoiler-name="backtrace">
		<pre class="code" data-first-line="0">
			<?= implode("\n", $trace) ?>
		</pre>
	</div>
</div>

<div class="datum" data-name="PocketMine Information">
	<table class="info">
		<?php
		$generalInfo = $data["general"];
		?>
		<tbody>
		<tr>
			<th>Version</th>
			<td><?= $generalInfo["version"] ?></td>
		</tr>
		<tr>
			<th>API</th>
			<td><?= $generalInfo["version"] ?></td>
		</tr>
		<?php if(trim($generalInfo["git"], "0") !== ""): ?>
			<tr>
				<th>Git commit SHA</th>
				<td><?= $generalInfo["git"] ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<th>RakLib version</th>
			<td><?= $generalInfo["raklib"] ?></td>
		</tr>
		</tbody>
	</table>
</div>

<div class="datum" data-name="Plugins">
	<div class="spoiling" data-spoiler-name="<?= count($data["plugins"]) ?> plugins">
		<ul>
			<?php foreach($data["plugins"] as $plugin): ?>
				<?php
				if(!$plugin["enabled"]){
					continue;
				}
				?>
				<li>
					<?= htmlspecialchars($plugin["name"]) ?>
					<div class="spoiling" data-spoiler-name="<?= $plugin["name"] ?> information">
						<table class="info">
							<tbody>
							<tr>
								<th>Version</th>
								<td><?= htmlspecialchars($plugin["version"]) ?></td>
							</tr>
							<tr>
								<th>Author(s)</th>
								<td><?= formatArray($plugin["authors"]) ?></td>
							</tr>
							<tr>
								<th>Supported API(s)</th>
								<td><?= formatArray($plugin["api"]) ?></td>
							</tr>
							<?php if(count($plugin["depends"]) > 0): ?>
								<tr>
									<th>Dependencies</th>
									<td><?= formatArray($plugin["depends"]) ?></td>
								</tr>
							<?php endif; ?>
							<?php if(count($plugin["softDepends"]) > 0): ?>
								<tr>
									<th>Soft Dependencies</th>
									<td><?= formatArray($plugin["softDepends"]) ?></td>
								</tr>
							<?php endif; ?>
							<?php if($plugin["website"] !== null): $website = $plugin["website"]; ?>
								<tr>
									<th>Website</th>
									<td><?= formatUrl($website) ?></td>
								</tr>
							<?php endif; ?>
							<tr>
								<th>Main class</th>
								<td><?= htmlspecialchars($plugin["main"]) ?></td>
							</tr>
							</tbody>
						</table>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

<div class="datum" data-name="PocketMine configuration files">
	<div class="spoiling" data-spoiler-name="server.properties">
		<pre class="code">
			<?= htmlspecialchars($data["server.properties"]) ?>
		</pre>
	</div>
	<div class="spoiling" data-spoiler-name="pocketmine.yml">
		<pre class="code">
			<?= htmlspecialchars($data["pocketmine.yml"]) ?>
		</pre>
	</div>
</div>

<div class="datum" data-name="PHP information">
	<div class="spoiling" data-spoiler-name="PHP extensions">
		<table class="info">
			<tbody>
			<?php foreach($data["extensions"] as $extension => $version): ?>
				<?php
				if($version === false){
					continue;
				}
				?>
				<tr>
					<th><?= htmlspecialchars($extension) ?></th>
					<td><?= htmlspecialchars($version) ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

</body>
</html>
<?php
$html = ob_get_clean();
if(OPTIMIZER_ON){
	$html = preg_replace('/[ \r\n\t]+/', " ", $html);
}
echo $html;
?>
