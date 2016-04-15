<?php

namespace vardump;

class VarDumpParser{
	private $dump;
	private $pointer = 0;
	public function __construct($dump){
		$this->dump = $dump;
	}
	/**
	 * @return Variable
	 * @throws \Exception
	 */
	public function readVar(){
		if(substr($this->dump, $this->pointer, 11) === "*RECURSION*"){
			$this->read(11);
			return new RecursionVariable($this);
		}
		if($this->peek(4) === "NULL"){
			$this->skip(4);
			$result = new NullVariable($this);
		}else{
			$type = strtolower(ltrim(trim($this->readUntil("(")), "&"));
			$this->skip(1);
			switch($type){
				case "string":
					$result = new StringVariable($this);
					break;
				case "bool":
					$result = new BoolVariable($this);
					break;
				case "int":
					$result = new IntVariable($this);
					break;
				case "float":
					$result = new FloatVariable($this);
					break;
				case "object":
					$result = new ObjectVariable($this);
					break;
				case "array":
					$result = new ArrayVariable($this);
					break;
				case "*RECURSION*":

				default:
					throw new \Exception("Unknown type '$type'");
			}
		}
		$this->ltrim();
		return $result;
	}
	public function readUntil($needle){
		$pos = strpos($this->dump, $needle, $this->pointer);
		$ret = substr($this->dump, $this->pointer, $pos - $this->pointer);
		$this->pointer = $pos;
		return $ret;
	}
	public function skip($steps){
		$this->pointer += $steps;
	}
	public function read($length){
		$result = substr($this->dump, $this->pointer, $length);
		$this->pointer += $length;
		return $result;
	}
	public function ltrim(){
		$remaining = substr($this->dump, $this->pointer);
		$trimmed = ltrim($remaining);
		$diff = strlen($remaining) - strlen($trimmed);
		$this->skip($diff);
		return $diff;
	}
	public function printRemaining(){
		echo "<pre>";
		echo htmlspecialchars(substr($this->dump, $this->pointer));
		echo "</pre>";
	}
	public function peek($length){
		return substr($this->dump, $this->pointer, $length);
	}
}
