<?php
require '../vendor/autoload.php';
require("../functions.php");
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

$audioId = $argv[1];
$inputFile = $argv[2];
$outputFile = $argv[3];
$audioIn = $argv[4];
$duration = $argv[5];
$gain = $argv[6];
exec("/usr/bin/sox $inputFile $outputFile trim $audioIn $duration gain $gain");

$newFileName = s3Upload($outputFile, $GLOBALS["bucketName"]);

$serviceAccount = ServiceAccount::fromJsonFile('../.private/radio-a8e0f-firebase-adminsdk-458ba-20b024f066.json');

$firebase = (new Factory)
    ->withServiceAccount($serviceAccount)
    // The following line is optional if the project id in your credentials file
    // is identical to the subdomain of your Firebase project. If you need it,
    // make sure to replace the URL with the URL of your project.
    ->withDatabaseUri('https://radio-a8e0f.firebaseio.com')
    ->create();

$db = $firebase->getDatabase();

$db->getReference('audioclips/'.$audioId.'/publishStatus')->set("complete");
$db->getReference('audioclips/'.$audioId.'/awsaudio')->set($newFileName);


$fp = fopen('log.txt', 'w');
fwrite($fp, "save $newFileName");
fclose($fp);


