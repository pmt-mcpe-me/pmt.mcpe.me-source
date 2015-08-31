<?php

namespace vardump;

class FloatVariable extends Variable{
	public function __construct(VarDumpParser $parser){
		$this->float = floatval($parser->readUntil(")"));
		$parser->skip(1);
		$parser->ltrim();
	}
	public function presentInHtml(){
		echo Variable::TYPE_FLOAT;
		echo ":&nbsp;";
		printf("%f", $this->float);
	}
}
