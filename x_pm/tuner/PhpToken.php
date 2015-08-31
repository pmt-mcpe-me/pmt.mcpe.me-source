<?php

/** @noinspection PhpIllegalPsrClassPathInspection */
namespace pm\tuner;

class PhpToken{
	const T_SYMBOL = 10001;
	/** @var int */
	public $type;
	/** @var string */
	public $text;
	/** @var int */
	public $line;
	public function __construct($token){
		if(is_string($token)){
			$this->type = self::T_SYMBOL;
			$this->text = $token;
		}else{
			list($this->type, $this->text, $this->line) = $token;
		}
	}
	public function __toString(){
		return $this->text;
	}
}
