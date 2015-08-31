<?php
session_start();
$_SESSION["pm_terms_agreed"] = true;
header("Location: ./");
