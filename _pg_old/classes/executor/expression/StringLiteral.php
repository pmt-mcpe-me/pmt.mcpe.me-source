<?php

namespace pg\classes\executor\expression;

class StringLiteral implements StringExpression{
	public $string;
	public function toPhp(){
		return var_export($this->string, true);
	}
	public function toHtml(){
		// TODO: Implement toHtml() method.
	}
}
