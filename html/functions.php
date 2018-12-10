<?php
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
?>