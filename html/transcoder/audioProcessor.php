<?php
require("../functions.php");
$audioId = $argv[1];
$inputFile = $argv[2];
$outputFile = $argv[3];
$audioIn = $argv[4];
$duration = $argv[5];
$gain = $argv[6];

exec("/usr/bin/sox $inputFile $outputFile trim $audioIn $duration gain $gain");

$newFileName = s3Upload($outputFile, $GLOBALS["bucketName"]);

$fp = fopen('log.txt', 'w');
fwrite($fp, "save $newFileName");
fclose($fp);


