<?php

function xml_startElement($parser, $name, $attrs)
{   global $product;

 $eISBN   =  $product->data['ISBN'];
 $eTITLE   =  $product->data['products_name'];
 $eID   =  $product->data['products_id'];
 $cPrice  =  $product->data['products_price'];

	if($name=="RESULTLIST") {
	} else if($name=="BOOK") {

      # echo "\$eTITLE: $eTITLE<br>"  ;
     #   echo "\$cPrice: $cPrice<br>"  ;
      # echo "PLATFORM: ".$attrs["PLATFORM"]."<br>"  ;
     # echo "PRICEEUR: ".$attrs["PRICEEUR"]."<br>"  ;


			if($eISBN == '' && strrpos($attrs["TITLE"], $eTITLE)===false){
			}elseif($eISBN != '' && $attrs["PLATFORM"] != '' && substr($attrs["PLATFORM"], 0, 6) != "Amazon" && substr($attrs["PLATFORM"], 0, 4) != "Ebay"){
        $COMMENT = str_replace("'", "`", $attrs["COMMENT"]);
				$SQL = "Delete from amazonOffers where id_amazon = '$eID' and Platform = '". $attrs["PLATFORM"] ."'";
				#$SQL = "Delete from amazonOffers where id_amazon = '$eID' and Platform = '". $attrs["PLATFORM"] ."' and (ConditionNote  = '". $COMMENT ."' || oAmount > ". $cPrice . ")";  #

			#if($eISBN == '3933033659')
			#echo "\$cPrice: $cPrice<br>"  ;
				xtc_db_query($SQL) or die($SQL."<br>".mysql_error());
				if($attrs["PRICEEUR"] <= $cPrice){
					# ABEBOOKS BOOK-ID für ZANOX
				  $id_abebooks = "";
					if($attrs["PLATFORM"]=='Abebooks-Deutsch'){
							$EAN = substr($COMMENT, 6, 13);
              $request =   sprintf(URL_ABE_XML, $EAN);
					    $response = @file_get_contents($request);
							if (isset($response)) {
					      $pxml = simplexml_load_string($response);
					      if (isset($pxml)) {
					        $id_abebooks = $pxml->Book->bookId;
					      }
					    }
					}

					$SQL = "Insert into amazonOffers (id_amazon, id_abebooks, date_added, EurobuchID, Platform, oAmount, ConditionNote, AddToCartUrl, year, versandkosten_eur, versandkosten_bem, picurl) values(".$eID.", '$id_abebooks', now(), '". $attrs["ID"] ."', '". $attrs["PLATFORM"] ."', ". $attrs["PRICEEUR"] .", '". $COMMENT ."', '". $attrs["URL"] ."', '". $attrs["YEAR"] ."', '". $attrs["VERSANDKOSTEN_EUR"] ."', '".$attrs["VERSANDKOSTEN_BEM"]."', '"  .$attrs["PICURL"]."')";
         	#echo "$SQL<br>"  ;
				  xtc_db_query($SQL) or die($SQL."<br>".mysql_error());
				  $product->eurobuchInsert = true;
      if($attrs["PLATFORM"]=='Abebooks-Deutsch')
             xtc_db_query("Update products set EAN = '".substr($COMMENT, 7, 13)."' where products_id = '$eID'") or die(mysql_error());
	}
			}
   		flush();
	}
}

function xml_endElement($parser, $name)
{
}



function parseURL($url) {
	$fp=fopen($url,"rb");
	if(!$fp) {
		return("Could not open $url");
	}
	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "xml_startElement", "xml_endElement");
	while($data=fread($fp,4096)) {
		if (!xml_parse($xml_parser, $data,feof($fp))) {
		   $ret=sprintf("XML error: %s at line %d",
					   xml_error_string(xml_get_error_code($xml_parser)),
					   xml_get_current_line_number($xml_parser));
			xml_parser_free($xml_parser);
			return($ret);

		}

	}
	xml_parser_free($xml_parser);
	fclose($fp);
	return(true);
}


function addEurobuchListings(){
 global $product;
 if($product->data['ISBN']!=''){
		#echo $product->data['ISBN']  ;
	 #echo "http://www.eurobuch.com/extreq/meta/extquery.php?platform=48773&password=QnoOpE02&clientip=77.188.23.234&format=xml8&order=price&mediatype=0&isbn=".$product->data['ISBN']."<br>";
	$Eurobuch = parseURL("http://www.eurobuch.com/extreq/meta/extquery.php?platform=48773&password=QnoOpE02&clientip=".$_SERVER['REMOTE_ADDR']."&format=xml8&order=price&special=1&doAbe=0&doAbeDe=1&doAlibris=1&doAlphamusic=0&doAmazon=0&doAmazonUk=0&doAmazonCom=0&doAmazon=0&doAmazonFr=0&doAntbo=0&doAntikbuch24=0&doProlibri=0&doAntiquario=0&doAudibile=0&doBiblio=0&doBiblioman=0&doBooklooker=1&doBUCH=1&doBuch24=0&doBuchfreund=1&doEBay=0&doEBS=0&doGuth=0&doHit=0&doJokers=0&doLibri=0&doAum=0&doZeilenreich=0&doZVAB=1&mediatype=0&isbn=".$product->data['ISBN']);
	  return  $Eurobuch;
	# parseURL2("http://www.eurobuch.com/extreq/meta/extquery.php?platform=48773&password=QnoOpE02&clientip=".$_SERVER['REMOTE_ADDR']."&format=xml8&order=price&mediatype=0&isbn=".$product->data['ISBN']);

	 # return $ret;
 }
}


function updateOfferData($products_id){
   $SQL = "UPDATE products AS p  LEFT JOIN (
        SELECT  id_amazon, min( oAmount ) AS minPrice
        FROM  amazonOffers WHERE EurobuchID > 1 GROUP BY  id_amazon ) AS o
				ON o.id_amazon = p.products_id
				SET   p.minPrice = o.minPrice where   p.products_id = $products_id";
    		xtc_db_query($SQL) or die($SQL."<br>".mysql_error());

   $SQL = "UPDATE products AS p  LEFT JOIN (
        SELECT  id_amazon, count( * ) AS total
        FROM  amazonOffers WHERE EurobuchID > 1 GROUP BY  id_amazon ) AS o
				ON o.id_amazon = p.products_id
				SET   p.sumOffers = o.total where   p.products_id = $products_id";
    		xtc_db_query($SQL) or die($SQL."<br>".mysql_error());
}






?>