<?php
require 'vendor/autoload.php';
require 'transcoder/config.php';
use Aws\ElasticTranscoder\ElasticTranscoderClient;
use Aws\S3\S3Client;

function saveFileFromUrl($url,$fileName)
{
    $ch = curl_init($url);
    //curl_setopt($ch, CURLOPT_URL, $url);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo $httpCode;
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);

    $myfile = fopen($fileName, "w") or die("Unable to open file!");
    fwrite($myfile, $output);
    fclose($myfile);
    
    curl_close($ch);
}

function s3Upload($file, $bucketName)
{
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $newFileName = date("ymdhis") . "." . $ext;
    $s3Client = new S3Client([
        'version' => 'latest',
        'region' => 'eu-west-1',
        'credentials' => [
            'key' => $GLOBALS["accessKey"],
            'secret' => $GLOBALS["secret"],
        ],
    ]);
    $result = $s3Client->putObject(array(
        'Bucket' => $bucketName,
        'Key' => $newFileName,
        'SourceFile' => $file,
        'ACL' => 'public-read',
    ));

    // We can poll the object until it is accessible
    $s3Client->waitUntil('ObjectExists', array(
        'Bucket' => $bucketName,
        'Key' => $newFileName,
    ));

    if (unlink($file)) {
        //echo "Deleted File";
    } else {
        //echo "Couldn't delete temp file";
    }

    return $newFileName;

}
?>