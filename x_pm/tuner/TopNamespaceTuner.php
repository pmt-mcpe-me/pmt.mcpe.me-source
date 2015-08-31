<?php

/** @noinspection PhpIllegalPsrClassPathInspection */
namespace pm\tuner;

class TopNamespaceTuner implements Tuner{ // TNT!
	public static $OPTIMIZED_CONSTANTS = [
		"PHP_INT_MAX" => true,
		"PHP_INT_SIZE" => true,
		"PHP_EOL" => true,
		"TRUE" => true,
		"FALSE" => true,
		"NULL" => true,
		"ENDIANNESS" => true,
	];
	public function tune($file, $originalContents, &$tokens){
		foreach($tokens as $i => $token){
//		for($i = 1; $i < count($tokens) - 1; $i++){
//			$token = $tokens[$i];
			$j = $i - 1;
			/** @var PhpToken $before */
			do{
				if(!isset($tokens[$j])){
					$cont = true;
					break;
				}
				$before = $tokens[$j];
				$j--;
			}while($before->type === T_WHITESPACE or $before->type === T_COMMENT or $before->type === T_DOC_COMMENT);
			if(isset($cont)){
				continue;
			}
			$j = $i + 1;
			/** @var PhpToken $after */
			do{
				if(!isset($tokens[$j])){
					$cont = true;
					break;
				}
				$after = $tokens[$j];
				$j++;
			}while($after->type === T_WHITESPACE or $after->type === T_COMMENT or $after->type === T_DOC_COMMENT);
			if(isset($cont)){
				continue;
			}
			if($token->type === T_STRING and $before->text !== "\\"){
				if(isset(self::$OPTIMIZED_CONSTANTS[strtoupper($token->text)]) and $after->text !== "("){
					$token->text = "\\" . $token->text;
				}elseif(function_exists($token->text) and !($before->type === PhpToken::T_SYMBOL and ($before->text !== "::" or $before->text !== "->")) and $after->type === PhpToken::T_SYMBOL and $after->text === "("){
//					array_splice($tokens, $i++, 0, new PhpToken([T_NS_SEPARATOR, "\\"]));
					$token->text = "\\" . $token->text;
				}
			}
		}
	}
}
