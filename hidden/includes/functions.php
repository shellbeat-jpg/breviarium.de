<?php

function updateProductsPrice(){
	 $check = mysql_query("SELECT DISTINCT id_amazon, min(oAmount) as minAmount from amazonOffers GROUP BY id_amazon") or die($SQL."<br>".mysql_error());
	 while($productsCheck = mysql_fetch_array($check, MYSQL_ASSOC)){
	       mysql_query("update products set products.products_price = '".$productsCheck['minAmount']."' where products_id = '".$productsCheck['id_amazon']."'") or die("<br>".mysql_error());
	 }
}


	 
function setDetailPageURL($ASIN, $products_id){
	  global $db_link;
		$params = array("Operation"=>"ItemSearch",  "Keywords"=>$ASIN, "Condition"=>"All","MerchantId"=>"All","ResponseGroup"=>"Large","SearchIndex"=>"Books") ; #   # Offers/Offer/OfferAttributes
		$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);
		if(!$pxml){
			#	var_dump($pxml);
        return false;
		}else{
			 $item = $pxml->Items->Item;
			 if(!$item){
          # var_dump($pxml);
          return false;
			 }
 		   $Content = $item->EditorialReviews->EditorialReview->Content;
 		   $Content = str_replace("'", "\'", $Content);
			 $Source = $item->EditorialReviews->EditorialReview->Source;
			 $Source = str_replace("'", "\'", $Source);
       $DetailPageURL = $item->DetailPageURL;
       mysql_query("Update products set EditorialSource = '$Source', EditorialReview = '$Content', DetailPageURL = '$DetailPageURL' where products_id = '$products_id'")  or die("<br>".mysql_error());
	 }
	 return true;
}
	 
function setOffersFromASIN($ASIN, $products_id){
	global $db_link;

		$params = array("Operation"=>"ItemSearch",  "Keywords"=>$ASIN, "Condition"=>"All","MerchantId"=>"All","ResponseGroup"=>"Large","SearchIndex"=>"Books") ; #   # Offers/Offer/OfferAttributes
		$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);
		if(!$pxml){
        return false;
		}else{
			 mysql_query("delete from amazonOffers where amazonOffers.id_amazon  = '$products_id'") or die(mysql_error());
			 $offers=false;
			 $item = $pxml->Items->Item;
			 if(!$item)
			     return false;
			     
			 $ItemAttributes = $item->ItemAttributes;
			 $DetailPageURL  = $item->DetailPageURL;
 		   $Content = $item->EditorialReviews->EditorialReview->Content;
 		   $Content = str_replace("'", "\'", $Content);
			 $Source = $item->EditorialReviews->EditorialReview->Source;
			 $Source = str_replace("'", "\'", $Source);
			 
			 if($pxml->Items->Item->OfferSummary->TotalNew  < 1){
         $Nodes=$pxml->Items->Item->Offers->Offer;
				 foreach($Nodes as $Offer) {
             $MerchantId = $Offer->Merchant->MerchantId;
		         $OfferListingId = $Offer->OfferListing->OfferListingId;
		         $ExchangeId = $Offer->OfferListing->ExchangeId;
		         $SubCondition = $Offer->OfferAttributes->SubCondition;
		         $ConditionNote = $Offer->OfferAttributes->ConditionNote;
		         $oCurrencyCode = $Offer->OfferListing->Price->CurrencyCode;
             $ExchangeId = $Offer->OfferListing->ExchangeId;
		         $oAmount = $Offer->OfferListing->Price->Amount;
						 $oAmount = str_replace(",", "", $oAmount/100);
		         $oAmount = number_format($oAmount, 2);
						 $SQL = "INSERT INTO amazonOffers (id_amazon, MerchantId, OfferListingId, ExchangeId, oAmount, oCurrencyCode, SubCondition, ConditionNote)  VALUES($products_id, '".$MerchantId."', '".$OfferListingId."', '".$ExchangeId."', '".$oAmount."', '".$oCurrencyCode."', '". str_replace("'", "\'", ($SubCondition))."', '".str_replace("'", "\'", ($ConditionNote))."')";
						 #echo $SQL."<br><br>";      ;
						 mysql_query($SQL) or die($SQL."<br>".mysql_error());
						 $offers=true;
				}
			}

			$Title = $ItemAttributes->Title;
			
			mysql_query("delete from products_description where products_id  = '$products_id'") or die(mysql_error());
      $SQL = "Insert into products_description ( products_id, 	language_id, 	products_name ) values($products_id, 2, '".str_replace("'", "\'", ($Title))."')";
  	  #echo $SQL."<br><br>";
			mysql_query($SQL) or die("$SQL<br>".mysql_error());
			if($offers){
				 $OfferSummary = $item->OfferSummary;
				 $CurrencyCode = $OfferSummary->LowestUsedPrice->CurrencyCode;
				 $LowestNewPrice = str_replace(",", "", $OfferSummary->LowestNewPrice->Amount);
				 $LowestUsedPrice = str_replace(",", "", $OfferSummary->LowestUsedPrice->Amount);
				 $LowestCollectiblePrice = str_replace(",", "", $OfferSummary->LowestCollectiblePrice->Amount);
			   $LowestNewPrice = number_format($LowestNewPrice/100, 2);
			   $LowestUsedPrice = number_format($LowestUsedPrice/100, 2);
			   $LowestCollectiblePrice = number_format($LowestCollectiblePrice/100, 2);
			   $TotalNew = $OfferSummary->TotalNew;
				 $TotalUsed = $OfferSummary->TotalUsed;
				 $TotalCollectible = $OfferSummary->TotalCollectible;
	 			 $ListPrice = number_format($ItemAttributes->ListPrice->Amount/100, 2);
				 $products_price = ( ($LowestCollectiblePrice==0) || ($LowestUsedPrice>0&&$LowestUsedPrice<$LowestCollectiblePrice) ?$LowestUsedPrice:$LowestCollectiblePrice);

	       $SQL = "Update products set
								products_quantity = 0 ,
								products_status = 1,
								CurrencyCode = '".$CurrencyCode."',
								products_price = '".$products_price."',
								ListPrice = '".$ListPrice."',
								LowestNewPrice = '".$LowestNewPrice."',
								LowestUsedprice = 	'".$LowestUsedPrice."',
								LowestCollectiblePrice = '".$LowestCollectiblePrice."',
								TotalUsed = '".$TotalUsed."',
								TotalNew = '".$TotalNew."',
								DetailPageURL  = '".$DetailPageURL."',
								EditorialSource = '$Source',
								EditorialReview = '$Content',
								TotalCollectible = '".$TotalCollectible."',
								products_last_modified = Now()
								where products_id = '$products_id'";
			  }else{
				$SQL = "Update products set
								products_quantity = 0 ,
								products_status = 1
								where products_id = '$products_id'";
			  }
      #echo $SQL."<br><br>";
			mysql_query($SQL)  or die("$SQL<br>".mysql_error());

      mysql_query("delete from products_to_categories where products_id  = '$products_id'") or die(mysql_error());
			#echo $SQL."<br><br>";
      $arrCategory = getCategory($ASIN);
      $categories_id=$arrCategory[0];
			$SQL = "Insert into products_to_categories (products_id, categories_id) values($products_id, $categories_id)";     ;
			mysql_query($SQL)  or die("$SQL<br>".mysql_error());
						 
      mysql_query("delete from amazonCreator where id_amazon  = '$products_id'") or die(mysql_error());
 		  foreach( $ItemAttributes->Creator as $Creator) {
			 	$arrReturn[$i]['Creator'][]=array('Role' => $Creator['Role'], 'Creator' => $Creator);
			  $SQL = "INSERT INTO amazonCreator (creator, role, id_amazon) VALUES('". str_replace("'", "\'", ($Creator))."', '". str_replace("'", "\'", ($Creator['Role']))."', $products_id)";
				#echo $SQL."<br><br>";      ;
				mysql_query($SQL) or die($SQL."<br>".mysql_error()); # or die($SQL."<br>".mysql_error());
			}
	 }
	 return true;
}


