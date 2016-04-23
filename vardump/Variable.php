<?php

namespace vardump;

abstract class Variable{
	const TYPE_STRING = "<font color='#00D0D0'>String</font>";
	const TYPE_INT = "<font color='#D00000'>Integer</font>";
	const TYPE_BOOL = "<font color='#00D0D0'>Boolean</font>";
	const TYPE_FLOAT = "<font color='#FF0000'>Float</font>";
	const TYPE_ARRAY = "<font color='#00FFFF'>Array</font>";
	const TYPE_OBJECT = "<font color='#D0D000'>Object</font>";
	const TYPE_NULL = "<font color='#808080FFFF'>Null</font>";
	public abstract function __construct(VarDumpParser $parser);
	public abstract function presentInHtml();
}
