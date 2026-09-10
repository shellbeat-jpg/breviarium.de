<?php
$robots = array(
	't-online' => array('q'),
	'google' => array('q','as_q','as_epq'),
	'fireball' => array('q'),
	'lycos' => array('query'),
	'yahoo' => array('p'),
	'web.de' => array('su'),
	'aol' => array('q'),
	'msn' => array('q')
);

function isGoogleInstantPreview(){
    if (strpos($_SERVER['HTTP_USER_AGENT'],"Google Web Preview")){
        return true;
    }else{
        return false;
    }
}

function isGoogleBot(){
    if (strpos($_SERVER['HTTP_USER_AGENT'],"crawler") || strpos($_SERVER['HTTP_USER_AGENT'],"spider") || strpos($_SERVER['HTTP_USER_AGENT'],"bot") || strpos($_SERVER['HTTP_USER_AGENT'],"libwww")){
        $_SESSION['isBot'] = true;
        return true;
    }else{
        return false;
    }
}

function parseHttpString($searchValue){
	#$searchValue=strtolower($searchValue);
	$searchValue=str_replace("Ã¤","ä",$searchValue);
	$searchValue=str_replace("Ã„","Ä",$searchValue);
	$searchValue=str_replace("Ã¼","ü",$searchValue);
	$searchValue=str_replace("Ãœ","Ü",$searchValue);
	$searchValue=str_replace("Ã¶","ö",$searchValue);
	$searchValue=str_replace("Ã-","Ö",$searchValue);
	$searchValue=str_replace("ÃŸ","ß",$searchValue);
	$searchValue=str_replace(":"," ",$searchValue);
	$searchValue=rawurldecode($searchValue);
	$searchValue=str_replace("-"," ",$searchValue);
	$searchValue=str_replace("."," ",$searchValue);
	$searchValue=str_replace(","," ",$searchValue);
	$searchValue=str_replace(";"," ",$searchValue);
	$searchValue=str_replace("\"","",$searchValue);
	$searchValue=str_replace("/"," ",$searchValue);
	$searchValue=str_replace("\\","",$searchValue);
	#$searchValue=str_replace(" buch ","buch",$searchValue);
	#$searchValue=str_replace("bücherei","",$searchValue);
	#$searchValue=str_replace(" gebraucht ","gebraucht",$searchValue);
	$searchValue=str_replace("antiquariat","",$searchValue);
	$searchValue=str_replace("breviarium","",$searchValue);
	$searchValue=trim($searchValue);
	return $searchValue;
}


function getSearchdot(){
  global $robots;

	$searchdots=array_keys($robots);
	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
	$suchmaschine = $HTTP_REFERER;

	if (true == strpos("google",$HTTP_REFERER) && !strpos("Googlebot",$HTTP_REFERER)) $suchmaschine = 'google';
	elseif (true == strpos("t-online",$HTTP_REFERER)) $suchmaschine = 't-online';
	elseif (true == strpos("fireball",$HTTP_REFERER) && !strpos("KIT-Fireball",$HTTP_REFERER)) $suchmaschine = 'fireball';
	elseif (true == strpos("lycos",$HTTP_REFERER)) $suchmaschine = 'lycos';
	elseif (true == strpos("yahoo",$HTTP_REFERER) && !strpos("Yahoo!",$HTTP_REFERER)) $suchmaschine = 'yahoo';
	elseif (true == strpos("web.de",$HTTP_REFERER)) $suchmaschine = 'web.de';
	elseif (true == strpos("aol",$HTTP_REFERER)) $suchmaschine = 'aol';
	elseif (true == strpos("msn",$HTTP_REFERER) && !strpos("MSNBot",$HTTP_REFERER)) $suchmaschine = 'msn';

	return $suchmaschine ;
}

function restyleResult(){
	if(isGoogleBot())
		return "";
	global $robots;

	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
  $suchmaschine = getSearchdot();
  
	if($suchmaschine != $HTTP_REFERER){
		$httpStr=substr($HTTP_REFERER,strpos($HTTP_REFERER,'?')+1);
		parse_str($httpStr);
		$strHeader="";
		$strJs="<script>\n";
		$strJs.="arrHighlight=new Array(\n";
		$s=0;
		for($i=0;$i<count($robots[$suchmaschine]);$i++){
		 if($$robots[$suchmaschine][$i]!=null){
			$searchValue=parseHttpString($$robots[$suchmaschine][$i]);
			$arr_temp = explode(" ",$searchValue);
			for($j=0;$j<count($arr_temp);$j++){
				if(trim($arr_temp[$j])!=""){
					$strJs.="\"".trim($arr_temp[$j])."\",\n";
					$strHeader.="<span class='searchword$s'>".trim($arr_temp[$j])."</span>,\n";
					$s>3?$s=0:$s++;
				}
			}
		 }
	  }

		if(count($robots[$suchmaschine])>0){
			$strHeader=substr($strHeader,0,strlen($strHeader)-2);
			$strJs=substr($strJs,0,strlen($strJs)-2);
		}
		
		$strJs.="\n);\n";
    $strJs.="window.onload = googleSearchHighlight;\n";
		$strJs.="</script>\n";
    echo $strJs;
		return $strHeader ;
	}
}



function requestRedirect(){
	global $robots, $_GET;
  $strReturn="";

	if(isGoogleBot())
		return false;

  if(basename($_SERVER['SCRIPT_NAME']) != FILENAME_DEFAULT || !isset($_GET['cPath']))
    return false;
    
	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
  $suchmaschine = getSearchdot();

	if($suchmaschine != $HTTP_REFERER){
		$httpStr=substr($HTTP_REFERER,strpos($HTTP_REFERER,'?')+1);
		parse_str($httpStr);
		for($i=0;$i<count($robots[$suchmaschine]);$i++){
		 if($$robots[$suchmaschine][$i]!=null){
			$searchValue=parseHttpString($$robots[$suchmaschine][$i]);
			$arr_temp = explode(" ",$searchValue);
			for($j=0;$j<count($arr_temp);$j++){
				if(trim($arr_temp[$j])!="")
					$strSearch.= trim($arr_temp[$j])." ";
			}
		 }
	  }
	  $strSearch = trim($strSearch);
		if($strSearch=="site buch de")
		   return "";
		   
		if(strlen(trim($strSearch))>3){
     $strReturn="<script>\n";
     $strReturn.= "alert(\"Sie werden weitergeleitet zum Suchergebnis für '$strSearch'\");\n" ;
     $strReturn.="location.replace('".HTTP_SERVER."/".FILENAME_ADVANCED_SEARCH_RESULT."?keywords=".$strSearch."');\n";
		 #$strReturn.="window.onload = initSearch('".HTTP_SERVER."/".FILENAME_ADVANCED_SEARCH_RESULT."?keywords=".$strSearch."');\n";
     $strReturn.="</script>\n";
		}

		return $strReturn ;
	}
}
?>