function addOfferListingId($LIM){
	global $db_link, $arrBinding, $limit, $page, $c, $arrAwsKeys, $key;
	

   $check = mysql_query("SELECT distinct ASIN  FROM products group by ASIN LIMIT $LIM, 25") or die($SQL."<br>".mysql_error());
	 while($productsCheck = mysql_fetch_array($check, MYSQL_ASSOC)){
				 $ASIN   =  $productsCheck['ASIN'];
         $SQL = "SELECT   products.products_id, products.SmallImage,  amazonOffers.oAmount, amazonOffers.id, amazonOffers.MerchantId  FROM products LEFT JOIN amazonOffers ON products.products_id = amazonOffers.id_amazon WHERE products.ASIN = '$ASIN' and amazonOffers.OfferListingId = ''";
         $checkEmpty = mysql_query($SQL) or die($SQL."<br>".mysql_error());
         if(mysql_num_rows($checkEmpty)){
            $productsCheck = mysql_fetch_array($checkEmpty, MYSQL_ASSOC);
            $products_id  = $productsCheck['products_id'];
            $c++;
						$params = array("Operation"=>"ItemSearch",  "Keywords"=>$ASIN, "Condition"=>"All","MerchantId"=>"All","ResponseGroup"=>"Large","SearchIndex"=>"Books") ; #   # Offers/Offer/OfferAttributes
						$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);
						if(!$pxml){
								$key++;
								echo "$key++<br>";
								if($key>=count($arrAwsKeys))
						    $key = 0;
							  echo "<script>\n";
							  echo "document.location.href=\"http://buch.breviarium.de/hidden/addOfferListingId.php?&c=0&key=$key&LIM=".$LIM."\"\n";
							  echo "</script>\n";
						}else{
							   $LargeImage = (String) $pxml->Items->Item->LargeImage->URL;
							   $MediumImage = (String) $pxml->Items->Item->MediumImage->URL;
							   $SmallImage = (String) $pxml->Items->Item->SmallImage->URL;
							   if($LargeImage!=''){
								   echo " - $c<br>";
								   $SQL = "delete from amazonOffers where amazonOffers.id_amazon  = '$products_id'";
									 mysql_query($SQL) or die($SQL."<br>".mysql_error()); # or die($SQL."<br>".mysql_error());
                   if($pxml->Items->Item->OfferSummary->TotalNew  < 1){
                     mysql_query("UPDATE products set SmallImage = '$SmallImage', MediumImage = '$MediumImage', LargeImage = '$LargeImage' where products_id = '$products_id'") or die("<br>".mysql_error()); # or die($SQL."<br>".mysql_error());
	                   $Nodes=$pxml->Items->Item->Offers->Offer;
										 foreach($Nodes as $Offer) {
		                     $MerchantId = $Offer->Merchant->MerchantId;
								         $OfferListingId = $Offer->OfferListing->OfferListingId;
								         $ExchangeId = $Offer->OfferListing->ExchangeId;
								         $SubCondition = $Offer->OfferAttributes->SubCondition;
								         $ConditionNote = $Offer->OfferAttributes->ConditionNote;
								         $oCurrencyCode = $Offer->OfferListing->Price->CurrencyCode;
				                 $ExchangeId = $Offer->OfferListing->ExchangeId;
								         $oAmount = $Offer->OfferListing->Price->Amount;
					 							 $oAmount = str_replace(",", "", $oAmount/100);
								         $oAmount = number_format($oAmount, 2);
												 $SQL = "INSERT INTO amazonOffers (id_amazon, MerchantId, OfferListingId, ExchangeId, oAmount, oCurrencyCode, SubCondition, ConditionNote)  VALUES($products_id, '".$MerchantId."', '".$OfferListingId."', '".$ExchangeId."', '".$oAmount."', '".$oCurrencyCode."', '". str_replace("'", "\'", ($SubCondition))."', '".str_replace("'", "\'", ($ConditionNote))."')";
				                 mysql_query($SQL) or die($SQL."<br>".mysql_error());
												 echo "$SQL<br><br>";
										}
									}
								 }
						}
         }
	 }
	 return true;
}



function deleteDuplicatesAmazon($LIM){
	 $check = mysql_query("SELECT DISTINCT ASIN, id FROM  amazon group by ASIN LIMIT $LIM, 200") or die(mysql_error());
	 echo "SELECT DISTINCT ASIN, id FROM  amazon group by ASIN LIMIT $LIM, 200<br>" ;
      if(!mysql_num_rows($check)){
					exit;
			}

			$i=0;
			while($productsCheck = mysql_fetch_array($check, MYSQL_ASSOC)){
        $i++;
				$ASIN     =  $productsCheck['ASIN'];
        $idCheck = $productsCheck['id'];
        echo "$i: delete from amazon where ASIN = '$ASIN' and id != $idCheck<br>";
				mysql_query("delete from amazon where ASIN = '$ASIN' and id != $idCheck") or die(mysql_error());
			}
}
function deleteDuplicates($LIM){
	 $check = mysql_query("SELECT DISTINCT ASIN, products_id FROM  products group by ASIN LIMIT $LIM, 200") or die(mysql_error());
	 echo "SELECT DISTINCT ASIN, products_id FROM  products group by ASIN LIMIT $LIM, 200<br>" ;
      if(!mysql_num_rows($check)){
					mysql_query("DELETE amazonOffers FROM amazonOffers LEFT JOIN products ON amazonOffers.id_amazon=products.products_id WHERE products.products_id IS NULL");
					mysql_query("DELETE amazonCreator FROM amazonCreator LEFT JOIN products ON amazonCreator.id_amazon=products.products_id WHERE products.products_id IS NULL");
          mysql_query("DELETE products_to_categories FROM products_to_categories LEFT JOIN products ON products_to_categories.products_id=products.products_id WHERE products.products_id IS NULL");
          mysql_query("DELETE products_description FROM products_description LEFT JOIN products ON products_description.products_id=products.products_id WHERE products.products_id IS NULL");
					exit;
			}

			$i=0;
			while($productsCheck = mysql_fetch_array($check, MYSQL_ASSOC)){
        $i++;
				$ASIN     =  $productsCheck['ASIN'];
        $idCheck = $productsCheck['products_id'];
        echo "$i: delete from products where ASIN = '$ASIN' and products_id != $idCheck<br>";
				mysql_query("delete from products where ASIN = '$ASIN' and products_id != $idCheck") or die(mysql_error());
			}
}

function checkTitel($ASIN){
	 $check = mysql_query("select products_id, products_date_added from products where ASIN = '$ASIN'");
   $products_date_added = date("Y-m-d H:i:s");
	 if(mysql_num_rows($check)>0){
      $productsCheck = mysql_fetch_array($check, MYSQL_ASSOC);
      $idCheck = $productsCheck['products_id'];
			  if($productsCheck['products_date_added']!='0000-00-00 00:00:00' && $productsCheck['products_date_added'] !='')
			  	$products_date_added = $productsCheck['products_date_added'];
	      $idCheck = $productsCheck['products_id'];
	      #mysql_query("Delete from products where products_id = '$idCheck'");
	      #mysql_query("Delete from products_description where products_id = '$idCheck'");
	      #mysql_query("Delete from products_to_categories where products_id = '$idCheck'");
	 }else{
	      mysql_query("Delete from products where products_id = '$idCheck'");
	      mysql_query("Delete from products_description where products_id = '$idCheck'");
	      #mysql_query("Delete from products_to_categories where products_id = '$idCheck'");
	 }
	      mysql_query("Delete from products_to_categories where products_id = '$idCheck'");
	      mysql_query("Delete from amazonOffers where id_amazon = '$idCheck' and (Platform = '' or Platform = 'Amazon')");
	      #mysql_query("Delete from amazonCreator where id_amazon = '$idCheck'");
	      




	 return array('id' => $idCheck, 'date' => $products_date_added);
}


