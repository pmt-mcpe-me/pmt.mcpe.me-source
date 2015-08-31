<?php

namespace pg\classes\executor\stmt;

use pg\classes\executor\expression\StringExpression;

class SendMessageStatement extends Statement{
	/** @var \pg\classes\executor\expression\ObjectExpression */
	private $receiver;
	/** @var StringExpression */
	private $msg;
	public function __construct($receiver, $message){
		$this->receiver = $receiver;
		$this->msg = $message;
	}
	protected function toPhp(){
		$msg = $this->msg->toPhp();
		if(!($this->msg instanceof StringExpression)){
			$msg = "(string) $msg";
		}
		return "{$this->receiver->toPhp()}->sendMessage($msg);";
	}
	protected function explainCode($mode, &$break){
		return "Send message to {$this->receiver->toHtml()}: {$this->msg->toHtml()}";
	}
}
