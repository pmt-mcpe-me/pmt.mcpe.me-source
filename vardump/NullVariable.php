<?php

/*
 * pmt.mcpe.me
 *
 * Copyright (C) 2015 PEMapModder
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PEMapModder
 */

namespace vardump;

class NullVariable extends Variable{
	public function __construct(VarDumpParser $parser){
	}
	public function presentInHtml(){
		echo Variable::TYPE_NULL;
	}
}
