<?php

namespace inspections;

class InspectionResult{
	private $name;
	/** @var string[] */
	public $warnings = [], $errors = [], $notices = [], $info = [];
	public function __construct($name){
		$this->name = $name;
	}
	public function warning($warning){
		$this->warnings[] = $warning;
	}
	public function error($error){
		$this->errors[] = $error;
	}
	public function notice($notice){
		$this->notices[] = $notice;
	}
	public function info($info){
		$this->info[] = $info;
	}
	public function htmlEcho(){
		echo "<li><b>$this->name</b>";
		echo "<ul>";
		echo "<li>Inspection result: <b>";
		if(count($this->errors) > 0){
			echo "<font color='FF0000'>Error</font>";
		}elseif(count($this->warnings) > 0){
			echo "<font color='FF6400'>Warning</font>";
		}elseif(count($this->notices) > 0){
			echo "<font color='0064FF'>Notice</font>";
		}else{
			echo "<font color='64FF00'>Passed</font>";
		}
		echo "</b></li>";
		if(count($this->errors)){
			echo "<li><b>Errors</b><ul>";
			foreach($this->errors as $error){
				echo "<li>$error</li>";
			}
			echo "</ul></li>";
		}
		if(count($this->warnings)){
			echo "<li><b>Warnings</b><ul>";
			foreach($this->warnings as $warning){
				echo "<li>$warning</li>";
			}
			echo "</ul></li>";
		}
		if(count($this->notices)){
			echo "<li><b>Notices</b><ul>";
			foreach($this->notices as $notice){
				echo "<li>$notice</li>";
			}
			echo "</ul></li>";
		}
		if(count($this->info)){
			echo "<li><b>Info</b><ul>";
			foreach($this->info as $info){
				echo "<li>$info</li>";
			}
			echo "</ul></li>";
		}
		echo "</ul>";
		echo "</li>";
	}
	public function jsonResult(){
		if(count($this->errors) > 0){
			$status = "Error";
		}elseif(count($this->warnings) > 0){
			$status = "Warning";
		}elseif(count($this->notices) > 0){
			$status = "Notice";
		}else{
			$status = "Passed";
		}
		return [
			"status" => $status,
			"errors" => $this->errors,
			"warnings" => $this->warnings,
			"notices" => $this->notices,
			"info" => $this->info
		];
	}
	/**
	 * @return mixed
	 */
	public function getName(){
		return $this->name;
	}
}
