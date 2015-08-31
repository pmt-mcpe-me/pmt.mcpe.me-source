<?php

namespace vardump;

class ObjectVariable extends ArrayVariable{
	public function __construct(VarDumpParser $parser){
		$this->class = $parser->readUntil(")#");
		$parser->skip(2);
		$this->id = intval($parser->readUntil(" ("));
		$parser->skip(2);
		$count = intval($parser->readUntil(")"));
		$parser->readUntil("{");
		$parser->skip(1);
		$parser->ltrim();
		$this->readArray($count, $parser);
		$parser->readUntil("}");
		$parser->skip(1);
		$parser->ltrim();
	}
	public function presentInHtml(){
		echo Variable::TYPE_OBJECT;
		echo ":&nbsp;";
		echo "<font color='#A000A0'>Class: ";
		echo "<code><span style='background-color: #30FF30'>";
		echo htmlspecialchars($this->class);
		echo "</span></code>";
		echo "</font>";
		echo "(#<font color='#D0D000' size='5'><code>$this->id</code></font>)";
		echo "<ul>";
		$this->presentArrayInHtml();
		echo "</ul>";
	}
}
