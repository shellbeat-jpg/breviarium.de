<?php
exit;
# http://alt.breviarium.de/hidden/temp.php?&k=9&c=398&limit=271&page=34&MinimumPrice=70
include("includes/db.php");
include("includes/functions.php");
include("arrCategories.php");
$LIM= $_GET['LIM'];
if($LIM<1)$LIM=0;


# http://www.worldcat.org/oclc/75938429
$arrBinding=array('Spielkarten', 'MP3 CD', 'Microfilm', 'Tageskalender', 'Diskette', 'Poster', 'Schautafel', 'Kalender', 'Broschüre', 'Zubehör', 'Videokassette', 'Digital Download', 'Landkarte', 'Vorbespielter Audioplayer', 'Stoffbilderbuch', 'Loseblattsammlung', 'Hörkassette', 'Musiknoten',  'Geschenkartikel', 'Kindle Edition', 'CD-ROM', 'Audio CD');

if(isset($_GET['page']))
	$page = trim($_GET['page']);

if(!isset($page) || $page<1)
	$page=1;


$key = trim($_GET['k']);
if($key<1)$key=0;

$c = trim($_GET['c']);
if($c<1)$c=0;

if($c>1900){
	if($key==0){
    $key = 1;
	}elseif($key==1){
    $key = 2;
	}elseif($key==2){
    $key = 3;
	} else{
    $key = 0;
	}
}

define('AWS_KEY', $arrAwsKeys[$key]['AWS_KEY']);
define('AWS_SECRET_KEY', $arrAwsKeys[$key]['AWS_SECRET_KEY']);


$SQL="SELECT  *  FROM  buchfreund where ISBN = '' and date_checked = '0000-00-00'";

if($limit<1)$limit=0;

if($MinimumPrice<MIN || $page > 39){
#	$MinimumPrice=MAX;
	$page=1;
	$limit++;
}

if(!isset($_GET['allRec'])){
 $sqlAll = mysql_query($SQL) or die(mysql_error());
 $allRec = mysql_num_rows($sqlAll);
  if($allRec<1){
    mysql_query("delete    FROM  buchfreund where title = ''");
    mysql_query("update buchfreund set date_checked = now() where date_checked = '0000-00-00' and ISBN = ''");
		#echo "NOREC"     ;
	 exit;
	}
}
if($c>$allRec){
    mysql_query("delete    FROM  buchfreund where title = ''");
    mysql_query("update buchfreund set date_checked = now() where date_checked = '0000-00-00' and ISBN = ''");
		#echo "EOF";
		exit;
}

#header("refresh:70;url=http://buch.breviarium.de/hidden/getOffersFromBuchfreund.php?&allRec=$allRec&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice");

$check = mysql_query("$SQL LIMIT $c, 10") or die(mysql_error());
while($productsCheck = mysql_fetch_array($check, MYSQL_ASSOC)){
  $i++;
	$Titel     =  $productsCheck['title'];
	$ID     =  $productsCheck['id'];
	$Titel = utf8_encode($Titel);
	$TotalPages=getOffersFromPriceAndNode(MIN, MAX, 0, $page, $Titel, 'getOffersFromBuchfreund.php');
	if($TotalPages>0)
     mysql_query("update buchfreund set title = '' where id  = '$ID'");
}

if($page>=$TotalPages){
	$page=0;
}
$c+=10;
header("Location: http://buch.breviarium.de/hidden/getOffersFromBuchfreund.php?&allRec=$allRec&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice");
#echo "<script>\n";
#echo "document.location.href=\"http://buch.breviarium.de/hidden/getOffersFromBuchfreund.php?&allRec=$allRec&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice\"\n";
#echo "</script>\n";


?>