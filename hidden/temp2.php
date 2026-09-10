<?php
include("includes/db.php");
include("includes/functions.php");
#require_once('Cache/Lite/Function.php');

$page = trim($_GET['page']);
if($page<1)$page=1;

$MinimumPrice=trim($_GET['MinimumPrice']);
if($MinimumPrice<1)$MinimumPrice=500;

$limit = $_GET['limit'];

$key = trim($_GET['k']);
if($key<1)$key=0;

$c = trim($_GET['c']);
if($c<1)$c=1;

if($c>2000){
	if($key==1){
    $key = 0;
	}else{
    $key = 1;
	}
	echo "<a href='http://buch.breviarium.de/hidden/temp2.php?&c=0&limit=$limit&page$page&MinimumPrice=".($MinimumPrice-10)."'>Weiter</a>";
	exit;
}

$key = 1;
define('AWS_KEY', $arrAwsKeys[$key]['AWS_KEY']);
define('AWS_SECRET_KEY', $arrAwsKeys[$key]['AWS_SECRET_KEY']);
 













$arrBinding=array('Kalender', 'Broschüre', 'Zubehör', 'Videokassette', 'Digital Download', 'Landkarte', 'Vorbespielter Audioplayer', 'Stoffbilderbuch', 'Loseblattsammlung', 'Hörkassette', 'Musiknoten', 'Zeitschrift', 'Comic', 'Geschenkartikel', 'Kindle Edition', 'CD-ROM');
$arrCategories = array(13694231, 13694251, 13694261, 13694271, 13695961, 13695981, 13695991, 13696001, 13696011, 13696051, 13696131, 13696141, 13696261, 13696371, 13696561, 13696711, 13696801);




if($limit<1)$limit=0;

if($MinimumPrice<80 || $page > 40){
	$MinimumPrice=500;
	$page=1;
	$limit++;
}

if($limit>=count($arrCategories))
	exit;

$categories_id=$arrCategories[$limit];

# echo $categories_id;exit;
/*
$result = mysql_query("select distinct categories_id from categories where parent_id 	 > 1 group by categories_id order by categories_id", $db_link); #  limit $limit, 1
if(!mysql_num_rows($result))
	exit;
$result_download = mysql_fetch_array($result, MYSQL_ASSOC);
$categories_id=$result_download['categories_id'];
*/


function aws_signed_request($region, $params, $public_key, $private_key)
{

    /*
    Parameters:
        $region - the Amazon(r) region (ca,com,co.uk,de,fr,jp)
        $params - an array of parameters, eg. array("Operation"=>"ItemLookup",
                        "ItemId"=>"B000X9FLKM", "ResponseGroup"=>"Small")
        $public_key - your "Access Key ID"
        $private_key - your "Secret Access Key"
    */

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
    echo "<a href='$request' target='_blank'>$request</a><br><br>"  ;
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
# http://www.webknecht.net/temp.php5?&isbn=9783827325426



function getCategory($ASIN){
	global $db_link;
	$arrReturn=array();
	$params = array("Operation"=>"ItemLookup",  "ItemId"=>$ASIN, 'IdType' => 'ISBN', "ResponseGroup"=>"OfferListings","SearchIndex"=>"Books") ; #   # Offers/Offer/OfferAttributes
	$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);
	$Nodes=$pxml->Items->Item->BrowseNodes->BrowseNode;
	for($i=0; $i < count($Nodes); $i++){
		$result = mysql_query("select categories_id from categories where categories_id ='".$Nodes[$i]->BrowseNodeId."'", $db_link);
		if(mysql_num_rows($result) && !in_array($Nodes[$i]->BrowseNodeId, $arrReturn))
			 $arrReturn[]=$Nodes[$i]->BrowseNodeId;
	}
	return $arrReturn ;
}


