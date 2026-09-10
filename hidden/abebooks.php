<?php
set_time_limit(0);
ignore_user_abort(true);
include("includes/db.php");
include("includes/functions.php");

extract($_SERVER['QUERY_STRING']);
 # http://search2.abebooks.com/search?&currency=EUR&destinationcountry=DE&outputsize=long&title=Die%20G%F6ttliche%20Com%F6die&bookcondition=used&clientkey=ab0afb06-3aed-4ec4-b8cb-d132627077f6
 # ab0afb06-3aed-4ec4-b8cb-d132627077f6
 # http://www.zanox-affiliate.de/ppc/?18874409C1850807451T&ULP=[[ULP]]
 # http://www.zanox-affiliate.de/ppc/?18878769C1340896547T&ULP=[[&isbn=ISBN]]
 # http://www.zanox-affiliate.de/ppc/?18878810C1140401093T&ULP=BUCH-ID
 # ISBN
 # http://search2.abebooks.com/search?&isbn=ISBN&currency=EUR&destinationcountry=DE&outputsize=microbookcondition=used&clientkey=ab0afb06-3aed-4ec4-b8cb-d132627077f6
#keyword=9783806728385

function getABEBOOKSData($KEY, $SEARCH){
		global $ABE_URL;
		$request = str_replace($KEY, $SEARCH, $ABE_URL);
		#echo $request  ;
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

function getABEBOOKSBookID(){
	global $db_link;
	$arrReturn=array();
	$SQL = "SELECT o.id, p.products_ean FROM  amazonOffers o left join products p on o.id_amazon = p.products_id WHERE  o.Platform  = 'Abebooks-Deutsch'";
	$check = mysql_query($SQL);
	while($productsCheck = mysql_fetch_array($check, MYSQL_ASSOC)){
	      $idOffer = $productsCheck['id'];
	      $EAN = $productsCheck['products_ean'];
				$pxml = getABEBOOKSData('EAN', $EAN);
				$bookId=$pxml->Book->bookId;
				if(isset($bookId)){
				 mysql_query("Update amazonOffers set id_abebooks = '$bookId' where id = '$idOffer'");
    		 echo "$EAN - $bookId<br>";
				}
	}
}

echo getABEBOOKSBookID();
?>