<?php

include "simplehtmldom/simple_html_dom.php";
include("includes/db.php");
include("includes/functions.php");

function parseBuchfreund($url){
	$html = file_get_html($url);

	$arrData=array();
	$arrReturn=array();
	$i=0;
	foreach($html->find('td.result-details') as $p) {
	    # var_dump($p);
	    $arrData[$i]=array();
	    $j=0;
			foreach($p->find('a') as $a){
					if($j==0)
           $arrData[$i]['title']=$a->innertext;
          if($j==1 && strlen($a->innertext) == 10)
             $arrData[$i]['isbn']=$a->innertext;
          $j++;
			}
      $j=0;
			foreach($p->find('b') as $a){
         if($j==1)
            $arrData[$i]['author']=$a->innertext;
         $j++;
			}


			foreach($p->find('div.result-description') as $a){
         $arrData[$i]['description']=str_replace("Buchbeschreibung: ", "", strip_tags($a->innertext));
			}


			    
            echo $arrData[$i]['title']   ."<br>" ;
             echo $arrData[$i]['author']   ."<br>" ;
            echo $arrData[$i]['isbn']   ."<br>" ;
				echo $arrData[$i]['description']   ."<br>" ;
					echo "<br>" ;

	  $i++;
	}

	#return $arrReturn ;
}

$arrReturn = parseBuchfreund("http://www.abebooks.de/servlet/SearchResults?kn=Sammlerst%FCck&prl=10.00&recentlyadded=2day&sortby=1&sts=t&x=43&y=18");








?>