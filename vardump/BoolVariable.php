<?php

namespace vardump;

class BoolVariable extends Variable{
	private $bool;
	public function __construct(VarDumpParser $parser){
		$string = $parser->readUntil(")");
		if($string === "true"){
			$this->bool = true;
		}
		elseif($string === "false"){
			$this->bool = false;
		}
		else{
			throw new \Exception("Unknown boolean representation: $string");
		}
		$parser->skip(1);
	}
	public function presentInHtml(){
		echo Variable::TYPE_BOOL;
		echo ":&nbsp;";
		if($this->bool){
			echo "<span style='background-color: #008000'><code>true</code></span>";
		}
		else{
			echo "<span style='background-color: #FFA0A0'><code>false</code></span>";
		}
	}
}
