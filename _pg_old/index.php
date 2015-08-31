<?php
include_once "pgFx.php";
if(isInitialized()){
	include "main.php";
}
else{
	include "pg.php";
}
