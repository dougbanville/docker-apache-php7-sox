<?php
require("config.php");
require("../functions.php");

$audioId = $_GET["audioId"];

$start =$_GET["start"];

$end = $_GET["end"];

$service = $_GET["service"];

$format = $_GET["format"];

$fileName = $_GET["fileName"];

$audioOut = $_GET["audioOut"];

$duration = $_GET["duration"];

$audioIn = $_GET["audioIn"];

$filename = "temp";

$audioIn = number_format($audioIn,3,'.','');

$audioIn = str_pad($audioIn, 9, '0', STR_PAD_LEFT);

$url = $GLOBALS['audioFileUrl'] . "download.php?service=" . $service . "&start=" . $start . "&end=" . $end . "&file_title=" . $filename . "&format=" . $format;

$fileName = "audio/".time() . ".mp3";
$outputFile = "audio/".time()."-p.mp3";
$gainFile = "audio/gain".time().".mp3";

//save the audio from AF
saveFileFromUrl($url,$fileName);

//create the reponse
$json = array('audioId'=>$audioId,'fileName' => $fileName, 'outputFile' =>$outputFile, 'audioIn' => $audioIn, 'duration' => $duration);
$json = json_encode($json);
echo $_GET['callback'] . '('.$json.')';// Actual response that will be sent to the user
//run it as exec the client can go about their business
exec("nohup php audioProcessor.php $audioId $fileName $outputFile $audioIn $duration 8 >/dev/null 2>&1 &");
//exec("nohup /usr/bin/sox $fileName $outputFile trim $audioIn $duration gain 8 >/dev/null 2>&1 &");

//exec("nohup /usr/bin/sox $outputFile $gainFile gain 10 >/dev/null 2>&1 &");
?>