function removeEmpty($pID){
	 $check = mysql_query("select products_id from products  where products_id > '$pID'  order by products_id  LIMIT 0, 500");
	 echo "select products_id from products  where products_id > '$pID'  order by products_id  LIMIT 0, 500<br>" ;
			$i=1;
			$idCheck = $pID   ;
			while($productsCheck = mysql_fetch_array($check, MYSQL_ASSOC)){
				$idCheck = $productsCheck['products_id'];
	      $checkTotal =mysql_query("Select COUNT( amazonOffers.id_amazon ) AS total from amazonOffers where id_amazon = '$idCheck' and amazonOffers.EurobuchID =0");
        $productsTotal = mysql_fetch_array($checkTotal, MYSQL_ASSOC);

				if($productsTotal['total']<1){
					echo      "$i: $idCheck | ";

					mysql_query("Delete from amazonOffers where id_amazon = '$idCheck'");
					mysql_query("Delete from products where products_id = '$idCheck'");
		      mysql_query("Delete from products_description where products_id = '$idCheck'");
		      mysql_query("Delete from products_to_categories where products_id = '$idCheck'");
					mysql_query("Delete from amazonCreator where id_amazon = '$idCheck'");
				}
				$i++;
			}
			return $idCheck   ;
}

function deleteTitel(){
	 $check = mysql_query("select products_id from products where TotalNew > 0");
	 echo mysql_num_rows($check) ;
	 if(mysql_num_rows($check)>0){
			while($productsCheck = mysql_fetch_array($check, MYSQL_ASSOC)){
				$idCheck = $productsCheck['products_id'];
				echo "$idCheck ";
				mysql_query("Delete from products where products_id = '$idCheck'");
	      mysql_query("Delete from products_description where products_id = '$idCheck'");
	      mysql_query("Delete from products_to_categories where products_id = '$idCheck'");
	      mysql_query("Delete from amazonOffers where id_amazon = '$idCheck'");
	      mysql_query("Delete from amazonCreator where id_amazon = '$idCheck'");
			}
	 }
}

function deleteOfferItems(){
	   $arrPlattforms = array("Amazon", "ZVAB.com", "Abebooks-Deutsch", "Buchfreund", "buch.de", "Booklooker", "Alibris", "Prolibri.de", "Antbo", "Biblioman", "Antiquario", "Guthschrift");
     for($i=1; $i < count($arrPlattforms); $i++){
       $plattform = $arrPlattforms[$i];
			 $check = mysql_query("SELECT DISTINCT id_amazon, count( id_amazon ) AS total
															FROM amazonOffers
															WHERE Platform = '$plattform'
															GROUP BY id_amazon
															ORDER BY `total` DESC");

				while($productsCheck = mysql_fetch_array($check, MYSQL_ASSOC)){
					$idCheck = $productsCheck['id_amazon'];
					$total = $productsCheck['total'];
					if($total>1){
					 $checkOffer = mysql_query("SELECT * FROM amazonOffers
																	WHERE id_amazon = '$idCheck'
																	and Platform = '$plattform'
																	ORDER BY oAmount");
						$o=0;
						while($offer = mysql_fetch_array($checkOffer, MYSQL_ASSOC)){
	           $idOffer = $offer['id'];
						 if($o>0)
						 		mysql_query("Delete from amazonOffers where id = '$idOffer'");
						    #mysql_query("Update amazonOffers set del = 1 where id = '$idOffer'");
	           $o++;
						}
					}
				}
     }


}



function get_cat_link($cID, $name='') {
	$cName = get_cleanName(strToLower($name));
	$link = '/cat/c'.$cID.'_'.$cName.'.html';
	return $link;
}

function get_prod_link($cID, $name='') {
	$cName = get_cleanName(strToLower($name));
	$link = '/pID/c'.$cID.'_'.$cName.'.html';
	return $link;
}

 function get_cleanName($name) {
 	$search_array=array('ä','Ä','ö','Ö','ü','Ü','&auml;','&Auml;','&ouml;','&Ouml;','&uuml;','&Uuml;');
 	$replace_array=array('ae','Ae','oe','Oe','ue','Ue','ae','Ae','oe','Oe','ue','Ue');
 	$name=str_replace($search_array,$replace_array,$name);

     $replace_param='/[^a-zA-Z0-9]/';
     $name=preg_replace($replace_param,'-',$name);
     return $name;
 }

 function get_cat_id($cName='') {
 	$cat = explode('_', $cName);
	$cID = $cat[0];
	$actual_cat_id = (int) str_replace('c', '', $cID);
	return $actual_cat_id;
}

function get_url($url='') {
  if(strlen($url)<8)$url="";
  if(substr($url, 0, 4)!="http" && $url!="")$url="http://".$url;
  return $url;
}


function date_mysql_transform($datestring){
  if($datestring=="0000-00-00 00:00:00")
    return "";
  if(strlen($datestring)>=10)
    return substr($datestring, 8, 2).".".substr($datestring, 5, 2).".".substr($datestring, 0, 4);
}


function aws_signed_request($region, $params, $public_key, $private_key, $debug = false)
{

/*Parameters:
    $region - the Amazon(r) region (ca,com,co.uk,de,fr,jp)
    $params - an array of parameters, eg. array("Operation"=>"ItemLookup",
                    "ItemId"=>"B000X9FLKM", "ResponseGroup"=>"Small")
    $public_key - your "Access Key ID"
    $private_key - your "Secret Access Key"*/


    // some paramters
    $method = "GET";
    $host = "ecs.amazonaws.".$region;
    $uri = "/onca/xml";

    // additional parameters
    $params["Service"] = "AWSECommerceService";
    $params["AWSAccessKeyId"] = $public_key;
    // GMT timestamp
    $params["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
    // API version
    $params["Version"] = "2009-03-31";
    $params["AssociateTag"] = "breviarium-21";

    // sort the parameters
    ksort($params);

    // create the canonicalized query
    $canonicalized_query = array();
    foreach ($params as $param=>$value)
    {
        $param = str_replace("%7E", "~", rawurlencode($param));
        $value = str_replace("%7E", "~", rawurlencode($value));
        $canonicalized_query[] = $param."=".$value;
    }
    $canonicalized_query = implode("&", $canonicalized_query);

    // create the string to sign
    $string_to_sign = $method."\n".$host."\n".$uri."\n".$canonicalized_query;

    // calculate HMAC with SHA256 and base64-encoding
    $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $private_key, True));

    // encode the signature for the request
    $signature = str_replace("%7E", "~", rawurlencode($signature));

    // create request
    $request = "http://".$host.$uri."?".$canonicalized_query."&Signature=".$signature;

		if($debug){
      echo "<a href='$request' target='_blank'>$request</a><br><br>"  ;
  		exit;
		}

    // do request
    $response = @file_get_contents($request);

    if ($response === False)
    {
        return False;
    }
    else
    {
        $pxml = simplexml_load_string($response);
        if ($pxml === False)
        {
            return False; // no xml
        }
        else
        {
            return $pxml;
        }
    }
}




function getCategory($ASIN){
	global $db_link;
	$arrReturn=array();
	$params = array("Operation"=>"ItemLookup",  "ItemId"=>$ASIN, 'IdType' => 'ASIN', "ResponseGroup"=>"Large") ; #   # Offers/Offer/OfferAttributes
	$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);
	 #var_dump($pxml);
	$Nodes=$pxml->Items->Item->BrowseNodes->BrowseNode;
	for($i=0; $i < count($Nodes); $i++){
		# echo "<br>".$Nodes[$i]->BrowseNodeId.': '.$Nodes[$i]->Name ;
		$result = mysql_query("select categories_id from categories where categories_id ='".$Nodes[$i]->BrowseNodeId."'", $db_link);
		if(mysql_num_rows($result) && !in_array($Nodes[$i]->BrowseNodeId, $arrReturn))
			 $arrReturn[]=$Nodes[$i]->BrowseNodeId;
	}
	# Einzelne Nodes in die Tiefe durchlaufen
	if(count($arrReturn)<1){
	 $Nodes=$pxml->Items->Item->BrowseNodes->BrowseNode[0]->Ancestors->BrowseNode;
  #echo "<br>".$Nodes[0]->BrowseNodeId.': '.$Nodes[0]->Name ;
	 $result = mysql_query("select categories_id from categories where categories_id ='".$Nodes[0]->BrowseNodeId."'", $db_link);
	 if(mysql_num_rows($result) && !in_array($Nodes[0]->BrowseNodeId, $arrReturn))
   	return array($Nodes[0]->BrowseNodeId);

	 $Nodes=$Nodes[0]->Ancestors->BrowseNode;
  # echo "<br>".$Nodes[0]->BrowseNodeId.': '.$Nodes[0]->Name ;
	 $result = mysql_query("select categories_id from categories where categories_id ='".$Nodes[0]->BrowseNodeId."'", $db_link);
	 if(mysql_num_rows($result) && !in_array($Nodes[0]->BrowseNodeId, $arrReturn))
   	return array($Nodes[0]->BrowseNodeId);

	 $Nodes=$Nodes[0]->Ancestors->BrowseNode;
   #echo "<br>".$Nodes[0]->BrowseNodeId.': '.$Nodes[0]->Name ;
	 $result = mysql_query("select categories_id from categories where categories_id ='".$Nodes[0]->BrowseNodeId."'", $db_link);
	 if(mysql_num_rows($result) && !in_array($Nodes[0]->BrowseNodeId, $arrReturn))
   	return array($Nodes[0]->BrowseNodeId);
   	
	 $Nodes=$Nodes[0]->Ancestors->BrowseNode;
   #echo "<br>".$Nodes[0]->BrowseNodeId.': '.$Nodes[0]->Name ;
	 $result = mysql_query("select categories_id from categories where categories_id ='".$Nodes[0]->BrowseNodeId."'", $db_link);
	 if(mysql_num_rows($result) && !in_array($Nodes[0]->BrowseNodeId, $arrReturn))
   	return array($Nodes[0]->BrowseNodeId);
   	
	 $Nodes=$Nodes[0]->Ancestors->BrowseNode;
   #echo "<br>".$Nodes[0]->BrowseNodeId.': '.$Nodes[0]->Name ;
	 $result = mysql_query("select categories_id from categories where categories_id ='".$Nodes[0]->BrowseNodeId."'", $db_link);
	 if(mysql_num_rows($result) && !in_array($Nodes[0]->BrowseNodeId, $arrReturn))
   	return array($Nodes[0]->BrowseNodeId);
   	
	 $Nodes=$Nodes[0]->Ancestors->BrowseNode;
   #echo "<br>".$Nodes[0]->BrowseNodeId.': '.$Nodes[0]->Name ;
	 $result = mysql_query("select categories_id from categories where categories_id ='".$Nodes[0]->BrowseNodeId."'", $db_link);
	 if(mysql_num_rows($result) && !in_array($Nodes[0]->BrowseNodeId, $arrReturn))
   	return array($Nodes[0]->BrowseNodeId);
   	return array('4185461');
	}
	return $arrReturn ;
}


