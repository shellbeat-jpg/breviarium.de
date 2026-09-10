<?php

/* -----------------------------------------------------------------------------------------
   $Id: index.php 1321 2005-10-26 20:55:07Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(default.php,v 1.84 2003/05/07); www.oscommerce.com
   (c) 2003	 nextcommerce (default.php,v 1.13 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Enable_Disable_Categories 1.3        	Autor: Mikel Williams | mikel@ladykatcostumes.com
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

       echo 123; 
include ('includes/application_top.php');
    exit;
// create smarty elements
# var_dump($_GET);
#	exit;
$smarty = new Smarty;
 
// include boxes
require (DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/source/boxes.php');

/*
Truncate Table products;
Insert into products (products_quantity, 	products_status, 	products_tax_class_id, products_id, products_ean, products_image, products_price, ASIN, Author, Title, Publisher, NumberOfPages, Edition, PublicationDate, ISBN, EAN, Binding, OfferListing, CurrencyCode, ListPrice, LowestNewPrice, LowestUsedprice, LowestCollectiblePrice, TotalUsed, TotalNew, TotalCollectible)
Select 1, 1, 2, id, EAN, IF(SmallImage != '', concat(id, "_0.jpg"), ''), IF(LowestUsedprice > LowestCollectiblePrice, LowestUsedprice, LowestCollectiblePrice), ASIN, Author, Title, Publisher, NumberOfPages, Edition, PublicationDate, ISBN, EAN , Binding, OfferListing, CurrencyCode, ListPrice, LowestNewPrice, LowestUsedprice, LowestCollectiblePrice, TotalUsed, TotalNew, TotalCollectible
from amazon;

Truncate Table products_description;
Insert into products_description ( products_id, 	language_id, 	products_name ) Select id, 2, Title from amazon;

Truncate Table products_to_categories;
Insert into products_to_categories (products_id, categories_id 	) Select id, categories_id from amazon;
                      amazonOffers  amazonCreator id_amazon
DELETE amazonOffers FROM amazonOffers LEFT JOIN amazon ON amazonOffers.id_amazon=amazon.id WHERE amazon.id IS NULL;
DELETE amazonCreator FROM amazonCreator LEFT JOIN amazon ON amazonCreator.id_amazon=amazon.id WHERE amazon.id IS NULL;


Update products set products_price =  IF(LowestUsedprice > LowestCollectiblePrice, LowestUsedprice, LowestCollectiblePrice)

*/
/*
	$categories_products_query = xtDBquery("Select * from amazon");
	while($cateqories_products = xtc_db_fetch_array($categories_products_query, true)){
    xtDBquery("Update ".TABLE_PRODUCTS_DESCRIPTION." set AddToCartUrl = '".str_replace("'", "\'", utf8_encode($cateqories_products['AddToCartUrl']))."', MerchantId = '".str_replace("'", "\'", utf8_encode($cateqories_products['MerchantId']))."' , SubCondition = '".str_replace("'", "\'", utf8_encode($cateqories_products['SubCondition']))."' , ConditionNote = '".str_replace("'", "\'", utf8_encode($cateqories_products['ConditionNote']))."'   where  products_id = " . $cateqories_products['id']);
	}
	exit;

	$categories_products_query = xtDBquery("Select p.products_id, a.LowestUsedprice  from products p left join amazon a on a.id = p.products_id where products_price = 0");
	while($cateqories_products = xtc_db_fetch_array($categories_products_query, true)){
  #   xtDBquery("Update `products` set products_price = " . $cateqories_products['LowestUsedprice'] . " where  products_price = 0 and products_id = " . $cateqories_products['products_id']);
	}
	#exit;
	
  $categories_products_query = "select * from amazon where id = 3203";
	$categories_products_query = xtDBquery($categories_products_query);
	while($cateqories_products = xtc_db_fetch_array($categories_products_query, true)){
     #xtDBquery("Update ".TABLE_PRODUCTS_DESCRIPTION." set products_name = '".str_replace("'", "\'", utf8_encode($cateqories_products['Title']))."' where products_id = '".$cateqories_products['id']."'");
     # echo "Update ".TABLE_PRODUCTS_DESCRIPTION." set products_name = '".str_replace("'", "\'", utf8_encode($cateqories_products['Title']))."' where products_id = '".$cateqories_products['id']."'<br>";
      
	}
	#exit;
	
	
 $categories_products_query = "select * from ".TABLE_PRODUCTS_DESCRIPTION;
	$categories_products_query = xtDBquery($categories_products_query);
	while($cateqories_products = xtc_db_fetch_array($categories_products_query, true)){
      # xtDBquery("Update ".TABLE_CATEGORIES_DESCRIPTION." set categories_name = '".utf8_encode($cateqories_products['categories_name'])."' where categories_id = '".$cateqories_products['categories_id']."'");
      if($cateqories_products['products_id']>23){
				 #echo str_replace("'", "\'", utf8_encode($cateqories_products['products_name']))    ."<br>";
			   #xtDBquery("Update ".TABLE_PRODUCTS_DESCRIPTION." set 	Binding = '".utf8_encode($cateqories_products['Binding'])."', products_name = '".str_replace("'", "\'", utf8_encode($cateqories_products['products_name']))."', 	author = '".str_replace("'", "\'", utf8_encode($cateqories_products['author']))."', 	Publisher = '".str_replace("'", "\'", utf8_encode($cateqories_products['Publisher']))."' where products_id = '".$cateqories_products['products_id']."'");
			}
      #  																																																							echo utf8_encode(utf8_encode($cateqories_products['categories_name']))."<br>";
	}





	 */
	 

// the following cPath references come from application_top.php
$category_depth = 'top';
if (isset ($cPath) && xtc_not_null($cPath)) {
	$categories_products_query = "select count(*) as total from ".TABLE_PRODUCTS_TO_CATEGORIES." where categories_id = '".$current_category_id."'";
	$categories_products_query = xtDBquery($categories_products_query);
	$cateqories_products = xtc_db_fetch_array($categories_products_query, true);
	if ($cateqories_products['total'] > 0) {
		$category_depth = 'products'; // display products
	} else {
		$category_parent_query = "select count(*) as total from ".TABLE_CATEGORIES." where parent_id = '".$current_category_id."'";
		$category_parent_query = xtDBquery($category_parent_query);
		$category_parent = xtc_db_fetch_array($category_parent_query, true);
		if ($category_parent['total'] > 0) {
			$category_depth = 'nested'; // navigate through the categories
		} else {
			$category_depth = 'products'; // category has no products, but display the 'no products' message
		}
	}
}

require (DIR_WS_INCLUDES.'header.php');

include (DIR_WS_MODULES.'default.php');

$smarty->assign('language', $_SESSION['language']);

$smarty->caching = 0;
if (!defined(RM))
	$smarty->load_filter('output', 'note');
$smarty->display(CURRENT_TEMPLATE.'/index.html');

include ('includes/application_bottom.php');  
?>