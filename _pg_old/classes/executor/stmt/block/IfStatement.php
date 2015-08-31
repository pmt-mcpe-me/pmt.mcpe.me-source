<?php

namespace pg\classes\executor\stmt\block;

use pg\classes\executor\expression\BooleanExpression;

class IfStatement extends BlockStatement{
	/** @var BooleanExpression */
	private $condition;
	/** @var \pg\classes\executor\stmt\Statement[] */
	private $body;
	public function __construct(BooleanExpression $condition, $body){
		$this->condition = $condition;
		$this->body = $body;
	}
	public function getName(){
		return "if";
	}
	public function getCondition(){
		return $this->condition->toPhp();
	}
	public function getBody(){
		$code = "";
		foreach($this->body as $stmt){
			$stmt->printTo($code);
		}
		return $code;
	}
}
