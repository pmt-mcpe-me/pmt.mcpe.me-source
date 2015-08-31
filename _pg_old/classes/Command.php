<?php

namespace pg\classes;

use pg\classes\executor\expression\BooleanLiteral;
use pg\classes\executor\stmt\ReturnStatement;
use pg\classes\executor\stmt\Statement;

class Command{
	/** @var Plugin */
	public $plugin;
	/** @var string */
	public $name, $desc, $usage;
	/** @var executor\Executor */
	public $executor = null;
	public static function isValidName($name){
		return strpos($name, " ") === false and strpos($name, ":") === false;
	}
	public function __construct(Plugin $plugin, $name, $desc, $usage){
		$this->plugin = $plugin;
		$this->name = $name;
		$this->desc = $desc;
		$this->usage = $usage;
		$this->executor = new executor\Executor("onCommand_$name", ["\$args", "\$sender"]);
		$this->executor->returnStmt = new ReturnStatement();
		$this->executor->returnStmt->result = new BooleanLiteral(true);
	}
	public function toHtml(){
		echo "<ul>";
		foreach($this->executor->stmts as $stmt){
			echo "<li>";
			echo $stmt->explain(Statement::MODE_COMMAND, $break);
			echo "</li>";
			if($break){
				break;
			}
		}
		echo "<li>";
		echo $this->executor->returnStmt->explain(Statement::MODE_COMMAND, $b);
		echo "</li>";
		echo "</ul>";
	}
}
