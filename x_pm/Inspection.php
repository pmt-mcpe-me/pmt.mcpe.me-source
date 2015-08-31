<?php

/** @noinspection PhpIllegalPsrClassPathInspection */
namespace pm;

interface Inspection{
	public function inspect($path, &$warnings);
}
