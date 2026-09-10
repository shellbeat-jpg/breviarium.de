<?php



include("includes/db.php");

include("includes/functions.php");



if(isset($_GET['pID']) && isset($_GET['eMail'])){

 mysql_query("delete from vormerkungen where email = '".$_GET['eMail']."' and products_id = '".$_GET['pID']."'");

 echo "Ihre Vormerkung wurde gelöscht.";

 exit;

}






$arrBinding=array('Spielkarten', 'MP3 CD', 'Microfilm', 'Tageskalender', 'Diskette', 'Poster', 'Schautafel', 'Kalender', 'Broschüre', 'Zubehör', 'Videokassette', 'Digital Download', 'Landkarte', 'Vorbespielter Audioplayer', 'Stoffbilderbuch', 'Loseblattsammlung', 'Hörkassette', 'Musiknoten',  'Geschenkartikel', 'Kindle Edition', 'CD-ROM', 'Audio CD');



if(isset($_GET['page']))

	$page = trim($_GET['page']);



if(!isset($page) || $page<1)

	$page=1;





$key = trim($_GET['k']);

if($key<1)$key=0;



$c = trim($_GET['c']);

if($c<1)$c=0;



if($c>1000){

	if($key==0){

    $key = 1;

	}elseif($key==1){

    $key = 2;

	} else{

    $key = 0;

	}

}



define('AWS_KEY', $arrAwsKeys[$key]['AWS_KEY']);

define('AWS_SECRET_KEY', $arrAwsKeys[$key]['AWS_SECRET_KEY']);

 # error_reporting(E_ALL);

 

#getDetailsFromASIN('3517068195', 0, 'ASIN', '');

#exit;





$SQL="SELECT  *  FROM  vormerkungen where date_checked < '".date("Y-m-d")."'  LIMIT $c, 10";

$sqlAll = mysql_query($SQL) or die(mysql_error());



if(mysql_num_rows($sqlAll)<1){

 mysql_query("update vormerkungen set date_checked = now()");

 $sqlVormerkungen = mysql_query("SELECT DISTINCT v.ISBN, v.products_id, v.status, v.min_price, v.email, v.datum, min( p.oAmount ) AS amount

												FROM vormerkungen v

												LEFT JOIN amazonOffers p ON v.products_id = p.id_amazon

												WHERE (v.min_price > p.oAmount OR v.min_price =0) AND p.oAmount >0

												GROUP BY v.products_id, v.email");



 while($vormerkung = mysql_fetch_array($sqlVormerkungen, MYSQL_ASSOC)){

	  $min_price  =  $vormerkung['min_price'];

	  $new_price  =  $vormerkung['amount'];

	  #echo $vormerkung['products_id'] .": $new_price - $new_price - ".$vormerkung['status']."<br>";

	  if($vormerkung['status']<1)

	     $min_price=0;

		$ISBN     =  $vormerkung['ISBN'];

		$products_id     =  $vormerkung['products_id'];

		$datum     =  date_mysql_transform($vormerkung['datum']);

		$email    =  $vormerkung['email'];

		$status    =  $vormerkung['status'];

		

    $product_query = "select Title  from products where products_id = '" . $products_id . "'";

    $product  = mysql_query($product_query);

    $product = mysql_fetch_array($product);

		$url = "http://buch.breviarium.de/x::$products_id.html";

		if($status>0){

     $body = "Der Titel Ihrer Vormerkung am $datum wurde neu erfasst:\n$url\nTitel: ".$product['Title']."\nISBN: $ISBN\nalter Preis: $min_price Euro, neuer Preis: $new_price Euro";

		}else{

     $body = "Der Titel Ihrer Vormerkung am $datum wurde neu erfasst:\n$url\nTitel: ".$product['Title'].", ISBN: $ISBN";

		}

    $reset="http://buch.breviarium.de/hidden/vormerkungen.php?&pID=$products_id&eMail=$email";

		$body .= "\nSobald ein günstigeres Angebot vorliegt werden Sie erneut benachrichtigt.\n\nVormerkung entfernen: $reset";

		$body  = utf8_decode($body);

		mail($email, "Breviarium: Ihre Vormerkung $ISBN", $body  , "From: post@breviarium.de\n");

		       #echo "update vormerkungen set date_sent = now(), min_price = '$new_price', status = 1 where products_id  = '$products_id' and email = '$email'<br>"  ;

		mysql_query("update vormerkungen set date_sent = now(), min_price = '$new_price', status = 1 where products_id  = '$products_id' and email = '$email'");

 }

 exit;

}



while($productsCheck = mysql_fetch_array($sqlAll, MYSQL_ASSOC)){

  $i++;

  $min_price     =  $productsCheck['min_price'];

  if($productsCheck['status']<1)

     $min_price=0;

	$ASIN     =  $productsCheck['ASIN'];

	$ISBN     =  $productsCheck['ISBN'];

	getDetailsFromASIN($ASIN, $min_price, 'ASIN', '');

  parseURL("http://www.eurobuch.com/extreq/meta/extquery.php?platform=48773&password=QnoOpE02&clientip=".$_SERVER['REMOTE_ADDR']."&format=xml8&order=price&special=1&doAbe=0&doAbeDe=1&doAlibris=1&doAlphamusic=0&doAmazon=0&doAmazonUk=0&doAmazonCom=0&doAmazon=0&doAmazonFr=0&doAntbo=0&doAntikbuch24=0&doProlibri=0&doAntiquario=0&doAudibile=0&doBiblio=0&doBiblioman=0&doBooklooker=1&doBUCH=1&doBuch24=0&doBuchfreund=1&doEBay=0&doEBS=0&doGuth=0&doHit=0&doJokers=0&doLibri=0&doAum=0&doZeilenreich=0&doZVAB=1&mediatype=0&isbn=".$ISBN);

}

$c+=10;

header("Location: http://buch.breviarium.de/hidden/vormerkungen.php?&k=$key&c=$c");



?>