function getDetailsFromASIN($ASIN, $MinimumPrice, $IdType = 'ISBN', $SearchIndex="Books"){
	global $db_link, $arrBinding;
	$success =false;
	$arrReturn=array();
	$params = array("Operation"=>"ItemLookup",  "ItemId"=>$ASIN, 'IdType' => $IdType, "Condition"=>"All","MerchantId"=>"All","ResponseGroup"=>"Large") ; #   # Offers/Offer/OfferAttributes
	if($SearchIndex!="")
	   $params["SearchIndex"]=$SearchIndex ;

	$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);

	#var_dump($pxml);
	#exit;
	
	$Nodes=$pxml->Items->Item->Offers->Offer;
	# var_dump($pxml->Items->TotalPages);
  	$TotalResults=$pxml->Items->TotalResults;
	$TotalPages=$pxml->Items->TotalPages;
	#echo "<br>$TotalResults : $TotalPages<br>";
	$i=0;
  foreach($pxml->Items->Item as $item) {
   #echo isset($item->Offers)." - ".$item->OfferSummary->TotalNew." - ".$item->LargeImage->URL." - ".(!in_array($item->ItemAttributes->Binding, $arrBinding))."<br>";
	 if(isset($item->Offers) && $item->OfferSummary->TotalNew < 1  && ( $item->OfferSummary->LowestUsedPrice->Amount > $MinimumPrice ||  $item->OfferSummary->LowestCollectiblePrice->Amount > $MinimumPrice) && $item->LargeImage->URL != ""  && !in_array($item->ItemAttributes->Binding, $arrBinding)){
   $LargeImage = $item->LargeImage->URL;
   $MediumImage = $item->MediumImage->URL;
   $SmallImage = $item->SmallImage->URL;

	 $OfferSummary = $item->OfferSummary;
	 $CurrencyCode = $OfferSummary->LowestUsedPrice->CurrencyCode;
	 $LowestNewPrice = str_replace(",", "", $OfferSummary->LowestNewPrice->Amount);
	 $LowestUsedPrice = str_replace(",", "", $OfferSummary->LowestUsedPrice->Amount);
	 $LowestCollectiblePrice = str_replace(",", "", $OfferSummary->LowestCollectiblePrice->Amount);
   $LowestNewPrice = number_format($LowestNewPrice/100, 2);
   $LowestUsedPrice = number_format($LowestUsedPrice/100, 2);
   $LowestCollectiblePrice = number_format($LowestCollectiblePrice/100, 2);
   $TotalNew = $OfferSummary->TotalNew;
	 $TotalUsed = $OfferSummary->TotalUsed;
	 $TotalCollectible = $OfferSummary->TotalCollectible;
   $DetailPageURL  = $item->DetailPageURL;
   $Content = $item->EditorialReviews->EditorialReview->Content;
   $Content = str_replace("'", "\'", $Content);
	 $Source = $item->EditorialReviews->EditorialReview->Source;
	 $Source = str_replace("'", "\'", $Source);
	 $ASIN  = $item->ASIN;
	 $ItemAttributes = $item->ItemAttributes;
	 $ListPrice = number_format($ItemAttributes->ListPrice->Amount/100, 2);


	 $Title = $ItemAttributes->Title;
	 $Author = $ItemAttributes->Author;
	 $Publisher = $ItemAttributes->Publisher;
   if($Publisher=="siehe Beschreibung")
      $Publisher="";
	 $PublicationDate = $ItemAttributes->PublicationDate;
	 $NumberOfPages = $ItemAttributes->NumberOfPages;
	 $ISBN = $ItemAttributes->ISBN;
	 $EAN = $ItemAttributes->EAN;
	 $Edition = $ItemAttributes->Edition;
	 $Binding = $ItemAttributes->Binding;
	 $arrCheck = checkTitel($ASIN);
	 $id_amazon = $arrCheck['id'];
	 $products_date_added = $arrCheck['date'];

	 $arrReturn[$i] = array('TotalCollectible' => $TotalCollectible , 'TotalUsed' => $TotalUsed , 'CurrencyCode' => $CurrencyCode , 'ListPrice' => $ListPrice , 'LowestNewPrice' => $LowestNewPrice , 'LowestUsedPrice' => $LowestUsedPrice , 'LowestCollectiblePrice' => $LowestCollectiblePrice , 'LargeImage' => $LargeImage, 'MediumImage' => $MediumImage, 'SmallImage' => $SmallImage, 'ASIN' => $ASIN, 'Title' => $Title, 'ISBN' => $ISBN, 'EAN' => $EAN, 'Binding' => $Binding, 'Publisher' => $Publisher, 'PublicationDate' => $PublicationDate, 'NumberOfPages' => $NumberOfPages);
   $arrReturn[$i]['Creator'] = array();

   $products_price = ( ($LowestCollectiblePrice==0) || ($LowestUsedPrice>0&&$LowestUsedPrice<$LowestCollectiblePrice) ?$LowestUsedPrice:$LowestCollectiblePrice);
		if($id_amazon){
      $SQL = "Update products set
							products_quantity =1 ,
							products_status = 1,
							products_tax_class_id = 2,
							products_ean = '".str_replace("'", "\'", ($EAN))."',
							Author ='".str_replace("'", "\'", ($Author))."' ,
							Title = '". str_replace("'", "\'", ($Title))."',
							Publisher = '".str_replace("'", "\'", ($Publisher))."',
							NumberOfPages = '".str_replace("'", "\'", ($NumberOfPages))."',
							Edition = '".str_replace("'", "\'", ($Edition))."',
							PublicationDate = '".str_replace("'", "\'", ($PublicationDate))."',
							ISBN = '".str_replace("'", "\'", ($ISBN))."',
							EAN = '".str_replace("'", "\'", ($EAN))."',
							Binding = '".str_replace("'", "\'", ($Binding))."',
							CurrencyCode = '".$CurrencyCode."',
							products_price = '".$products_price."',
							ListPrice = '".$ListPrice."',
							LowestNewPrice = '".$LowestNewPrice."',
							LowestUsedprice = 	'".$LowestUsedPrice."',
							LowestCollectiblePrice = '".$LowestCollectiblePrice."',
							TotalUsed = '".$TotalUsed."',
							TotalNew = '".$TotalNew."',
							DetailPageURL  = '".$DetailPageURL."',
							EditorialSource = '$Source',
							EditorialReview = '$Content',
							TotalCollectible = '".$TotalCollectible."',
							SmallImage = '".$SmallImage."',
							MediumImage = '".$MediumImage."',
							LargeImage = '".$LargeImage."',
							products_date_added = '$products_date_added',
							products_last_modified = Now()
							where products_id = '$id_amazon'";
							mysql_query($SQL)  or die($SQL."<br>".mysql_error());
							$SQL = "Update products_description set products_name = '".str_replace("'", "\'", ($Title))."' where products_id = '$id_amazon'";
              #echo "$SQL<br>";
							 mysql_query($SQL)  or die("$SQL<br>".mysql_error());
		}else{
			$SQL = "Insert into products (
							products_quantity,
							DetailPageURL,
							EditorialSource,
							EditorialReview,
							products_status,
							products_tax_class_id,
							products_ean,
							ASIN,
							Author,
							Title,
							Publisher,
							NumberOfPages,
							Edition,
							PublicationDate,
							ISBN,
							EAN,
							Binding,
							CurrencyCode,
							products_price,
							ListPrice,
							LowestNewPrice,
							LowestUsedprice,
							LowestCollectiblePrice,
							TotalUsed,
							TotalNew,
							TotalCollectible,
							SmallImage,
							MediumImage,
							LargeImage,
							products_date_added,
							products_last_modified
			) VALUES (
							1,
							'".$DetailPageURL."',
							'".$Source."',
							'".$Content."',
							1,
							2,
							'".str_replace("'", "\'", ($EAN))."',
							'".$ASIN."',
							'".str_replace("'", "\'", ($Author))."',
							'". str_replace("'", "\'", ($Title))."',
							'".str_replace("'", "\'", ($Publisher))."',
							'".str_replace("'", "\'", ($NumberOfPages))."',
							'".str_replace("'", "\'", ($Edition))."',
							'".str_replace("'", "\'", ($PublicationDate))."',
							'".str_replace("'", "\'", ($ISBN))."',
							'".str_replace("'", "\'", ($EAN))."',
							'".str_replace("'", "\'", ($Binding))."',
							'".$CurrencyCode."',
							'".$products_price."',
							'".$ListPrice."',
							'".$LowestNewPrice."',
							'".$LowestUsedPrice."',
							'".$LowestCollectiblePrice."',
							'".$TotalUsed."',
							'".$TotalNew."',
							'".$TotalCollectible."',
							'".$SmallImage."',
							'".$MediumImage."',
							'".$LargeImage."',
							'".$products_date_added."',
	        			 NOW()
			);";
			#echo "$SQL<br>";
			 mysql_query($SQL)  or die($SQL."<br>".mysql_error());
      $id_amazon = mysql_insert_id();
      $SQL = "Insert into products_description ( products_id, 	language_id, 	products_name ) values($id_amazon, 2, '".str_replace("'", "\'", ($Title))."')";
  	  mysql_query($SQL) or die("$SQL<br>".mysql_error());
			 
			 foreach( $ItemAttributes->Creator as $Creator) {
			 	$arrReturn[$i]['Creator'][]=array('Role' => $Creator['Role'], 'Creator' => $Creator);
			  $SQL = "INSERT INTO amazonCreator (creator, role, id_amazon) VALUES('". str_replace("'", "\'", ($Creator))."', '". str_replace("'", "\'", ($Creator['Role']))."', $id_amazon)";
				mysql_query($SQL) or die($SQL."<br>".mysql_error()); # or die($SQL."<br>".mysql_error());
			 }
			 
			 
			 
		}
		#	$SQL = "Insert into products_to_categories (products_id, categories_id) values($id_amazon, $categories_id)";
		# echo "$SQL<br>";
		#   mysql_query($SQL)  or die("$SQL<br>".mysql_error());

					# echo $arrCheck['id'] ." - $id_amazon<br>";


				   $arrReturn[$i]['Offers'] = array();
				   foreach($item->Offers->Offer as $Offer) {
				         $MerchantId = $Offer->Merchant->MerchantId;
				         $OfferListingId = $Offer->OfferListing->OfferListingId;
				         $ExchangeId = $Offer->OfferListing->ExchangeId;
				         $SubCondition = $Offer->OfferAttributes->SubCondition;
				         $ConditionNote = $Offer->OfferAttributes->ConditionNote;
				         $oAmount = $Offer->OfferListing->Price->Amount;
	 							 $oAmount = str_replace(",", "", $oAmount/100);
				         $oAmount = number_format($oAmount, 2);
				         $oCurrencyCode = $Offer->OfferListing->Price->CurrencyCode;
				        # echo "$MerchantId $SubCondition $oAmount $oCurrencyCode<br>";
				         $arrReturn[$i]['Offers'][]=array('oAmount' => $oAmount, 'oCurrencyCode' => $oCurrencyCode, 'AddToCartUrl' => "http://www.amazon.de/gp/aws/cart/add.html?&AWSAccessKeyId=[AWS_KEY]&ASIN.1=$ASIN&Quantity.1=1&SellerId.1=$MerchantId", 'MerchantId' => $MerchantId, 'SubCondition' => $SubCondition, 'ConditionNote' => $ConditionNote);
				         $SQL = "INSERT INTO amazonOffers (id_amazon, date_added, MerchantId, Platform, OfferListingId, ExchangeId, oAmount, versandkosten_eur,  oCurrencyCode, SubCondition, ConditionNote, AddToCartUrl, picurl, year)  VALUES($id_amazon, now(), '".$MerchantId."', 'Amazon', '".$OfferListingId."', '".$ExchangeId."', '".$oAmount."', '3.00', '".$oCurrencyCode."', '". str_replace("'", "\'", ($SubCondition))."', '".str_replace("'", "\'", ($ConditionNote))."', 'www.amazon.de/gp/aws/cart/add.html?&AWSAccessKeyId=[AWS_KEY]&ASIN.1=$ASIN&Quantity.1=1&SellerId.1=$MerchantId', '".$MediumImage."', '".substr($PublicationDate, 0, 4)."')";
                 # echo "$SQL<br>";
								  mysql_query($SQL) or die($SQL."<br>".mysql_error()); # ; # or die($SQL."<br>".mysql_error());
					 }
   $i++;
   $success=true;
    }
  }
	return $success;
}

