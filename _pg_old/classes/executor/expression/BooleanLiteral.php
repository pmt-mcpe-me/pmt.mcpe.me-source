<?php

namespace pg\classes\executor\expression;

class BooleanLiteral implements BooleanExpression{
	private $boolean;
	public function __construct($boolean){
		if(!is_bool($boolean)){
			$boolean = boolval($boolean);
		}
		$this->boolean = $boolean;
	}
	public function toPhp(){
		return var_export($this->boolean, true);
	}
	public function toHtml(){
		return $this->boolean ? "true":"false";
	}
	/**
	 * @return boolean
	 */
	public function getBoolean(){
		return $this->boolean;
	}
}
