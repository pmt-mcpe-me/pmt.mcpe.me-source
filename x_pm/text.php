<?php
if(!isset($_GET["text"])){
	die;
}
$text = $_GET["text"];
$img = imagecreate($w = isset($_GET["width"]) ? min((int) $_GET["width"], 1000) : 1000, $h = isset($_GET["height"]) ? min((int) $_GET["height"], 1000) : 1000);
imagefilledrectangle($img, 0, 0, $w, $h, imagecolorallocate($img, 255, 255, 255));
imagettftext($img, $f = isset($_GET["size"]) ? (float) $_GET["size"] : 12.0, 0.0, 5, 5 + $f, imagecolorallocate($img, 0, 0, 0), "font.ttf", $text);
header("Content-Type: image/png");
imagepng($img);
imagedestroy($img);
