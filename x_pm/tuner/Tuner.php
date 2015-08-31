<?php

/** @noinspection PhpIllegalPsrClassPathInspection */
namespace pm\tuner;

interface Tuner{
	/**
	 * @param string $file
	 * @param string $originalContents
	 * @param PhpToken[] &$tokens
	 */
	public function tune($file, $originalContents, &$tokens);
}
