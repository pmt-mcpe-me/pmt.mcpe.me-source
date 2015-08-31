<?php

namespace pg\classes\executor\stmt\block;

abstract class BlockStatement{
	protected function toPhp(){
		$output = $this->getName();
		$condition = $this->getCondition();
		if($condition !== null){
			$output .= "($condition)";
		}
		$output .= "{" . PHP_EOL;
		$output .= $this->getBody();
		$output .= "}" . PHP_EOL;
		return $output;
	}
	public abstract function getName();
	public abstract function getCondition();
	public abstract function getBody();
}