function getOffersFromASIN($ASIN){
	global $db_link;
	$arrReturn=array();
	$params = array("Operation"=>"ItemSearch",  "Keywords"=>$ASIN, "Condition"=>"All","MerchantId"=>"All","ResponseGroup"=>"Offers","SearchIndex"=>"Books") ; #   # Offers/Offer/OfferAttributes
	$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);
	$Nodes=$pxml->Items->Item->Offers->Offer;
	for($i=0; $i < count($Nodes); $i++){
    $Price = number_format($Nodes[$i]->OfferListing->Price->Amount/100, 2);
	  $SubCondition= $Nodes[$i]->OfferAttributes->SubCondition;
	  $ConditionNote= $Nodes[$i]->OfferAttributes->ConditionNote;
	  $MerchantId= $Nodes[$i]->Merchant->MerchantId;
	  $arrReturn[] = array('Price' => $Price,'AddToCartUrl' => "http://www.amazon.de/gp/aws/cart/add.html?&AWSAccessKeyId=[AWS_KEY]&ASIN.1=$ASIN&Quantity.1=1&SellerId.1=$MerchantId", 'MerchantId' => $MerchantId, 'SubCondition' => $SubCondition, 'ConditionNote' => $ConditionNote);
	}
	return $arrReturn;
}


