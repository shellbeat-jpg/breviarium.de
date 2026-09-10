<?php
/*
$RequestCartCreate = "http://ecs.amazonaws.de/onca/xml?
Service=AWSECommerceService&
AWSAccessKeyId=".AWS_KEY."&AssociateTag=".AWS_ASSOC_ID."&
Operation=CartCreate&
Item.1.OfferListingId=B000062TU1&
Item.1.Quantity=2
&Timestamp=[YYYY-MM-DDThh:mm:ssZ]
&Signature=[Request Signature]"

	$params = array("Operation"=>"CartCreate",  "Item.1.OfferListingId"=>"B000062TU1&","Item.1.Quantity"=>"1") ;
	$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY)
*/
function setOffersFromASIN($ASIN, $products_id){
	global $db_link;
		$params = array("Operation"=>"ItemSearch",  "Keywords"=>$ASIN, "Condition"=>"All","MerchantId"=>"All","ResponseGroup"=>"Large","SearchIndex"=>"Books") ; #   # Offers/Offer/OfferAttributes
		$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);
		if(!$pxml){

        return false;
		}else{
		   mysql_query("delete from amazonOffers where id_amazon  = '$products_id' and Platform = 'Amazon'") or die(mysql_error());

			 $SQL = "Update products set
				products_quantity = 0 ,
				products_last_modified = Now(),
				date_checked = '".date("Y-m-d")."'
				where products_id = '$products_id'";
			  mysql_query($SQL)  or die("$SQL<br>".mysql_error());

			 $offers=false;
			 $item = $pxml->Items->Item;

			 if(!$item)
           return false;

			 $ItemAttributes = $item->ItemAttributes;
       $OfferSummary = $item->OfferSummary;

 			 if(!$ItemAttributes)
			     return false;

			 $offers=false;
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
						 $SQL = "INSERT INTO amazonOffers (id_amazon, date_added, MerchantId, Platform, OfferListingId, ExchangeId, oAmount, versandkosten_eur,  oCurrencyCode, SubCondition, ConditionNote, AddToCartUrl, picurl, year)  VALUES($products_id, now(), '".$MerchantId."', 'Amazon', '".$OfferListingId."', '".$ExchangeId."', '".$oAmount."', '3.00', '".$oCurrencyCode."', '". str_replace("'", "\'", ($SubCondition))."', '".str_replace("'", "\'", ($ConditionNote))."', 'www.amazon.de/gp/aws/cart/add.html?&AWSAccessKeyId=[AWS_KEY]&ASIN.1=$ASIN&Quantity.1=1&SellerId.1=$MerchantId', '".$MediumImage."', '".substr($PublicationDate, 0, 4)."')";
						 mysql_query($SQL) or die($SQL."<br>".mysql_error()); # ; # or die($SQL."<br>".mysql_error());
						 $offers=true;
			 }
			 

			 
 			 if($offers){
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
		              products_quantity = 1 ,
									CurrencyCode = '".$CurrencyCode."',
									products_price = '".$products_price."',
									ListPrice = '".$ListPrice."',
									LowestNewPrice = '".$LowestNewPrice."',
									LowestUsedprice = 	'".$LowestUsedPrice."',
									LowestCollectiblePrice = '".$LowestCollectiblePrice."',
									TotalUsed = '".$TotalUsed."',
									TotalNew = '".$TotalNew."',
									TotalCollectible = '".$TotalCollectible."',
									date_checked = '".date("Y-m-d")."',
									products_last_modified = Now()
									where products_id = '$products_id'";
					mysql_query($SQL)  or die("$SQL<br>".mysql_error());
			 }

			 
			 
			 
			 
       return $offers*1;
	 }
}

function getRoleCreator($ID){
		    $listing_sql = xtDBquery("select creator from amazonCreator  where  id = '$ID'");
		    $listing = xtc_db_fetch_array($listing_sql,true);
        return $listing['creator'];
}
function aws_signed_request($region, $params, $public_key, $private_key)
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
    $params["AssociateTag"] = AWS_ASSOC_ID;
    // API version
    #$params["Version"] = "2009-03-31";

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
    #echo "<a href='$request' target='_blank'>$request</a><br><br>"  ;
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
?>