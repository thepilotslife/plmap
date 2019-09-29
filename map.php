<?php

ini_set('memory_limit', '1500M');
ini_set('max_execution_time', 0);

$size = 8400;
$mapsize = 20500;

header("Content-Type: image/jpeg");
$im = imagecreatetruecolor($size, $size);
$roadcol = imagecolorallocate($im, 255, 0, 0);

$imgtocopy = imagecreatefromjpeg('map.jpg');
//imagecopy($im, $imgtocopy, 0, 0, 0, 0, $size, $size);
imagedestroy($imgtocopy);

imagesetthickness($im, 10);

include('roadz.php');

foreach ($roads as $road) {
	$x1 = ($road[0] + $mapsize / 2) / $mapsize * $size;
	$y1 = (-$road[1] + $mapsize / 2) / $mapsize * $size;
	$x2 = ($road[2] + $mapsize / 2) / $mapsize * $size;
	$y2 = (-$road[3] + $mapsize / 2) / $mapsize * $size;
	imageline($im, $x1, $y1, $x2, $y2, $roadcol);
}

imagesetthickness($im, 1);

imagejpeg($im);
imagedestroy($im);

