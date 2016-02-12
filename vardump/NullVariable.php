<?php

namespace vardump;

class NullVariable extends Variable{
	public function __construct(VarDumpParser $parser){
	}
	public function presentInHtml(){
		echo Variable::TYPE_NULL;
	}
}
