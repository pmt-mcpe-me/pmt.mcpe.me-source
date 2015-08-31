<?php

namespace vardump;

class RecursionVariable extends Variable{
	public function __construct(VarDumpParser $parser){
		$parser->ltrim();
	}
	public function presentInHtml(){
		echo Variable::TYPE_OBJECT;
		echo ":&nbsp;<font color='#FF0000'>Recursion</font>";
	}
}
