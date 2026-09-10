<?php
set_time_limit(0);
ignore_user_abort(true);
include("includes/db.php");
include("includes/functions.php");
include("arrSteps.php");
extract($_SERVER['QUERY_STRING']);
$url = "http://buch.breviarium.de/hidden/temp_6.php?&chapter=$chapter&STEP=$STEP&k=$k&c=$c&limit=$limit&page=$page&MinimumPrice=$MinimumPrice";


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



#writeFile("\n" . $_SERVER['REQUEST_URI']);

#echo $_SERVER['REQUEST_URI'];
#exit;
#writeFile("1 ");
$arrBinding=array('Spielkarten', 'MP3 CD', 'Microfilm', 'Tageskalender', 'Diskette', 'Poster', 'Schautafel', 'Kalender', 'Broschüre', 'Zubehör', 'Videokassette', 'Digital Download', 'Landkarte', 'Vorbespielter Audioplayer', 'Stoffbilderbuch', 'Loseblattsammlung', 'Hörkassette', 'Musiknoten',  'Geschenkartikel', 'Kindle Edition', 'CD-ROM', 'Audio CD');
include("arrCategories6.php");
#writeFile("2 ");

if($page==false)
	$page = 1;
if($MinimumPrice==false)
	$MinimumPrice = MIN;
if($limit==false)
	$limit = 0;
if($k==false)
	$k = 0;
$key=$k;
if($c==false)
	$c = 1;
if($chapter==false)
  $chapter = MIN;

if($c>1900){
	if($key==0){
    $key = 1;
	} else{
    $key = 0;
	}
	$c =0;
}

define('AWS_KEY', $arrAwsKeys[$key]['AWS_KEY']);
define('AWS_SECRET_KEY', $arrAwsKeys[$key]['AWS_SECRET_KEY']);

# Scriptabgleich URL
if($_SERVER['REQUEST_URI']== "/hidden/temp_6.php"){
	$check = mysql_query("SELECT configuration_value  FROM configuration where configuration_key = 'AMAZON_LAST_URL6'");
	$amazonCheck = mysql_fetch_array($check, MYSQL_ASSOC);
	$amazonCheck = $amazonCheck['configuration_value'];
	if($amazonCheck!='http://buch.breviarium.de/hidden/temp_6.php'){
	  header("Location: $amazonCheck");
	  exit;
	}
}else{
 # header("refresh:70,url=http://buch.breviarium.de/hidden/temp_6.php?&chapter=$chapter&STEP=$STEP&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice");
}
$SQL = "update configuration set last_modified = now(),  configuration_value  = 'http://buch.breviarium.de/hidden/temp_6.php?&chapter=$chapter&STEP=$STEP&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice' where configuration_key = 'AMAZON_LAST_URL6'";
mysql_query($SQL) or writeFile("\n".mysql_error()."\n");


if($chapter>MAX){
	 #echo " - \$chapter>MAX<br>";
   $MinimumPrice=MIN;
   $chapter = MIN;
   $limit++;
}

if($limit>=count($arrCategories)){
	$SQL = "update configuration set last_modified = now(),  configuration_value = 'http://buch.breviarium.de/hidden/temp_6.php' where configuration_key = 'AMAZON_LAST_URL6'";
	mysql_query($SQL) or writeFile("\n$SQL\n".mysql_error()."\n");
	echo "Import abgeschlossen!";
	exit;
}
$categories_id=$arrCategories[$limit];
	# echo $categories_id ;exit;
if(!isset($arrSteps[$categories_id][$chapter])){
  header("Location: http://buch.breviarium.de/hidden/temp_6.php?&chapter=".MIN."&STEP=10&k=$key&c=$c&limit=".($limit+1)."&page=1&MinimumPrice=".MIN);
	exit;
}

$STEP = $arrSteps[$categories_id][$chapter];
if($MinimumPrice+$STEP>MAX){
  $STEP=MAX-$MinimumPrice+0.01;
  #$limit++;
}


# echo "$categories_id - $chapter -> $STEP<br>";

#  echo "$categories_id: $STEP / ($chapter) " . $MinimumPrice ." - ".($MinimumPrice+$STEP-0.01)." \$page:$page<br>";
#$TotalPages=getOffersFromPriceAndNode($MinimumPrice, $MinimumPrice+$STEP-0.01, $categories_id, $page);
$TotalPages=getOffersFromPriceAndNode($MinimumPrice, $MinimumPrice+$STEP-0.01, $categories_id, $page, "*", 'temp_6.php', '6');

#  echo "\$page >= $TotalPages?<br>";
# Keine Seiten mehr gefunden
if($page >= $TotalPages || $page >= 40){
 # echo " - $page >= $TotalPages<br>";
	$page=0;
	if($MinimumPrice+$STEP>=$chapter+10){
	   if($STEP>10){
	     $chapter += $STEP;
	     $MinimumPrice=$chapter;
	     $STEP = $arrSteps[$categories_id][$chapter];
		 }else{
	     $chapter += 10;
	     $MinimumPrice=$chapter;
	     $STEP = $arrSteps[$categories_id][$chapter];
		 }
		 #echo  $MinimumPrice+$STEP." >MAX? ($chapter)<br>";
		 /*
		 if($MinimumPrice+$STEP>MAX){
         $STEP=MAX-$MinimumPrice;
         if($STEP<0){
            $chapter=round($chapter);
            if($chapter>=MAX)
  						$chapter = MIN;
            $STEP = $arrSteps[$categories_id][$chapter];
				 }
		 }
		 */

      #echo  "x $chapter +=".(10) ." - $STEP<br>";
	}else{
     $MinimumPrice=$MinimumPrice+$STEP;
	}
  #echo "\$MinimumPrice: $MinimumPrice<br>";
	#echo "\$chapter: $chapter<br>";
	#echo "\$arrSteps[$categories_id][$chapter]: ".$arrSteps[$categories_id][$chapter]."<br>";
  #echo "$MinimumPrice<br>";
}

if($MinimumPrice>MAX){
	 # echo " - \$chapter>MAX<br>";
   $MinimumPrice=MIN;
   $chapter = MIN;
   $limit++;
   $categories_id=$arrCategories[$limit];
   $STEP = $arrSteps[$categories_id][$chapter];
   if(!isset($STEP))
      $STEP=100;
   #echo "$categories_id: $chapter - $STEP<br>";
}

$c++;
#writeFile("\nnextLocation: http://buch.breviarium.de/hidden/temp_6.php?&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice");
#sleep(5);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit
# echo "<br><a href='http://buch.breviarium.de/hidden/temp_6.php?&chapter=$chapter&STEP=$STEP&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice'>chapter=$chapter&STEP=$STEP&MinimumPrice=$MinimumPrice&page=".($page+1)."&limit=$limit</a>";
header("Location: http://buch.breviarium.de/hidden/temp_6.php?&chapter=$chapter&STEP=$STEP&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice");
exit;
?>