function getOffersFromPriceAndNode($MinimumPrice, $MaximumPrice, $categories_id="", $ItemPage = 1, $Titel = "*", $file='temp.php', $tblAdd='', $lang = 'de', $debug=false){
	global $db_link, $arrBinding, $limit, $page, $c, $arrAwsKeys, $key, $url, $chapter, $STEP;

	$TotalPages =0;
	$MinimumPrice=$MinimumPrice*100;
	$MaximumPrice=$MaximumPrice*100;
	$arrReturn=array();     #
	if($Titel == "*"){
    $params = array("ItemPage"=> $ItemPage,"Operation"=>"ItemSearch", "BrowseNode" => $categories_id,  "Title"=>$Titel,  "MinimumPrice"=>$MinimumPrice,  "MaximumPrice"=>$MaximumPrice, "Sort" => "pricerank", "Condition"=>"All","MerchantId"=>"All","ResponseGroup"=>"Large","SearchIndex"=>"Books") ; #   # Offers/Offer/OfferAttributes
	}else{
    $params = array("ItemPage"=> $ItemPage,"Operation"=>"ItemSearch", "Title"=>$Titel, "Condition"=>"All","MerchantId"=>"All","ResponseGroup"=>"Large","SearchIndex"=>"Books") ; #   # Offers/Offer/OfferAttributes  inverse-
	}
	$pxml = aws_signed_request($lang, $params, AWS_KEY, AWS_SECRET_KEY, $debug);


	if(!$pxml){
		# Limit überschritten
		$key++;
		#echo  " $key ";
    if($key>=count($arrAwsKeys))
    	$key = 0;
		if($c<10){
		 echo  count($arrAwsKeys). " - $c Limited exceeded! ";
		 exit;
		}
		header("Location: http://buch.breviarium.de/hidden/$file?&chapter=$chapter&STEP=$STEP&k=$key&c=0&limit=$limit&page=".($page)."&MinimumPrice=".(round($MinimumPrice/100)));
	}

	$TotalResults=$pxml->Items->TotalResults;
	$TotalPages=$pxml->Items->TotalPages;


	 if($tblAdd!='' && $tblAdd!='0'){
		 $sqlData = "Insert into categories_data".$tblAdd." (categories_id,  totalResults, ItemPage , TotalPages,   minimumPrice,  maximumPrice, last_update, url) values('$categories_id',  '$TotalResults',  '$ItemPage',  '$TotalPages',     $MinimumPrice/100,  $MaximumPrice/100, now(), '$url')";
	   mysql_query( $sqlData ) or die($sqlData. mysql_error());
	 }

	 if($TotalPages<1)
	  return 1;
	
	 if($tblAdd!='' && $tblAdd!='0'){
	 	 $sqlData = "Insert into categories_data (categories_id,  totalResults, ItemPage , TotalPages,   minimumPrice,  maximumPrice, last_update, url) values('$categories_id',  '$TotalResults',  '$ItemPage',  '$TotalPages',     $MinimumPrice/100,  $MaximumPrice/100, now(), '$url')";
	   mysql_query( $sqlData ) or die($sqlData. mysql_error());
	 }
	
   #if($MaximumPrice-$MinimumPrice==999)
 	 #return $TotalResults;
	
	# echo "<br>$TotalResults : $TotalPages<br>";
	$i=0;
	 #echo "\$MinimumPrice: $MinimumPrice<br>";
  foreach($pxml->Items->Item as $item) {
		#
  # echo $item->ItemAttributes->Title .":".  isset($item->Offers) .":". $item->OfferSummary->TotalNew  .":".  $item->LargeImage->URL  .":".    $item->OfferSummary->LowestUsedPrice->Amount .":".   $item->OfferSummary->LowestCollectiblePrice->Amount .":".  $MinimumPrice  .":".   in_array($item->ItemAttributes->Binding, $arrBinding) ."<br>";
	 if(isset($item->Offers) && $item->OfferSummary->TotalNew < 1 && $item->LargeImage->URL != "" && ( $item->OfferSummary->LowestUsedPrice->Amount >= $MinimumPrice ||  $item->OfferSummary->LowestCollectiblePrice->Amount >= $MinimumPrice) && !in_array($item->ItemAttributes->Binding, $arrBinding)){
   $LargeImage = $item->LargeImage->URL;
   $MediumImage = $item->MediumImage->URL;
   $SmallImage = $item->SmallImage->URL;

	 $OfferSummary = $item->OfferSummary;
	 $CurrencyCode = $OfferSummary->LowestUsedPrice->CurrencyCode;
	 $LowestNewPrice = str_replace(",", "", $OfferSummary->LowestNewPrice->Amount);
	 $LowestUsedPrice = str_replace(",", "", $OfferSummary->LowestUsedPrice->Amount);
	 $LowestCollectiblePrice = str_replace(",", "", $OfferSummary->LowestCollectiblePrice->Amount);
   $LowestNewPrice = number_format($LowestNewPrice/100, 2);
   $LowestUsedPrice = number_format($LowestUsedPrice/100, 2);
   $LowestCollectiblePrice = number_format($LowestCollectiblePrice/100, 2);
   $TotalNew = $OfferSummary->TotalNew;
	 $TotalUsed = $OfferSummary->TotalUsed;
	 $TotalCollectible = $OfferSummary->TotalCollectible;
   $DetailPageURL  = $item->DetailPageURL;
   $Content = $item->EditorialReviews->EditorialReview->Content;
   $Content = str_replace("'", "\'", $Content);
	 $Source = $item->EditorialReviews->EditorialReview->Source;
	 $Source = str_replace("'", "\'", $Source);
	 $ASIN  = $item->ASIN;
	 $ItemAttributes = $item->ItemAttributes;
	 $ListPrice = number_format($ItemAttributes->ListPrice->Amount/100, 2);


	 $Title = $ItemAttributes->Title;
	 $Author = $ItemAttributes->Author;
	 if(in_array($Author, $blacklist_authors))
	    $Author = "";
	 $Publisher = $ItemAttributes->Publisher;
    if($Publisher=="siehe Beschreibung")
    $Publisher="";
	 $PublicationDate = $ItemAttributes->PublicationDate;
	 $NumberOfPages = $ItemAttributes->NumberOfPages;
	 $ISBN = $ItemAttributes->ISBN;
	 $EAN = $ItemAttributes->EAN;
	 $Edition = $ItemAttributes->Edition;
	 $Binding = $ItemAttributes->Binding;
	 $arrCheck = checkTitel($ASIN);
	 $id_amazon = $arrCheck['id'];
	 $products_date_added = $arrCheck['date'];

	 #echo "<br><image src='$LargeImage'>";
   #
	 #echo "<br>$ASIN: ($TotalUsed / $TotalCollectible) $LowestUsedPrice / $LowestCollectiblePrice $CurrencyCode - $Title<br>$LargeImage<br>";
	# echo "$ASIN:  ($LowestUsedPrice>0 && $LowestUsedPrice < $LowestCollectiblePrice && $LowestCollectiblePrice > 0 ? $LowestUsedPrice : $LowestCollectiblePrice)<br>";
#				 echo    ( ($LowestCollectiblePrice==0) || ($LowestUsedPrice>0&&$LowestUsedPrice<$LowestCollectiblePrice) ?$LowestUsedPrice:$LowestCollectiblePrice)    ;
#		exit;
	 $arrReturn[$i] = array('TotalCollectible' => $TotalCollectible , 'TotalUsed' => $TotalUsed , 'CurrencyCode' => $CurrencyCode , 'ListPrice' => $ListPrice , 'LowestNewPrice' => $LowestNewPrice , 'LowestUsedPrice' => $LowestUsedPrice , 'LowestCollectiblePrice' => $LowestCollectiblePrice , 'LargeImage' => $LargeImage, 'MediumImage' => $MediumImage, 'SmallImage' => $SmallImage, 'ASIN' => $ASIN, 'Title' => $Title, 'ISBN' => $ISBN, 'EAN' => $EAN, 'Binding' => $Binding, 'Publisher' => $Publisher, 'PublicationDate' => $PublicationDate, 'NumberOfPages' => $NumberOfPages);
   $arrReturn[$i]['Creator'] = array();
   
   $products_price = ( ($LowestCollectiblePrice==0) || ($LowestUsedPrice>0&&$LowestUsedPrice<$LowestCollectiblePrice) ?$LowestUsedPrice:$LowestCollectiblePrice);

    #echo "\$id_amazon: $id_amazon<br>";

		if($id_amazon){
		   #echo "<br>$ASIN: $products_price  - UPDATE: $id_amazon";
      $SQL = "Update products set
							products_quantity =1 ,
							products_status = 1,
							products_tax_class_id = 2,
							products_ean = '".str_replace("'", "\'", ($EAN))."',
							Author ='".str_replace("'", "\'", ($Author))."' ,
							Title = '". str_replace("'", "\'", ($Title))."',
							Publisher = '".str_replace("'", "\'", ($Publisher))."',
							NumberOfPages = '".str_replace("'", "\'", ($NumberOfPages))."',
							Edition = '".str_replace("'", "\'", ($Edition))."',
							PublicationDate = '".str_replace("'", "\'", ($PublicationDate))."',
							ISBN = '".str_replace("'", "\'", ($ISBN))."',
							EAN = '".str_replace("'", "\'", ($EAN))."',
							Binding = '".str_replace("'", "\'", ($Binding))."',
							CurrencyCode = '".$CurrencyCode."',
							products_price = '".$products_price."',
							DetailPageURL = '".$DetailPageURL."',
							EditorialSource = '$Source',
							EditorialReview = '$Content',
							ListPrice = '".$ListPrice."',
							LowestNewPrice = '".$LowestNewPrice."',
							LowestUsedprice = 	'".$LowestUsedPrice."',
							LowestCollectiblePrice = '".$LowestCollectiblePrice."',
							TotalUsed = '".$TotalUsed."',
							TotalNew = '".$TotalNew."',
							TotalCollectible = '".$TotalCollectible."',
							SmallImage = '".$SmallImage."',
							MediumImage = '".$MediumImage."',
							LargeImage = '".$LargeImage."',
							products_date_added = '$products_date_added',
							products_last_modified = Now()
							where products_id = '$id_amazon'";
							
						#	echo "<br>$SQL";
							
							mysql_query($SQL)  or die($SQL."<br>".mysql_error());
							$SQL = "Update products_description set products_name = '".str_replace("'", "\'", ($Title))."' where products_id = '$id_amazon'";
							mysql_query($SQL)  or die("$SQL<br>".mysql_error());
		}else{

			$SQL = "Insert into products (
							products_quantity,
							products_status,
							products_tax_class_id,
							products_ean,
							ASIN,
							Author,
							Title,
							Publisher,
							NumberOfPages,
							Edition,
							PublicationDate,
							ISBN,
							EAN,
							DetailPageURL,
							EditorialSource,
							EditorialReview,
							Binding,
							CurrencyCode,
							products_price,
							ListPrice,
							LowestNewPrice,
							LowestUsedprice,
							LowestCollectiblePrice,
							TotalUsed,
							TotalNew,
							TotalCollectible,
							SmallImage,
							MediumImage,
							LargeImage,
							products_date_added,
							products_last_modified
			) VALUES (
							1,
							1,
							2,
							'".str_replace("'", "\'", ($EAN))."',
							'".$ASIN."',
							'".str_replace("'", "\'", ($Author))."',
							'". str_replace("'", "\'", ($Title))."',
							'".str_replace("'", "\'", ($Publisher))."',
							'".str_replace("'", "\'", ($NumberOfPages))."',
							'".str_replace("'", "\'", ($Edition))."',
							'".str_replace("'", "\'", ($PublicationDate))."',
							'".str_replace("'", "\'", ($ISBN))."',
							'".str_replace("'", "\'", ($EAN))."',
							'".$DetailPageURL."',
							'".$Source."',
							'".$Content."',
							'".str_replace("'", "\'", ($Binding))."',
							'".$CurrencyCode."',
							'".$products_price."',
							'".$ListPrice."',
							'".$LowestNewPrice."',
							'".$LowestUsedPrice."',
							'".$LowestCollectiblePrice."',
							'".$TotalUsed."',
							'".$TotalNew."',
							'".$TotalCollectible."',
							'".$SmallImage."',
							'".$MediumImage."',
							'".$LargeImage."',
							'".$products_date_added."',
	        			 NOW()
			);";
		#	echo "<br>$SQL";
			mysql_query($SQL)  or die($SQL."<br>".mysql_error());
			$newItem=true;
      $id_amazon = mysql_insert_id();
      # echo "<br>$ASIN: $products_price  - INSERT: $id_amazon";
      $SQL = "Insert into products_description ( products_id, 	language_id, 	products_name ) values($id_amazon, 2, '".str_replace("'", "\'", ($Title))."')";
      mysql_query($SQL) or die("$SQL<br>".mysql_error());
      
			 #echo $arrCheck['id'] ." - $id_amazon<br>";
			 foreach( $ItemAttributes->Creator as $Creator) {
			  # echo "<br>".$Creator['Role'].": ".$Creator;
			 	$arrReturn[$i]['Creator'][]=array('Role' => $Creator['Role'], 'Creator' => $Creator);
			  $SQL = "INSERT INTO amazonCreator (creator, role, id_amazon) VALUES('". str_replace("'", "\'", ($Creator))."', '". str_replace("'", "\'", ($Creator['Role']))."', $id_amazon)";
	      mysql_query($SQL) or die($SQL."<br>".mysql_error()); # or die($SQL."<br>".mysql_error());
			 }
      
      
      
      
		}
		if($categories_id>0){
		 #echo "<br>$categories_id";
			$SQL = "Insert into products_to_categories (products_id, categories_id) values($id_amazon, $categories_id)";
	    mysql_query($SQL)  or die("$SQL<br>".mysql_error());
		}


	#		var_dump($item->Offers->Offer);

				   $arrReturn[$i]['Offers'] = array();
				   foreach($item->Offers->Offer as $Offer) {
				         $MerchantId = $Offer->Merchant->MerchantId;
				         $OfferListingId = $Offer->OfferListing->OfferListingId;
				         $ExchangeId = $Offer->OfferListing->ExchangeId;
				         $SubCondition = $Offer->OfferAttributes->SubCondition;
				         $ConditionNote = $Offer->OfferAttributes->ConditionNote;
				         $oAmount = $Offer->OfferListing->Price->Amount;
	 							 $oAmount = str_replace(",", "", $oAmount/100);
				         $oAmount = number_format($oAmount, 2);
				         $oCurrencyCode = $Offer->OfferListing->Price->CurrencyCode;
				         #echo "<br><b>$MerchantId $SubCondition $oAmount $oCurrencyCode</b>";
				         $arrReturn[$i]['Offers'][]=array('oAmount' => $oAmount, 'oCurrencyCode' => $oCurrencyCode, 'AddToCartUrl' => "http://www.amazon.de/gp/aws/cart/add.html?&AWSAccessKeyId=[AWS_KEY]&ASIN.1=$ASIN&Quantity.1=1&SellerId.1=$MerchantId", 'MerchantId' => $MerchantId, 'SubCondition' => $SubCondition, 'ConditionNote' => $ConditionNote);
				         $SQL = "INSERT INTO amazonOffers (id_amazon, date_added, MerchantId, Platform, OfferListingId, ExchangeId, oAmount, versandkosten_eur,  oCurrencyCode, SubCondition, ConditionNote, AddToCartUrl, picurl, year)  VALUES($id_amazon, now(), '".$MerchantId."', 'Amazon', '".$OfferListingId."', '".$ExchangeId."', '".$oAmount."', '3.00', '".$oCurrencyCode."', '". str_replace("'", "\'", ($SubCondition))."', '".str_replace("'", "\'", ($ConditionNote))."', 'www.amazon.de/gp/aws/cart/add.html?&AWSAccessKeyId=[AWS_KEY]&ASIN.1=$ASIN&Quantity.1=1&SellerId.1=$MerchantId', '".$MediumImage."', '".substr($PublicationDate, 0, 4)."')";
								# if($newItem)
								# echo "<br>$SQL";
								 mysql_query($SQL) or die($SQL."<br>".mysql_error()); # ; # or die($SQL."<br>".mysql_error());
 }
				  #if($SmallImage!="")
				 # 	copy($SmallImage, '../images//product_images/thumbnail_images/' . $id_amazon."_0.jpg");
				 # if($LargeImage!="")
				 # 	copy($LargeImage, '../images//product_images/popup_images/' . $id_amazon."_0.jpg");



   $i++;
    }
  }
  #if($newItem)
 # exit;
	return $TotalPages;
}



