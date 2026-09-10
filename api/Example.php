<?php
set_time_limit(10000);

/* Example usage of the Amazon Product Advertising API */
include("amazon_api_class.php");
error_reporting(E_ALL);
$public_key     = "AKIAIA7YXELZZQTB2T2Q";
$private_key    = "rSJrJn09YxCQ+n3MZKRyXOw14KL+Mhtnrze1RNZe";
$region         = "de"; // or "CA" or "DE" etc.

$obj = new AmazonProductAPI($public_key, $private_key, $region);
$obj->setMedia("display");
print_r($obj->getBrowseNodes("541686"));


?>