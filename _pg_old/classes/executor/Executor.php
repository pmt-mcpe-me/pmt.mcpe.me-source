<?php

namespace pg\classes\executor;

use pg\classes\executor\stmt\ReturnStatement;

class Executor{
	/** @var stmt\Statement[] */
	public $stmts = [];
	/** @var null|stmt\ReturnStatement */
	public $returnStmt = null;
	private $name;
	private $argsString;
	public function __construct($name, $args = []){
		$this->name = $name;
		$this->argsString = implode(", ", $args);
	}
	public function toPhp(){
		$output = "public function $this->name($this->argsString)";
		$output .= "{" . PHP_EOL;
		foreach($this->stmts as $stmt){
			$stmt->printTo($output);
		}
		if($this->returnStmt instanceof ReturnStatement){
			$this->returnStmt->printTo($output);
		}
		$output .= "}" . PHP_EOL;
		return $output;
	}
}
