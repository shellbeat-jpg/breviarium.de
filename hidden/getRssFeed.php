<?php

include "simplehtmldom/simple_html_dom.php";
include("includes/db.php");
include("includes/functions.php");

$date = date('Y-m-d', time());
$url = 'http://www.buchfreund.de/topNewEntries.php?dDate='.$date.'&page=%s';
#$url = "http://www.buchfreund.de/topNewEntries.php?dDate=2011-11-14&page=%s";

function parseBuchfreund($url){
	$html = file_get_html($url);
	$arrData=array();
	$arrReturn=array();
	$i=0;
	#echo $url ."<br>";
	$arrData=array();
	$arrTemp=explode('<div class="catalogListing">', $html);
	$arrTemp=explode('</div></div>', $arrTemp[1]);
  $html = $arrTemp[0];
  $html = str_replace("</ul>", "", $html);
  $html = str_replace("<ul>", "", $html);
  $html = trim($html);
  $html = str_get_html($html) ;
  $i=0;
  foreach($html->find('span.small') as $p) {
	  $ISBN   = '';
    if(strrpos($p->innertext, 'ISBN: ')===false){
		}else{
		  $arrTemp=explode('ISBN: ', $p->innertext);
		  $ISBN   =  substr($arrTemp[1], 0, 10);
		}
		$arrData[$i]['ISBN']=$ISBN;
    #echo $ISBN."<br>";
    #echo  $p->innertext."<br>";
    $i++;
	}
	$i=0;
  foreach($html->find('a.titleLink') as $a) {
    #echo  $a->innertext."<br>";
    $arrData[$i]['title']=$a->innertext;
    $i++;
	}
  return $arrData ;
	exit;
  #echo $html ;
  #$html = str_get_html($arrTemp[0]) ;
  #var_dump($html);
  #foreach($html->find('p.metaData2') as $p) {
  #  echo  $a->innertext."<br>";
	#}
	foreach($html->find('p.metaData2') as $p) {
	    $arrData[$i]=array();


	    
			foreach($p->find('a.titleLink') as $a)
	        $arrData[$i]['title']=$a->innertext;
	        

	        
			foreach($p->find('small') as $a)
			    $arrData[$i]['description']=$a->innertext;
      echo $arrData[$i]['description'] ."<br>";
	  $i++;

	}
	 #var_dump($html->find('p.metaData2'));
	exit;
	foreach($html->find('p.metaData2') as $p) {
	echo $p->title ."<br>";
	}
	foreach($html->find('div.catalogListing') as $p) {
	
	    for($i=0; $i < count($arrData); $i++){

		    if(strrpos($arrData[$i]['title'], $p->title)===false){
				}else{
				  $ISBN   = '';
			    if(strrpos($arrData[$i]['description'], 'ISBN: ')===false){
					}else{
					  $arrTemp=explode('ISBN: ', $arrData[$i]['description']);
					  $ISBN   =  substr($arrTemp[1], 0, 10);
					}
				  $arrReturn[]=array('ISBN' => $ISBN, 'title' => $p->title, 'image' => 'http://www.buchfreund.de/' . $p->href);
				}
			}
	}

  exit;
	return $arrReturn ;
}



for($i=0; $i < 3; $i++){
  $link = sprintf($url, $i);

  $arrReturn = parseBuchfreund($link);
  #var_dump($arrReturn);
  #exit;
  for($j=0; $j < count($arrReturn); $j++){
      $ISBN =  $arrReturn[$j]['ISBN'];
      $title = $arrReturn[$j]['title'];
		 $title = utf8_encode($title); #
			$title = preg_replace("/[^0-9a-zA-ZäÄüÜöÖ]/",' ',$title);
		  $newTitle ="";
			$arrTemp   = explode(' ', $title);
			for($i=0; $i < count($arrTemp); $i++){
					if(strlen($arrTemp[$i])>3)
		         $newTitle.=$arrTemp[$i]. " ";
			}
		  $title=utf8_decode($newTitle); #  utf8_decode
      
      $image = $arrReturn[$j]['image'];
      if($ISBN!=''){
		    $check = mysql_query("SELECT * FROM  products where ISBN = '$ISBN'");
	      if(!mysql_num_rows($check)){
		      $SQL = "Insert into buchfreund (date_added, ISBN, title, image) values( '$date', '". $ISBN ."', '". str_replace("'", "\'", $title) ."', '". $image ."')";
					mysql_query($SQL) or die($SQL."<br>".mysql_error());
				}
			}else{
	      $SQL = "Insert into buchfreund (date_added, ISBN, title, image) values( '$date', '". $ISBN ."', '". str_replace("'", "\'", $title) ."', '". $image ."')";
				mysql_query($SQL) or die($SQL."<br>".mysql_error());
			}
  }
}
      
/*
$start=strtotime("15 June 2011");
$now=strtotime("15 June 2011");
#$now=strtotime(date("d F Y"));
while($now>=$start)
{

	    $date = date('Y-m-d', $now);
	    
	    $link = sprintf($url, $date, '1');
	    $arrReturn = parseBuchfreund($link);
	    echo count($arrReturn) ." ";
      for($j=0; $j < count($arrReturn); $j++){
          $ISBN =  $arrReturn[$j]['ISBN'];
          $title = $arrReturn[$j]['title'];
          $image = $arrReturn[$j]['image'];
          $SQL = "Insert into buchfreund (date_added, ISBN, title, image) values( '$date', '". $ISBN ."', '". str_replace("'", "\'", $title) ."', '". $image ."')";
          mysql_query($SQL) or die($SQL."<br>".mysql_error());
      }
	    # SQL
     $now=$now-(60*60*24);

}
*/







?>