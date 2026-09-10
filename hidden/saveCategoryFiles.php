<?php
set_time_limit(0);
ignore_user_abort(true);
include("includes/db.php");
include("includes/functions.php");


$strOut = "<?php\n";
$strOut .= "\$arrCategories=array();\n";
$strOut .= "%s\n";
$strOut .= "?".">";

$resultTotal = mysql_query("SELECT DISTINCT categories_id, count(categories_id)/7 AS total FROM categories_data");
$check = mysql_fetch_array($resultTotal);
$part=round($check['total']);
# echo $part;exit;
$resultCategories = mysql_query("SELECT DISTINCT categories_id, count( categories_id ) AS total
FROM categories_data
GROUP BY categories_id
ORDER BY categories_id");

$i=0;
$f=1;

while($check = mysql_fetch_array($resultCategories)){
 $i+=$check['total'];
 $strTemp.="\$arrCategories[] = ".$check['categories_id'].";\n";
 if($i>$part){
		# Speichern & zurücksetzen
		$filename = "arrCategories$f.php";
		if (!$handle = fopen($filename, 'w')) {
		     print "Kann die Datei $filename nicht öffnen";
		     exit;
		}
		if (!fwrite($handle, sprintf($strOut, $strTemp))) {
		    print "Kann in die Datei $filename nicht schreiben";
		    exit;
		}
		fclose($handle);

    $i=0;
    $f++;
    $strTemp="";
 }
}


$filename = "arrCategories$f.php";
if (!$handle = fopen($filename, 'w')) {
     print "Kann die Datei $filename nicht öffnen";
     exit;
}
if (!fwrite($handle, sprintf($strOut, $strTemp))) {
    print "Kann in die Datei $filename nicht schreiben";
    exit;
}
fclose($handle);
?>