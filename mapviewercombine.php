<?php

ini_set('memory_limit', '2500M');
ini_set('max_execution_time', 0);

$SEGS = 14;
$SEGS = 55;
$SEGSIZE = 600;
$SEGSIZE = 256;

$SEGCOUNT = $SEGS * $SEGS;
$SIZE = $SEGS * $SEGSIZE;

if (!isset($_GET['d'])) header("Content-Type: image/png");
$im = imagecreatetruecolor($SIZE, $SIZE);
$background_color = imagecolorallocate($im, 0, 0, 0);

$s = 0;
for ($i = 0; $i < $SEGS; $i++) {
	for ($j = 0; $j < $SEGS; $j++) {
		$num = '' . $s;
		if (strlen($num) == 1) {
			$num = '0' . $num;
		}
		$imgtocopy = imagecreatefrompng("d:/Users/Robin/games/gtasa/!new/mapviewer-0.5a4/radar{$num}.png");
		imagecopy($im, $imgtocopy, $j * $SEGSIZE, $i * $SEGSIZE, 0, 0, $SEGSIZE, $SEGSIZE);
		imagedestroy($imgtocopy);
		$s++;
		if ($s >= $SEGCOUNT) {
			break 2;
		}
	}
}

//imagepng($im, null, 0, PNG_NO_FILTER);
imagepng($im);
imagedestroy($im);

