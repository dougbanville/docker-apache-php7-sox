<?php
require("functions.php");

$url = "http://audiofile.rte.ie/webservice/v3/download.php?service=radio1&start=2018-12-10-07-00-00-00&end=2018-12-10-07-03-00-00&file_title=radio1_2018-12-10-00-00-00-00__2018-12-10-00-00-00-00&format=mp3";
$fileName = "test.mp3";
saveFileFromUrl($url,$fileName);

$start = escapeshellarg($_GET["start"]);

$json = saveFromAudioFile($_GET["start"],$_GET["end"],$_GET["service"],$_GET["format"],$_GET["fileName"],$audioIn,$audioOut,$duration);
echo $_GET['callback'] . '('.$json.')';
?>

<h1>Hello World</h1>
