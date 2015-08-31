<?php

namespace pg\classes\executor\expression;

interface Expression{
	public function toPhp();
	public function toHtml();
}
