<?php

/**
 * pmt.mcpe.me
 * Copyright (C) 2015 PEMapModder
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace inspections;

class SyntaxErrorInspection{
	private $dir;
	public function __construct($dir){
		$this->dir = rtrim($dir, "\\/") . DIRECTORY_SEPARATOR;
	}
	/**
	 * @return InspectionResult
	 */
	public function run(){
		$result = new InspectionResult("Syntax errors");
		$good = 0;
		$bad = 0;
		foreach(new \RegexIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->dir)), "#\\.php\$#") as $file){
			$ef = escapeshellarg($file);
			exec("php -l $ef 2>&1", $out);
			$lint = implode("<br>", $out);
			if(strpos($lint, "No syntax errors detected in") === 0){
				$good++;
			}else{
				$bad++;
				$result->error($lint);
			}
		}
		$result->info("$good good PHP file(s) and $bad bad PHP file(s) found.");
		$result->info("Checked with <code>PHP " . `php -r 'echo PHP_VERSION;'` . "</code>");
		return $result;
	}
}
