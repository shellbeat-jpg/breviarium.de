<?php
include("includes/db.php");
include("includes/functions.php");
$LIM= $_GET['LIM'];
if($LIM<1)
	$LIM=0;
$arrBinding=array('Spielkarten', 'MP3 CD', 'Microfilm', 'Tageskalender', 'Diskette', 'Poster', 'Schautafel', 'Kalender', 'Broschüre', 'Zubehör', 'Videokassette', 'Digital Download', 'Landkarte', 'Vorbespielter Audioplayer', 'Stoffbilderbuch', 'Loseblattsammlung', 'Hörkassette', 'Musiknoten',  'Geschenkartikel', 'Kindle Edition', 'CD-ROM', 'Audio CD');
$key=0;
define('AWS_KEY', $arrAwsKeys[$key]['AWS_KEY']);
define('AWS_SECRET_KEY', $arrAwsKeys[$key]['AWS_SECRET_KEY']);


$date_checked = date("Y-m-d");
$SQL_PRODUCTS = mysql_query("SELECT *
FROM `products`
WHERE ASIN != '' and products_status < 1 and date_checked != '$date_checked' LIMIT 0, 10") or die(mysql_error());

if(! mysql_num_rows($SQL_PRODUCTS))
	exit;

while ($PRODUCTS = mysql_fetch_array($SQL_PRODUCTS, MYSQL_ASSOC)) {
   $products_id = $PRODUCTS['products_id'];
   $ASIN = $PRODUCTS['ASIN'];
   if(setOffersFromASIN($ASIN, $products_id)){
			#echo "$products_id<br>";
	 }else{
			mysql_query("Update products set  date_checked = '$date_checked' where products_id = '$products_id'")  or die("<br>".mysql_error());
	  # echo $products_id ;
   # exit;
	 }

}
header("refresh:0,url=http://buch.breviarium.de/hidden/setProductsFromAsin.php?&LIM=".($LIM+10));
?>