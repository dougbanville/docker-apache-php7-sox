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
    return "https://s3-eu-west-1.amazonaws.com/" . $bucketName . "/" . $newFileName;
}

function getClipperFileName($siteId, $radiomanid, $modifydate, $slug, $title, $fieldTitle)
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://clipper-next.rte.ie/DistributedServices/Publishing.svc/pox/RegisteriNews?siteId=$siteId&radiomanid=$radiomanid&modifydate=$modifydate&slug=$slug&title=$title&fieldTitle=$fieldTitle&description=",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 300,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Postman-Token: 12219352-aed0-4ab8-8cc4-6a8a86db4187",
            "cache-control: no-cache",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        die("clipper failed cURL Error #:" . $err);
    } else {
        $xml = simplexml_load_string($response) or die("Error: Cannot create object");
        $fileName = $xml[0];
        $fileName = str_replace("Successful ", "", $fileName);
        return "inews_".$modifydate.$fileName;
    }
    /*
    Where:
    modifydate = Math.round((new Date()).getTime() / 1000); 
    RadiomanID = Unique integer - try use modifydate above
    siteId = 1322 (test changes)
    slug / title / fieldTitle all the same - (mi-/nw1-/tw-)ClipNameWithNoSpaces

    Responce
    "Successful 10969970_21471137_10969971_JD-Test2"
    Save file with this name, using modified date above and the 3 ids from the response:

    inews_1543481759_10969970_21471137_10969971_1_twjdtest2_a_1543481759_rte54fminews.mp2
    */

    //10967858_21469746_10969147_DB-Test
};
?>