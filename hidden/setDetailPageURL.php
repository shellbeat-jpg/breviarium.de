<?php

include("includes/db.php");

include("includes/functions.php");

extract($_SERVER['QUERY_STRING']);

$LIM= $_GET['LIM'];

if($LIM<1)

	$LIM=0;

if($key<1)

	$key=0;



if($c>200){

	if($key==0){

    $key = 1;

	} elseif($key==1){

    $key = 2;

	}elseif($key==2){

    $key = 3;

	} elseif($key==3){

    $key = 4;

	} elseif($key==4){

    $key = 5;

	} elseif($key==5){

    $key = 6;

	}elseif($key==6){

    $key = 7;

	}elseif($key==7){

    $key = 8;

	} elseif($key==8){

    $key = 9;

	} elseif($key==9){

    $key = 10;

	} elseif($key==10){

    $key = 11;

	} elseif($key==11){

    $key = 0;

	}

	$c =0;

}

$c++;











$arrBinding=array('Spielkarten', 'MP3 CD', 'Microfilm', 'Tageskalender', 'Diskette', 'Poster', 'Schautafel', 'Kalender', 'Broschüre', 'Zubehör', 'Videokassette', 'Digital Download', 'Landkarte', 'Vorbespielter Audioplayer', 'Stoffbilderbuch', 'Loseblattsammlung', 'Hörkassette', 'Musiknoten',  'Geschenkartikel', 'Kindle Edition', 'CD-ROM', 'Audio CD');

define('AWS_KEY', $arrAwsKeys[$key]['AWS_KEY']);

define('AWS_SECRET_KEY', $arrAwsKeys[$key]['AWS_SECRET_KEY']);



 #setDetailPageURL('3593367823', 174716);

 #exit;



$date_checked = date("Y-m-d");

# and DetailPageURL = ''

$SQL_PRODUCTS = mysql_query("SELECT *

FROM `products` WHERE products_quantity > 0 and date_checked = '0000-00-00' order by products_id LIMIT 0, 10") or die(mysql_error());

$date_checked = date("Y-m-d");

if(! mysql_num_rows($SQL_PRODUCTS))

	exit;



while ($PRODUCTS = mysql_fetch_array($SQL_PRODUCTS, MYSQL_ASSOC)) {

   $products_id = $PRODUCTS['products_id'];

   $ASIN = $PRODUCTS['ASIN'];

	 setDetailPageURL($ASIN, $products_id);

	 mysql_query("Update products set date_checked = '$date_checked' where products_id = '$products_id'")  or die("<br>".mysql_error());



	 #   exit;

	 	#echo "Error: $ASIN <br>";

}

header("refresh:0,url=http://buch.breviarium.de/hidden/setDetailPageURL.php?&key=$key&c=$c&LIM=".($LIM+10));

?>