<?php

class TuneArgs{
	public $topNamespaceBackslash;
	public $obfuscate;
	public function isFilled(){
		return $this->topNamespaceBackslash or $this->obfuscate;
	}
}
