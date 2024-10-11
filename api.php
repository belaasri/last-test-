<?php

require_once("./converter.php");


$requestURL = $_POST["url"] ?? false;
$requestType = $_POST["type"] ?? false;
$api = $_POST["api"] ?? false;

header('Content-type: application/json');

if(($requestURL == false || $requestType == false || $api == false) && $api != "download")
{
    print_r(json_encode([
        "status"=>"fail",
        "msg"=>"Invalid api request"
    ]));
    return;
}





$converter = new Youtube2Mp3();

if($api == "download")
{

    $k = $_POST["k"] ?? false;
    $vid = $_POST["vid"] ?? false;
    $response = $converter->download($k,$vid);

    print_r(json_encode([
        "type"=>$requestType,
        "url"=>$requestURL,
        "response"=>$response
    ]));
}
else if($api = "token")
{
    $response = $converter->getToken($requestURL,$requestType);

    print_r(json_encode([
        "type"=>$requestType,
        "url"=>$requestURL,
        "response"=>$response
    ]));
}



?>