# EUROBUCH

function xml_startElement($parser, $name, $attrs)
{   global $eISBN, $eTITLE, $eID, $cPrice;

	if($name=="RESULTLIST") {
	} else if($name=="BOOK") {
			if($eISBN == '' && strrpos($attrs["TITLE"], $eTITLE)===false){
			}elseif($eISBN != '' && $attrs["PLATFORM"] != '' && substr($attrs["PLATFORM"], 0, 6) != "Amazon" && substr($attrs["PLATFORM"], 0, 4) != "Ebay"){
				$SQL = "Delete from amazonOffers where id_amazon = '$eID' and Platform = '". $attrs["PLATFORM"] ."' and (AddToCartUrl = '". $attrs["URL"] ."' || oAmount > ". $cPrice . ")";
        #echo "<br>";
				#echo $attrs["PLATFORM"].": Delete from amazonOffers..<br>";
				#echo "$eISBN : $eID - ".$attrs["PRICEEUR"]." ($cPrice)<br>";
        mysql_query($SQL) or die($SQL."<br>".mysql_error());
				if($attrs["PRICEEUR"] <= $cPrice){
          $SQL = "Insert into amazonOffers (id_amazon, date_added, EurobuchID, Platform, oAmount, ConditionNote, AddToCartUrl, year, versandkosten_eur, versandkosten_bem, picurl) values(".$eID.", now(), '". $attrs["ID"] ."', '". $attrs["PLATFORM"] ."', ". $attrs["PRICEEUR"] .", '". str_replace("'", "`", $attrs["COMMENT"]) ."', '". $attrs["URL"] ."', '". $attrs["YEAR"] ."', '". $attrs["VERSANDKOSTEN_EUR"] ."', '".$attrs["VERSANDKOSTEN_BEM"]."', '"  .$attrs["PICURL"]."')";
          #echo "Insert into amazonOffers..<br>";
				  mysql_query($SQL) or die($SQL."<br>".mysql_error());
				}
			}
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
		return true;
	}
	xml_parser_free($xml_parser);
	fclose($fp);
	return(false);
}

