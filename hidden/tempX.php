<?php
set_time_limit(0);
ignore_user_abort(true);
include("includes/db.php");
include("includes/functions.php");
define('MIN', 50);
define('MAX', 2000);
#extract($_SERVER['QUERY_STRING']);

function writeFile($data,$perm="a"){
    $filename='log.txt';
    if (!$handle = fopen($filename, $perm)) {
         print "Kann die Datei $filename nicht öffnen";
         exit;
    }
    if (!fwrite($handle, $data)) {
        print "Kann in die Datei $filename nicht schreiben";
        exit;
    }
    fclose($handle);
    return true;
}



writeFile("\n" . $_SERVER['REQUEST_URI']);

#echo $_SERVER['REQUEST_URI'];
#exit;
#writeFile("1 ");
$arrBinding=array('Spielkarten', 'MP3 CD', 'Microfilm', 'Tageskalender', 'Diskette', 'Poster', 'Schautafel', 'Kalender', 'Broschüre', 'Zubehör', 'Videokassette', 'Digital Download', 'Landkarte', 'Vorbespielter Audioplayer', 'Stoffbilderbuch', 'Loseblattsammlung', 'Hörkassette', 'Musiknoten',  'Geschenkartikel', 'Kindle Edition', 'CD-ROM', 'Audio CD');
include("arrCategories.php");
#writeFile("2 ");

if($page==false)
	$page = 1;
if($MinimumPrice==false)
	$MinimumPrice = MAX;
if($limit==false)
	$limit = 0;
if($k==false)
	$k = 0;
$key=$k;
if($c==false)
	$c = 1;

$dir="X";
if($r==false)
	$r = 0;
if($r>8){
	$r = 1;
	$dir="Y";
}
$r++;

if($c>1900){
	if($key==0){
    $key = 1;
	}elseif($key==1){
    $key = 2;
	} else{
    $key = 0;
	}
	$c =0;
}

define('AWS_KEY', $arrAwsKeys[$key]['AWS_KEY']);
define('AWS_SECRET_KEY', $arrAwsKeys[$key]['AWS_SECRET_KEY']);
#writeFile("3 ");


if($MinimumPrice<MIN || $page > 39){
  writeFile("\n$MinimumPrice<".MIN." || $page > 39");
	$MinimumPrice=MAX;
	$page=1;
	$limit++;
}

if($limit>=count($arrCategories)){
 writeFile("\n$limit>=".count($arrCategories));
  #writeFile("4 ");
	$SQL = "update configuration set configuration_value = 'http://buch.breviarium.de/hidden/tempX.php' where configuration_key = 'AMAZON_LAST_URL'";
	mysql_query($SQL) or writeFile("\n$SQL\n".mysql_error()."\n");
	# Titel die nicht heute aktualisiert wurden (= nicht vorhanden) auf Status:0 setzen
  writeFile("\nImport abgeschlossen!");
	#echo "Import abgeschlossen!";
	exit;
}
#writeFile("5 ");
# Scriptabgleich URL
if($_SERVER['REQUEST_URI']== "/hidden/tempX.php"){
  #writeFile("6 ");
	$check = mysql_query("SELECT configuration_value  FROM configuration where configuration_key = 'AMAZON_LAST_URL'");
	$amazonCheck = mysql_fetch_array($check, MYSQL_ASSOC);
	$amazonCheck = $amazonCheck['configuration_value'];
	if($amazonCheck!='http://buch.breviarium.de/hidden/tempX.php'){
    #writeFile("61, \$amazonCheck: $amazonCheck ");
	  header("Location: $amazonCheck");
   #writeFile("62 ");
	  exit;
	}
}else{
 #writeFile("7 ");
 #header("refresh:90,url=http://buch.breviarium.de/hidden/tempX.php?&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice");
}
#writeFile("8 ");
$SQL = "update configuration set configuration_value  = 'http://buch.breviarium.de/hidden/tempX.php?&r=$r&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice' where configuration_key = 'AMAZON_LAST_URL'";
mysql_query($SQL) or writeFile("\n".mysql_error()."\n");
$SQL = "update configuration set configuration_value  = now() where configuration_key = 'AMAZON_DATE_URL'";
mysql_query($SQL) or writeFile("\n".mysql_error()."\n");


$categories_id=$arrCategories[$limit];
$TotalPages=getOffersFromPriceAndNode($MinimumPrice, $MinimumPrice+4.99, $categories_id, $page);
writeFile("\n\$MinimumPrice: $MinimumPrice, \$categories_id:$categories_id, \$page:$page");
if($page>=$TotalPages){
	$MinimumPrice=$MinimumPrice-5;
	$page=0;
}
$c++;
writeFile("\nnextLocation: http://buch.breviarium.de/hidden/temp$dir.php?&r=$r&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice");
#sleep(1);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit
#header("Location: http://buch.breviarium.de/hidden/temp.php?&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice");
header("Location: http://buch.breviarium.de/hidden/temp$dir.php?&r=$r&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice");
exit;
?>