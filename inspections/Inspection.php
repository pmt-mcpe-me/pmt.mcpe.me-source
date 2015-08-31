<?php

namespace inspections;

interface Inspection{
	public function __construct($dir);
	/**
	 * @return InspectionResult
	 */
	public function run();
}
