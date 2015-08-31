<?php

namespace pg\classes\executor\stmt;

abstract class Statement{
	const MODE_COMMAND = 1;
	public $explanation = "";
	public function printTo(&$code){
		$code .= $this->toPhp();
		$code .= PHP_EOL;
	}
	protected abstract function toPhp();
	protected function argEx(){
		throw new \InvalidArgumentException;
	}
	public function explain($mode, &$break){
		if(strlen($this->explanation) > 0){
			return $this->explanation;
		}
		return $this->explainCode($mode, $break);
	}
	protected abstract function explainCode($mode, &$break);
}
