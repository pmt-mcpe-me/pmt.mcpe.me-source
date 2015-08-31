<?php

namespace pg\classes\executor\stmt;

use pg\classes\executor\expression\BooleanLiteral;

class ReturnStatement extends Statement{
	/** @var \pg\classes\executor\expression\Expression */
	public $result;
	protected function toPhp(){
		return "return {$this->result->toPhp()};";
	}
	protected function explainCode($mode, &$break){
		switch($mode){
			case self::MODE_COMMAND:
				if($this->result instanceof BooleanLiteral){
					if(!$this->result->getBoolean()){
						return "Send command usage to command sender";
					}
				}
		}
		return "<em>(do nothing)</em>";
	}
}