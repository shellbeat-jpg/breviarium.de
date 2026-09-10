<?php
$ID = $_GET['ID'];
$LIM= $_GET['LIM'];
if($LIM<1)$LIM=0;
if($ID<1)$ID=0;
include("includes/db.php");
include("includes/functions.php");



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
	} else{
    $key = 0;
	}
}

define('AWS_KEY', $arrAwsKeys[$key]['AWS_KEY']);
define('AWS_SECRET_KEY', $arrAwsKeys[$key]['AWS_SECRET_KEY']);


$SQL="SELECT  *  FROM  buchfreund where ISBN != '' and date_checked = '0000-00-00'";

if(!isset($_GET['allRec'])){
 $sqlAll = mysql_query($SQL) or die(mysql_error());
 $allRec = mysql_num_rows($sqlAll);
  if($allRec<1){
	#	echo "NOREC"     ;
	 exit;
	}
}

if($c>$allRec){
    mysql_query("delete    FROM  buchfreund where title = ''");
    mysql_query("update buchfreund set date_checked = now() where date_checked = '0000-00-00' and ISBN != ''");
	#	echo "EOF";
		exit;
}

$check = mysql_query("$SQL LIMIT $c, 10") or die(mysql_error());
if(!mysql_num_rows($check)){
		mysql_query("delete    FROM  buchfreund where title = ''");
    mysql_query("update buchfreund set date_checked = now() where date_checked = '0000-00-00' and ISBN != ''");
    #echo "NOREC"     ;
		exit;
}
while($productsCheck = mysql_fetch_array($check, MYSQL_ASSOC)){
  $i++;
	$ISBN     =  $productsCheck['ISBN'];
	$ID     =  $productsCheck['id'];
	#echo $ISBN ." ";
	if(getDetailsFromASIN($ISBN, MIN)){
     mysql_query("update buchfreund set title = '' where id  = '$ID'");
     	#echo " success ";
	}

     
#	echo "<br>";
}

$c+=10;
header("Location: http://buch.breviarium.de/hidden/checkBuchfreundItemsFromISBN.php?&k=$key&c=$c&allRec=$allRec&LIM=".($LIM+10));
#echo "<script>\n";
#echo "document.location.href=\"http://buch.breviarium.de/hidden/checkBuchfreundItemsFromISBN.php?&k=$key&c=$c&allRec=$allRec&LIM=".($LIM+10)."\"\n";
#echo "</script>\n";
?>