function getDetailsFromASIN($ASIN){
	global $db_link;
	$arrReturn=array();
	$params = array("Operation"=>"ItemLookup",  "ItemId"=>$ASIN, 'IdType' => 'ASIN', "Condition"=>"All","MerchantId"=>"All","ResponseGroup"=>"Large") ; #   # Offers/Offer/OfferAttributes
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

function getOffersFromPriceAndNode($MinimumPrice, $MaximumPrice, $categories_id="", $ItemPage = 1, $Titel = "*"){
	global $db_link, $arrBinding;
	$TotalPages =0;
	$MinimumPrice=$MinimumPrice*100;
	$MaximumPrice=$MaximumPrice*100;
	$arrReturn=array();
	$params = array("ItemPage"=> $ItemPage,"Operation"=>"ItemSearch", "BrowseNode" => $categories_id,  "Title"=>$Titel,  "MinimumPrice"=>$MinimumPrice,  "MaximumPrice"=>$MaximumPrice, "Sort" => "inverse-pricerank", "Condition"=>"All","MerchantId"=>"All","ResponseGroup"=>"Large","SearchIndex"=>"Books") ; #   # Offers/Offer/OfferAttributes
	$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);
	if(isset($pxml->ItemSearchErrorResponse)){
		echo $pxml->ItemSearchErrorResponse->Error->Code  ;
		echo $pxml->ItemSearchErrorResponse->Error->Message  ;
		exit;
	}

	$TotalResults=$pxml->Items->TotalResults;
	$TotalPages=$pxml->Items->TotalPages;
	echo "<br>$TotalResults : $TotalPages<br>";
	$i=0;
  foreach($pxml->Items->Item as $item) {

	 if(isset($item->Offers) && !in_array($item->ItemAttributes->Binding, $arrBinding)){
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

	 $TotalUsed = $OfferSummary->TotalUsed;
	 $TotalCollectible = $OfferSummary->TotalCollectible;

	 $ASIN  = $item->ASIN;
	 $ItemAttributes = $item->ItemAttributes;
	 $ListPrice = number_format($ItemAttributes->ListPrice->Amount/100, 2);


	 $Title = $ItemAttributes->Title;
	 $Author = $ItemAttributes->Author;
	 $Publisher = $ItemAttributes->Publisher;
	 $PublicationDate = $ItemAttributes->PublicationDate;
	 $NumberOfPages = $ItemAttributes->NumberOfPages;
	 $ISBN = $ItemAttributes->ISBN;
	 $EAN = $ItemAttributes->EAN;
	 $Edition = $ItemAttributes->Edition;
	 $Binding = $ItemAttributes->Binding;
	 echo "<br><image src='$LargeImage'>";
   echo "<br>$ASIN: ($TotalUsed / $TotalCollectible) $LowestUsedPrice / $LowestCollectiblePrice $CurrencyCode - $Title<br>$LargeImage<br>";

   $arrReturn[$i] = array('TotalCollectible' => $TotalCollectible , 'TotalUsed' => $TotalUsed , 'CurrencyCode' => $CurrencyCode , 'ListPrice' => $ListPrice , 'LowestNewPrice' => $LowestNewPrice , 'LowestUsedPrice' => $LowestUsedPrice , 'LowestCollectiblePrice' => $LowestCollectiblePrice , 'LargeImage' => $LargeImage, 'MediumImage' => $MediumImage, 'SmallImage' => $SmallImage, 'ASIN' => $ASIN, 'Title' => $Title, 'ISBN' => $ISBN, 'EAN' => $EAN, 'Binding' => $Binding, 'Publisher' => $Publisher, 'PublicationDate' => $PublicationDate, 'NumberOfPages' => $NumberOfPages);

   $arrReturn[$i]['Creator'] = array();


$SQL = "INSERT INTO  amazon  (
						`id` ,
						`ASIN` ,
						`SmallImage` ,
						`MediumImage` ,
						`LargeImage` ,
						`Author` ,
						`Title` ,
						`Publisher` ,
						`NumberOfPages` ,
						`PublicationDate` ,
						`ISBN` ,
						`EAN` ,
						`Edition` ,
						`Binding` ,
						`CurrencyCode` ,
						`ListPrice` ,
						`LowestNewPrice` ,
						`LowestUsedPrice` ,
						`LowestCollectiblePrice` ,
						`TotalUsed` ,
						`TotalCollectible` ,
						`categories_id`
						)
						VALUES (
						NULL , '".$ASIN."',
						'".$SmallImage."',
						'".$MediumImage."',
						'".$LargeImage."',
						'".str_replace("'", "\'", utf8_decode($Author))."',
						'". str_replace("'", "\'", utf8_decode($Title))."',
						'".str_replace("'", "\'", utf8_decode($Publisher))."',
						'".str_replace("'", "\'", utf8_decode($NumberOfPages))."',
						'".str_replace("'", "\'", utf8_decode($PublicationDate))."',
						'".str_replace("'", "\'", utf8_decode($ISBN))."',
						'".str_replace("'", "\'", utf8_decode($EAN))."',
						'".str_replace("'", "\'", utf8_decode($Edition))."',
						'".str_replace("'", "\'", utf8_decode($Binding))."',
						'".$CurrencyCode."',
						'".$ListPrice."',
						'".$LowestNewPrice."',
						'".$LowestUsedPrice."',
						'".$LowestCollectiblePrice."',
						'".$TotalUsed."',
						'".$TotalCollectible."',
               $categories_id
						);";
						mysql_query($SQL, $db_link); # or die($SQL."<br>".mysql_error());
	          $id_amazon = mysql_insert_id();

					 foreach( $ItemAttributes->Creator as $Creator) {
					 	echo "<br>".$Creator['Role'].": ".$Creator;
					 	$arrReturn[$i]['Creator'][]=array('Role' => $Creator['Role'], 'Creator' => $Creator);
					  $SQL = "INSERT INTO amazonCreator (creator, role, id_amazon) VALUES('". str_replace("'", "\'", utf8_decode($Creator))."', '". str_replace("'", "\'", utf8_decode($Creator['Role']))."', $id_amazon)";
            mysql_query($SQL, $db_link); # or die($SQL."<br>".mysql_error());
					 }

				   $arrReturn[$i]['Offers'] = array();
				   foreach($item->Offers->Offer as $Offer) {
				         $MerchantId = $Offer->Merchant->MerchantId;
				         $SubCondition = $Offer->OfferAttributes->SubCondition;
				         $ConditionNote = $Offer->OfferAttributes->ConditionNote;
				         $oAmount = number_format($Offer->OfferListing->Price->Amount/100, 2);
				         $oCurrencyCode = $Offer->OfferListing->Price->CurrencyCode;
				         echo "$MerchantId $SubCondition $oAmount $oCurrencyCode<br>";
				         $arrReturn[$i]['Offers'][]=array('oAmount' => $oAmount, 'oCurrencyCode' => $oCurrencyCode, 'AddToCartUrl' => "http://www.amazon.de/gp/aws/cart/add.html?&AWSAccessKeyId=[AWS_KEY]&ASIN.1=$ASIN&Quantity.1=1&SellerId.1=$MerchantId", 'MerchantId' => $MerchantId, 'SubCondition' => $SubCondition, 'ConditionNote' => $ConditionNote);
				         $SQL = "INSERT INTO amazonOffers (id_amazon, MerchantId, oAmount, oCurrencyCode, SubCondition, ConditionNote, AddToCartUrl)  VALUES($id_amazon, '".$MerchantId."', '".$oAmount."', '".$oCurrencyCode."', '". str_replace("'", "\'", utf8_decode($SubCondition))."', '".str_replace("'", "\'", utf8_decode($ConditionNote))."', 'www.amazon.de/gp/aws/cart/add.html?&AWSAccessKeyId=[AWS_KEY]&ASIN.1=$ASIN&Quantity.1=1&SellerId.1=$MerchantId')";
                 mysql_query($SQL, $db_link); # or die($SQL."<br>".mysql_error());
					 }
				  if($SmallImage!="")
				  	copy($SmallImage, 'thumbnail_images/' . $id_amazon."_0.jpg");
				  if($LargeImage!="")
				  	copy($LargeImage, 'popup_images/' . $id_amazon."_0.jpg");



   $i++;
    }
  }
	return $TotalPages;
}


  echo "$categories_id: $page ($TotalPages) - $MinimumPrice<br>";

  $TotalPages=getOffersFromPriceAndNode($MinimumPrice, $MinimumPrice+9.99, $categories_id, $page);



	if($page>=$TotalPages){
		$MinimumPrice=$MinimumPrice-10;
		$page=0;
	}
	$c++;
  echo "<script>\n";
  echo "document.location.href=\"http://amazon.breviarium.de/temp2.php?&k=$key&c=$c&limit=$limit&page=".($page+1)."&MinimumPrice=$MinimumPrice\"\n";
  echo "</script>\n";


?>