<?php

namespace vardump;

class IntVariable extends Variable{
	private $int;
	public function __construct(VarDumpParser $parser){
		$this->int = intval($parser->readUntil(")"));
		$parser->skip(1);
		$parser->ltrim();
	}
	public function presentInHtml(){
		echo Variable::TYPE_INT;
		echo ":";
		echo "<ul>";
		echo "<li>Decimal (base 10): <code>$this->int</code></li>";
		echo "<li>Binary (base 2): <code>";
		printf("%04b", $this->int);
		echo "<sub>2</sub></code></li>";
		echo "<li>Hexadecimal (base 16): <code>";
		printf("%02X", $this->int);
		echo "<sub>16</sub></code></li>";
		$timezone = utils_getURL("http://ip-api.com/php/" . $_SERVER["REMOTE_ADDR"]);
		if($timezone){
			$data = unserialize($timezone);
			if(!isset($data["timezone"])){
				goto bad;
			}
			$tz = $data["timezone"];
			$tzo = new \DateTimeZone($tz);
		}
		else{
			bad:
			$tz = "UTC";
			$tzo = new \DateTimeZone("UTC");
		}
		echo "<li>As unix timestamp at $tz: <ul>";
		$date = new \DateTime("now", $tzo);
		$date->setTimestamp($this->int);
		echo "<li>d-m-y: {$date->format("j-n-Y")}</li>";
		echo "<li>Time: {$date->format("H:i:s")}</li>";
		echo "</ul></li>";
		echo "</ul>";
	}
}