function addEurobuchListings($LIM){

	global $eISBN, $eTITLE, $eID, $cPrice;
   #echo  "SELECT * FROM products where ISBN != '' and products_id > 0 ORDER BY products_id LIMIT $LIM, 25<br>";
	 $check = mysql_query("SELECT * FROM products where ISBN != '' and products_id > 0 ORDER BY products_id LIMIT $LIM, 25") or die(mysql_error());
	 while($productsCheck = mysql_fetch_array($check, MYSQL_ASSOC)){
         $eISBN   =  $productsCheck['ISBN'];
         $eTITLE   =  $productsCheck['Title'];
         $eID   =  $productsCheck['products_id'];
         $LowestUsedprice   =  $productsCheck['LowestUsedprice'];
         $LowestCollectiblePrice  =  $productsCheck['LowestCollectiblePrice'];
         ($LowestUsedprice<$LowestCollectiblePrice&&$LowestUsedprice>0)||$LowestCollectiblePrice==0?$cPrice=$LowestUsedprice:$cPrice=$LowestCollectiblePrice;
				 #echo "http://www.eurobuch.com/extreq/meta/extquery.php?platform=test&password=test&clientip=77.188.23.234&format=xml8&order=price&mediatype=0&isbn=$eISBN<br>";
         $ret = parseURL("http://www.eurobuch.com/extreq/meta/extquery.php?platform=48773&password=QnoOpE02&clientip=".$_SERVER['REMOTE_ADDR']."&format=xml8&order=price&mediatype=0&special=1&doAbe=0&doAbeDe=1&doAlibris=1&doAlphamusic=0&doAmazon=0&doAmazonUk=0&doAmazonCom=0&doAmazon=0&doAmazonFr=0&doAntbo=0&doAntikbuch24=0&doProlibri=0&doAntiquario=0&doAudibile=0&doBiblio=0&doBiblioman=0&doBooklooker=1&doBUCH=1&doBuch24=0&doBuchfreund=1&doEBay=0&doEBS=0&doGuth=0&doHit=0&doJokers=0&doLibri=0&doAum=0&doZeilenreich=0&doZVAB=1&isbn=".$eISBN);
	 }
}



#if($eISBN!=''){
 #$ret = parseURL("http://www.eurobuch.com/extreq/meta/extquery.php?platform=test&password=test&clientip=77.188.23.234&format=xml8&order=price&mediatype=0&isbn=".$eISBN);
#}else{
 #$ret = parseURL("http://www.eurobuch.com/extreq/meta/extquery.php?platform=test&password=test&clientip=77.188.23.234&format=xml8&order=price&mediatype=0&author=".str_replace(" ", "%20", $product->data['Author'])."&isbn=&search=&author=&title=".str_replace(" ", "%20", $product->data['products_name']));
#}
?>