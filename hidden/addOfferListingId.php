<?php
include("includes/db.php");
include("includes/functions.php");



$LIM= $_GET['LIM'];
if($LIM<1)$LIM=0;

$key = trim($_GET['key']);
if($key<1)$key=0;

$c = trim($_GET['c']);
if($c<1)$c=1;
if($c>80){
	$key++;
	echo "$key++<br>";
	if($key>=count($arrAwsKeys))
  $key = 0;
	$c =0;
}


define('AWS_KEY', $arrAwsKeys[$key]['AWS_KEY']);
define('AWS_SECRET_KEY', $arrAwsKeys[$key]['AWS_SECRET_KEY']);
echo AWS_KEY."<br>";
if(addOfferListingId($LIM)){
	echo "<script>\n";
	echo "document.location.href=\"http://buch.breviarium.de/hidden/addOfferListingId.php?&c=$c&key=$key&LIM=".($LIM+25)."\"\n";
	echo "</script>\n";
}


?>