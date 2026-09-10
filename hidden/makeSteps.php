<?php
set_time_limit(0);
ignore_user_abort(true);
include("includes/db.php");
include("includes/functions.php");
include("arrCategories111.php");

$strOut = "<?php\n";
$strOut .= "\$arrSteps = array();\n";
$arrSteps= array();
for($i=0; $i < count($arrCategories); $i++){
    $categories_id=$arrCategories[$i];
    echo  "$categories_id<br>";
			$SQL = "SELECT DISTINCT categories_id, minimumPrice, 10 / ( (
							count( TotalPages ) * min( TotalPages ) ) /40
							) AS tp
							FROM `categories_data_4th`
							WHERE categories_id =$categories_id
							GROUP BY minimumPrice
							ORDER BY minimumPrice";
			$result = mysql_query($SQL);
			while($check = mysql_fetch_array($result)){
			$step = (double) $check['tp'];
			$minPrice  =  round($check['minimumPrice']);
			if($check['tp']>0 && $minPrice >  0){
					echo $minPrice .": ".number_format($check['tp']/2, 1) ." - ".$check['tp']   ."<br>";
			      $step = 100;
         if($check['tp']<100)
            $step = 50;
         if($check['tp']<50)
            $step = 25;
         if($check['tp']<25)
            $step = 10;
         if($check['tp']<10)
            $step = 5;
         if($check['tp']<5)
            $step = 2.5;
         if($check['tp']<2.5)
            $step = 1;
         if($check['tp']<1)
            $step = 0.5;
            $arrSteps[$categories_id][$minPrice]=$step;
         if($minPrice > 50 && !isset($arrSteps[$categories_id][40])){
              $strOut .=  "\$arrSteps['$categories_id']['40'] = $step;\n";
              $arrSteps[$categories_id][40]=$step;
				 }

         $strOut .=  "\$arrSteps['$categories_id']['$minPrice'] = $step;\n";
			}
			}
}

for($i=0; $i < count($arrCategories); $i++){
  $categories_id = $arrCategories[$i];
  for($j=MIN; $j <= MAX; $j+=10){
         if(!isset($arrSteps[$categories_id][$j]) && isset($arrSteps[$categories_id][$j-10])){
              $strOut .=  "\$arrSteps['$categories_id']['$j'] = ".$arrSteps[$categories_id][$j-10].";\n";
              $arrSteps[$categories_id][$j]=$arrSteps[$categories_id][$j-10];
				 }
	}
}

$strOut .= "?".">";

$filename = 'arrStepsNew.php';
if (!$handle = fopen($filename, 'w')) {
     print "Kann die Datei $filename nicht öffnen";
     exit;
}
if (!fwrite($handle, $strOut)) {
    print "Kann in die Datei $filename nicht schreiben";
    exit;
}
fclose($handle);
    
echo $strOut;
?>