<?php

namespace pg\classes\executor\expression\operands;

use pg\classes\executor\expression\Expression;

class DoubleOperandExpression implements Expression{
	public $name;
	/** @var Expression */
	protected $front, $back;
	/** @var string */
	protected $operator;
	public static function getInstance(Expression $front, $operator, Expression $back){
		if($operator === "->"){
			return new ObjectOperandExpression($front, $operator, $back);
		}
		if($operator === "."){
			return new StringOperandExpression($front, $operator, $back);
		}
		return new self($front, $operator, $back);
	}
	public function __construct(Expression $front, $operator, Expression $back){
		$this->front = $front;
		$this->operator = $operator;
		$this->back = $back;
	}
	public function toPhp(){
		return "({$this->front->toPhp()}) $this->operator ({$this->back->toPhp()})";
	}
	public function toHtml(){
		if(isset($this->name) and is_string($this->name) and strlen($this->name) > 0){
			return $this->name;
		}
		return $this->front->toHtml() . " <code>$this->operator</code> " . $this->back->toHtml();
	}
}
