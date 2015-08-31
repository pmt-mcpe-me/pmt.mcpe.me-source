<?php

namespace vardump;

class ArrayVariable extends Variable{
	/** @var Variable[] */
	protected $array = [];
	protected $hasStringKey = false;
	protected $threeDots = false;
	public function __construct(VarDumpParser $parser){
		$count = intval($parser->readUntil(")"));
		$parser->readUntil("{");
		$parser->skip(1);
		$parser->ltrim();
		if($parser->peek(3) === "..."){
			$this->threeDots = true;
			return;
		}
		$this->readArray($count, $parser);
		$parser->readUntil("}");
		$parser->skip(1); // }
		$parser->ltrim();
	}
	protected function readArray($count, VarDumpParser $parser){
		for($i = 0; $i < $count; $i++){
			$parser->skip(1); // [
			$key = $parser->readUntil("]=>");
			if(substr($key, 0, 1) === "\"" and substr($key, -1) === "\""){
				$key = substr($key, 1, -1);
				$this->hasStringKey = true;
			}
			$parser->skip(4);
			$parser->ltrim();
			$value = $parser->readVar();
			$this->array[$key] = $value;
			$parser->ltrim();
		}
	}
	public function presentInHtml(){
		echo Variable::TYPE_ARRAY;
		echo "<ul>";
		$this->presentArrayInHtml();
		echo "</ul>";
	}
	protected function presentArrayInHtml(){
		if($this->threeDots){
			echo "<li>...</li>";
		}
		foreach($this->array as $key => $value){
			$key = htmlspecialchars($key);
			echo "<li>";
			echo "<code>";
			if($this->hasStringKey){
				echo "\"";
			}
			echo "<span style='background-color: #F0F0F0'>$key</span>";
			if($this->hasStringKey){
				echo "\"";
			}
			echo "</code>";
			echo ":&nbsp;";
			$value->presentInHtml();
			echo "</li>";
		}
	}
}
