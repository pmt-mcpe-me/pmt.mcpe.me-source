<?php

namespace vardump;

class StringVariable extends Variable{
	private $string;
	public function __construct(VarDumpParser $parser){
		$length = intval(trim($parser->readUntil(")")));
		$parser->readUntil("\"");
		$parser->skip(1);
		$this->string = $parser->read($length);
		$parser->skip(1);
		$parser->ltrim();
	}
	public function presentInHtml(){
		echo Variable::TYPE_STRING;
		echo ":&nbsp;";
		echo "<code>\"<span style='background-color: #E0E0E0'>";
		echo htmlspecialchars($this->string);
		echo "</span>\"</code>";
	}
}
