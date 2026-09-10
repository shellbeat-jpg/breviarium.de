<?php
/* -----------------------------------------------------------------------------------------
   $Id: product_info.php 1317 2005-10-21 16:03:18Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_info.php,v 1.94 2003/05/04); www.oscommerce.com 
   (c) 2003      nextcommerce (product_info.php,v 1.46 2003/08/25); www.nextcommerce.org

   Released under the GNU General Public License 
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist
   New Attribute Manager v4b                            Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com   
   Cross-Sell (X-Sell) Admin 1                          Autor: Joshua Dechant (dreamscape)
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

//include needed functions
require_once (DIR_FS_INC.'xtc_check_categories_status.inc.php');
require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');
require_once (DIR_FS_INC.'xtc_get_vpe_name.inc.php');
require_once (DIR_FS_INC.'get_cross_sell_name.inc.php');
require_once (DIR_FS_INC.'xtc_date_short.inc.php');
$status = 0;
$info_smarty = new Smarty;
$info_smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
$group_check = '';

// BOF - Tomcraft - 2009-11-28 - Included xs:booster
// xs:booster start (v1.041)
$xsb_tx = array();
if(@is_array($_SESSION['xtb0']['tx'])) {
	foreach($_SESSION['xtb0']['tx'] as $tx) {
		if($tx['products_id']==$product->data['products_id']) {
			$xsb_tx = $tx;
			break;
		}
	}
}
// xs:booster end
// EOF - Tomcraft - 2009-11-28 - Included xs:booster

if (!is_object($product) || !$product->isProduct()) { // product not found in database

	$error = TEXT_PRODUCT_NOT_FOUND;
	include (DIR_WS_MODULES.FILENAME_ERROR_HANDLER);

} else {



	if (ACTIVATE_NAVIGATOR == 'true')
		include (DIR_WS_MODULES.'product_navigator.php');


	xtc_db_query("update ".TABLE_PRODUCTS_DESCRIPTION." set products_viewed = products_viewed+1 where products_id = '".$product->data['products_id']."' and language_id = '".$_SESSION['languages_id']."'");

		$products_price = $xtPrice->xtcGetPrice($product->data['products_id'], $format = true, 1, $product->data['products_tax_class_id'], $product->data['products_price'], 1);

		// check if customer is allowed to add to cart
		if ($_SESSION['customers_status']['customers_status_show_price'] != '0') {
			// fsk18
			if ($_SESSION['customers_status']['customers_fsk18'] == '1') {
				if ($product->data['products_fsk18'] == '0') {
// BOF - Tomcraft - 2009-11-28 - Included xs:booster
					//$info_smarty->assign('ADD_QTY', xtc_draw_input_field('products_qty', '1', 'size="3"').' '.xtc_draw_hidden_field('products_id', $product->data['products_id']));
				if(@$xsb_tx['XTB_ALLOW_USER_CHQTY']=='true'||$xsb_tx['products_id']!=$product->data['products_id'])
					$info_smarty->assign('ADD_QTY', xtc_draw_hidden_field('products_qty', '1').' '.xtc_draw_hidden_field('products_id', $product->data['products_id']));
				else
					$info_smarty->assign('ADD_QTY', xtc_draw_hidden_field('products_qty', '1').' '.xtc_draw_hidden_field('products_id', $product->data['products_id']));
// EOF - Tomcraft - 2009-11-28 - Included xs:booster
					$info_smarty->assign('ADD_CART_BUTTON', xtc_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART));
					$info_smarty->assign('IMAGE_BUTTON_IN_CART', IMAGE_BUTTON_IN_CART);
				}
			} else {
// BOF - Tomcraft - 2009-11-28 - Included xs:booster
				//$info_smarty->assign('ADD_QTY', xtc_draw_input_field('products_qty', '1', 'size="3"').' '.xtc_draw_hidden_field('products_id', $product->data['products_id']));
			if(@$xsb_tx['XTB_ALLOW_USER_CHQTY']=='true'||$xsb_tx['products_id']!=$product->data['products_id'])
				$info_smarty->assign('ADD_QTY', xtc_draw_hidden_field('products_qty', '1').' '.xtc_draw_hidden_field('products_id', $product->data['products_id']));
			else
				$info_smarty->assign('ADD_QTY', xtc_draw_hidden_field('products_qty', '1').' '.xtc_draw_hidden_field('products_id', $product->data['products_id']));
// EOF - Tomcraft - 2009-11-28 - Included xs:booster
				$info_smarty->assign('ADD_CART_BUTTON', xtc_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART));
			}
		}

		if ($product->data['products_fsk18'] == '1') {
			$info_smarty->assign('PRODUCTS_FSK18', 'true');
		}
		if (ACTIVATE_SHIPPING_STATUS == 'true') {
			$info_smarty->assign('SHIPPING_NAME', $main->getShippingStatusName($product->data['products_shippingtime']));
			$info_smarty->assign('SHIPPING_IMAGE', $main->getShippingStatusImage($product->data['products_shippingtime']));
		}
		$info_smarty->assign('FORM_ACTION', xtc_draw_form('cart_quantity', xtc_href_link(FILENAME_PRODUCT_INFO, xtc_get_all_get_params(array ('action')).'action=add_product')));
		$info_smarty->assign('FORM_END', '</form>');
		$info_smarty->assign('PRODUCTS_PRICE', $products_price['formated']);
		if ($product->data['products_vpe_status'] == 1 && $product->data['products_vpe_value'] != 0.0 && $products_price['plain'] > 0)
			$info_smarty->assign('PRODUCTS_VPE', $xtPrice->xtcFormat($products_price['plain'] * (1 / $product->data['products_vpe_value']), true).TXT_PER.xtc_get_vpe_name($product->data['products_vpe']));
		$info_smarty->assign('PRODUCTS_ID', $product->data['products_id']);
		$info_smarty->assign('PRODUCTS_NAME', $product->data['products_name']);
		$info_smarty->assign('PRODUCTS_Offers', $product->offers);
		$info_smarty->assign('TOTAL_Offers', count($product->offers));
   	$info_smarty->assign('MIN_Offers', $product->offers[0]);
		$info_smarty->assign('PRODUCTS_Persons', $product->persons);


 	 #if($product->data['products_id']=='147263')
     # var_dump( $product->offers);



				 $info_smarty->assign('PRODUCTS_Author' ,  $product->data['Author']);
				$info_smarty->assign('PRODUCTS_Publisher' ,  $product->data['Publisher']);
				 $info_smarty->assign('PRODUCTS_PublicationDate' ,  ($product->data['PublicationDate']!='0000-00-00 00:00:00'?substr($product->data['PublicationDate'], 0, 4):''));
				$info_smarty->assign( 'PRODUCTS_Binding' ,  $product->data['Binding']);
				 $info_smarty->assign('PRODUCTS_ASIN' ,  $product->data['ASIN']);
				$info_smarty->assign('PRODUCTS_AddToCartUrl',  $product->data['AddToCartUrl']);
				 $info_smarty->assign('PRODUCTS_MerchantId' ,  $product->data['MerchantId']);
				 $info_smarty->assign('PRODUCTS_SubCondition' ,  $product->data['SubCondition']);
				 $info_smarty->assign('PRODUCTS_ConditionNote' ,  $product->data['ConditionNote']);

  			$info_smarty->assign('PRODUCTS_NumberOfPages' ,  $product->data['NumberOfPages']);
		    $info_smarty->assign('PRODUCTS_Edition' ,  $product->data['Edition']);
		    $info_smarty->assign('PRODUCTS_ISBN' ,  $product->data['ISBN']);
		    $info_smarty->assign('PRODUCTS_EAN' ,  $product->data['EAN']);
		    $info_smarty->assign('PRODUCTS_OfferListing' ,  $product->data['OfferListing']);
		    $info_smarty->assign('PRODUCTS_CurrencyCode' ,  $product->data['CurrencyCode']);
		    $info_smarty->assign('PRODUCTS_EditorialSource' ,  str_replace("Product Description", "Anmerkungen", $product->data['EditorialSource']));
		    $info_smarty->assign('PRODUCTS_EditorialReview' ,  $product->data['EditorialReview']);
		    $info_smarty->assign('PRODUCTS_ListPrice' ,  $product->data['ListPrice']);
		    $info_smarty->assign('PRODUCTS_LowestNewPrice' ,  $product->data['LowestNewPrice']);
		    $info_smarty->assign('PRODUCTS_LowestUsedprice' ,  $product->data['LowestUsedprice']);
		    $info_smarty->assign('PRODUCTS_LowestCollectiblePrice' ,  $product->data['LowestCollectiblePrice']);
		    $info_smarty->assign('PRODUCTS_TotalUsed' ,  $product->data['TotalUsed']);
		    $info_smarty->assign('PRODUCTS_TotalNew' ,  $product->data['TotalNew']);
		    $info_smarty->assign('PRODUCTS_TotalCollectible' ,  $product->data['TotalCollectible']);




		if ($_SESSION['customers_status']['customers_status_show_price'] != 0) {
			// price incl tax
			$tax_rate = $xtPrice->TAX[$product->data['products_tax_class_id']];
			$tax_info = $main->getTaxInfo($tax_rate);
			$info_smarty->assign('PRODUCTS_TAX_INFO', $tax_info);
			$info_smarty->assign('PRODUCTS_SHIPPING_LINK',$main->getShippingLink());
		}
		$info_smarty->assign('PRODUCTS_MODEL', $product->data['products_model']);
		$info_smarty->assign('PRODUCTS_EAN', $product->data['products_ean']);
		$info_smarty->assign('PRODUCTS_QUANTITY', xtc_draw_hidden_field('products_quantity', $product->data['products_quantity']));
		$info_smarty->assign('QUANTITY', $product->data['products_quantity']);
		if($product->data['products_quantity']>0)
			 $status=1;
	#	echo "q: ".$product->data['products_quantity']  ;
		$info_smarty->assign('PRODUCTS_WEIGHT', $product->data['products_weight']);
		$info_smarty->assign('PRODUCTS_STATUS', $product->data['products_status']);
		$info_smarty->assign('PRODUCTS_ORDERED', $product->data['products_ordered']);
//BOF - Tomcraft - 2010-04-03 - unified popups with scrollbars and make them resizable
		//$info_smarty->assign('PRODUCTS_PRINT', '<img src="templates/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/print.gif"  style="cursor:pointer" onclick="javascript:window.open(\''.xtc_href_link(FILENAME_PRINT_PRODUCT_INFO, 'products_id='.$product->data['products_id']).'\', \'popup\', \'toolbar=0, width=640, height=600\')" alt="" />');
		$info_smarty->assign('PRODUCTS_PRINT', '<img src="templates/'.CURRENT_TEMPLATE.'/buttons/'.$_SESSION['language'].'/print.gif"  style="cursor:pointer" onclick="javascript:window.open(\''.xtc_href_link(FILENAME_PRINT_PRODUCT_INFO, 'products_id='.$product->data['products_id']).'\', \'popup\', \'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no, width=640, height=600\')" alt="" />');
//EOF - Tomcraft - 2010-04-03 - unified popups with scrollbars and make them resizable
		$info_smarty->assign('PRODUCTS_DESCRIPTION', stripslashes($product->data['products_description']));
// BOF - Tomcraft - 2009-11-28 - Included xs:booster
		if(isset($xsb_tx['XTB_REDIRECT_USER_TO'])&&$xsb_tx['products_id']==$product->data['products_id'])
			$info_smarty->assign('XTB_REDIRECT_USER_TO', $xsb_tx['XTB_REDIRECT_USER_TO']);
// EOF - Tomcraft - 2009-11-28 - Included xs:booster
		$image = '';
// BOF - Tomcraft - 2009-10-30 - use allready defined function from product.php
/*
		if ($product->data['products_image'] != '')
			$image = DIR_WS_INFO_IMAGES.$product->data['products_image'];
*/
		$image = $product->productImage($product->data['products_image'], 'popup');
		if($product->data['LargeImage']!=''&&$product->data['LargeImage']!='1')
		   $image=$product->data['LargeImage'];

		
		
// EOF - Tomcraft - 2009-10-30 - use allready defined function from product.php		

		$info_smarty->assign('PRODUCTS_IMAGE', $image);

//-- SEO ShopStat
/*
		//mo_images - by Novalis@eXanto.de
		if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
			$connector = '/';
		}else{
			$connector = '&';
		}
*/
    $connector = '&amp;';
//-- SEO ShopStat

		$info_smarty->assign('PRODUCTS_POPUP_LINK', 'javascript:popupWindow(\''.xtc_href_link(FILENAME_POPUP_IMAGE, 'pID='.$product->data['products_id'].$connector.'imgID=0').'\')');
		$mo_images = xtc_get_products_mo_images($product->data['products_id']);
		if ($mo_images != false) {
// BOF - Tomcraft - 2009-09-12 - build more_images array
/*
			foreach ($mo_images as $img) {
				$mo_img = DIR_WS_INFO_IMAGES.$img['image_name'];
				$info_smarty->assign('PRODUCTS_IMAGE_'.$img['image_nr'], $mo_img);
				$info_smarty->assign('PRODUCTS_POPUP_LINK_'.$img['image_nr'], 'javascript:popupWindow(\''.xtc_href_link(FILENAME_POPUP_IMAGE, 'pID='.$product->data['products_id'].$connector.'imgID='.$img['image_nr']).'\')');
			}
*/   
			$more_images_data = array();
			foreach ($mo_images as $img) {
				$more_images_data[] = array ('PRODUCTS_IMAGE' => DIR_WS_INFO_IMAGES.$img['image_name'],
											 'PRODUCTS_POPUP_LINK' => 'javascript:popupWindow(\''.xtc_href_link(FILENAME_POPUP_IMAGE, 'pID='.$product->data['products_id'].$connector.'imgID='.$img['image_nr']).'\')'
											 );
				// BOF - Tomcraft - 2009-09-12 - needed for non modified templates
				$mo_img = DIR_WS_INFO_IMAGES.$img['image_name'];
				$info_smarty->assign('PRODUCTS_IMAGE_'.$img['image_nr'], $mo_img);
				$info_smarty->assign('PRODUCTS_POPUP_LINK_'.$img['image_nr'], 'javascript:popupWindow(\''.xtc_href_link(FILENAME_POPUP_IMAGE, 'pID='.$product->data['products_id'].$connector.'imgID='.$img['image_nr']).'\')');
				// EOF - Tomcraft - 2009-09-12 - needed for non modified templates
			}
			$info_smarty->assign('more_images', $more_images_data);
// EOF - Tomcraft - 2009-09-12 - build more_images array
		}
		//mo_images EOF
		$discount = 0.00;
		if ($_SESSION['customers_status']['customers_status_public'] == 1 && $_SESSION['customers_status']['customers_status_discount'] != '0.00') {
			$discount = $_SESSION['customers_status']['customers_status_discount'];
			if ($product->data['products_discount_allowed'] < $_SESSION['customers_status']['customers_status_discount'])
				$discount = $product->data['products_discount_allowed'];
			if ($discount != '0.00')
				$info_smarty->assign('PRODUCTS_DISCOUNT', $discount.'%');
		}

	#	include (DIR_WS_MODULES.'product_attributes.php');
	#	include (DIR_WS_MODULES.'product_reviews.php');

		if (xtc_not_null($product->data['products_url']))
			$info_smarty->assign('PRODUCTS_URL', sprintf(TEXT_MORE_INFORMATION, xtc_href_link(FILENAME_REDIRECT, 'action=product&id='.$product->data['products_id'], 'NONSSL', true, false)));


			if ($product->data['products_date_added'] != '0000-00-00 00:00:00')
				$info_smarty->assign('PRODUCTS_ADDED', sprintf(TEXT_DATE_ADDED, xtc_date_long($product->data['products_date_added'])));
			if ($product->data['products_last_modified'] != '0000-00-00 00:00:00')
				$info_smarty->assign('PRODUCTS_SAVED', xtc_date_short($product->data['products_last_modified']));
			if($product->data['DetailPageURL']!='')
      	#$info_smarty->assign('URL_AMAZON', HTTP_SERVER."/index.php?&pID=".$product->data['products_id']."&target=".base64url_encode($product->data['DetailPageURL']));
        $info_smarty->assign('URL_AMAZON', HTTP_SERVER."/index.php?&pID=".$product->data['products_id']."&target=".base64url_encode('http://www.amazon.de/gp/offer-listing/'.$product->data['ASIN'].'/ref=dp_olp_0?ie=UTF8&condition=all&tag=breviarium-21'));
		 #if($product->data['ASIN']=='3781216780')
		#				echo HTTP_SERVER."/index.php?&pID=".$product->data['products_id']."&target=".base64url_encode($product->data['DetailPageURL'])   ;

		  $info_smarty->assign('ASIN', xtc_draw_hidden_field('ASIN', $product->data['ASIN']));
      
      #$data_reviews[] = array ('ExchangeId' => $reviews['ExchangeId'], 'OfferListingId' => $reviews['OfferListingId'], 'MerchantId' => $reviews['MerchantId'], 'oAmount' => $xtPrice->xtcGetPrice($this->pID, 1, 1, 2, $reviews['oAmount'], 1), 'oCurrencyCode' =>  $reviews['oCurrencyCode'] ,  'SubCondition' => $arrCondition[$reviews['SubCondition']],  'ConditionNote' => $reviews['ConditionNote']);
			if(count($product->offers)>1){
				for ($i = 0; $i < count($product->offers); $i++) {
	           $arrOffers[]=array('id' => $product->offers[$i]['OfferListingId'], 'text' => "Angebot " . ($i+1) ." (".$product->offers[$i]['oAmount']['formated']."): " . substr($product->offers[$i]['ConditionNote'], 0 , 200).'..');
	      }
	      $info_smarty->assign('OfferListingId', xtc_draw_pull_down_menu('OfferListingId', $arrOffers, '', 'style="width:510px;"'));
			}else{
      	$info_smarty->assign('OfferListingId', xtc_draw_hidden_field('OfferListingId', $product->offers[0]['OfferListingId']));
			}
      $info_smarty->assign('ExchangeId', $product->offers[0]['ExchangeId']);


/*
      # ShoppingCart anlegen und Titel Hinzufügen
			$params = array("AssociateTag"=>AWS_ASSOC_ID, "Operation"=>"CartCreate",  "Item.1.OfferListingId"=>$product->offers[0]['OfferListingId'],"Item.1.Quantity"=>"1") ;
			$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);

			$CartId = $pxml->Cart->CartId;
			$HMAC = $pxml->Cart->HMAC;
			$URLEncodedHMAC = $pxml->Cart->URLEncodedHMAC;
			$PurchaseURL = $pxml->Cart->PurchaseURL;
			$SubTotalAmount=$pxml->Cart->SubTotal->Amount;
			$SubTotalCurrencyCode=$pxml->Cart->SubTotal->CurrencyCode;
			$SubTotalFormattedPrice=$pxml->Cart->SubTotal->FormattedPrice;

      $_SESSION['CartId'] = (string) $CartId;
      $_SESSION['HMAC'] = (string) $HMAC;
      $_SESSION['URLEncodedHMAC'] = (string) $URLEncodedHMAC;
      $_SESSION['PurchaseURL'] = (string) $PurchaseURL;


   		echo "\$SubTotalAmount: $SubTotalAmount<br>";
   		echo "\$SubTotalCurrencyCode: $SubTotalCurrencyCode<br>";
   		echo "\$SubTotalFormattedPrice: $SubTotalFormattedPrice<br>";

      $CartItems=$pxml->Cart->CartItems;
			for($i=0; $i < count($CartItems); $i++){
				 $CartItemId=$CartItems[$i]->CartItem->CartItemId;
				 $MerchantId=$CartItems[$i]->CartItem->MerchantId;
				 $SubAmount = $CartItems[$i]->SubTotal->Amount;
				 $SubCurrencyCode=$CartItems[$i]->SubTotal->CurrencyCode;
				 $SubFormattedPrice=$CartItems[$i]->SubTotal->FormattedPrice;

				 echo "\$CartItemId: $CartItemId<br>";
				 echo "\$MerchantId: $MerchantId<br>";
				 echo "\$SubAmount: $SubAmount<br>";
				 echo "\$SubCurrencyCode: $SubCurrencyCode<br>";
				 echo "\$SubFormattedPrice: $SubFormattedPrice<br>";
			}

*/
/*
			# Titel Hinzufügen
			$params = array("AssociateTag"=>AWS_ASSOC_ID, "Operation"=>"CartAdd", "HMAC"=>$_SESSION['HMAC'], "CartId"=>$_SESSION['CartId'],  "Item.1.OfferListingId"=>$product->offers[0]['OfferListingId'],"Item.1.Quantity"=>"1") ;
			$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);


			# Titel löschen
			$params = array("AssociateTag"=>AWS_ASSOC_ID, "Operation"=>"CartModify", "HMAC"=>$_SESSION['HMAC'], "CartId"=>$_SESSION['CartId'],  "Item.1.CartItemId"=>$product->offers[0]['CartItemId'],"Item.1.Quantity"=>"0") ;
			$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);

			# Alle Titel löschen
			$params = array("AssociateTag"=>AWS_ASSOC_ID, "Operation"=>"CartClear", "HMAC"=>$_SESSION['HMAC'], "CartId"=>$_SESSION['CartId']) ;
			$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);

			# Übersicht ShoppingCart
			$params = array("AssociateTag"=>AWS_ASSOC_ID, "Operation"=>"CartGet", "HMAC"=>$_SESSION['HMAC'], "CartId"=>$_SESSION['CartId']) ;
			$pxml = aws_signed_request('de', $params, AWS_KEY, AWS_SECRET_KEY);

*/


		if ($_SESSION['customers_status']['customers_status_graduated_prices'] == 1)
			include (DIR_WS_MODULES.FILENAME_GRADUATED_PRICE);

		include (DIR_WS_MODULES.FILENAME_PRODUCTS_MEDIA);
		include (DIR_WS_MODULES.FILENAME_ALSO_PURCHASED_PRODUCTS);
		include (DIR_WS_MODULES.FILENAME_CROSS_SELLING);
	if ($product->data['product_template'] == '' or $product->data['product_template'] == 'default') {
		$files = array ();

		if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/')) {

			while ($file = readdir($dir)) {
// BOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
				//if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/'.$file) and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
				if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/'.$file) and (substr($file, -5) == ".html") and ($file != "index.html") and (substr($file, 0, 1) !=".")) {
// EOF - Tomcraft - 2010-02-04 - Prevent xtcModified from fetching other files than *.html
// BOF - web28 - 2010-07-12 - sort templates array
					//$files[] = array ('id' => $file, 'text' => $file);
					$files[] = $file;
				} //if
			} // while
			closedir($dir);
		}		
		sort($files);        	

		$product->data['product_template'] = $files[0];
//EOF - web28 - 2010-07-12 - sort templates array
	}

$i = count($_SESSION['tracking']['products_history']);
	if ($i > 6) {
		array_shift($_SESSION['tracking']['products_history']);
		$_SESSION['tracking']['products_history'][6] = $product->data['products_id'];
		$_SESSION['tracking']['products_history'] = array_unique($_SESSION['tracking']['products_history']);
	} else {
		$_SESSION['tracking']['products_history'][$i] = $product->data['products_id'];
		$_SESSION['tracking']['products_history'] = array_unique($_SESSION['tracking']['products_history']);
	}

  require_once (DIR_FS_INC.'xtc_validate_email.inc.php');
	$error = false;
	if (isset ($_GET['action']) && ($_GET['action'] == 'send')) {
		$err_msg = '';
		if (!xtc_validate_email(trim($_POST['email']))) $err_msg .= ERROR_EMAIL;
		$info_smarty->assign('error_message', ERROR_MAIL . $err_msg);
		if ($err_msg != '') $error = true;
		//Wenn kein Fehler Email formatieren und absenden
		if (!$error) {

		 $minPrice=($products_price['plain']<$product->data['oAmount']||$product->data['oAmount']<1?$products_price['plain']:$product->data['oAmount']);
		 if($_POST['status']=='0'){
        xtc_db_query("update products set products_price = 0, LowestUsedprice = 0 where products_id = '".$product->data['products_id']."'");
        $minPrice=0;
		 }
		 xtc_db_query  ("INSERT INTO vormerkungen  (
										datum ,
										products_id ,
										ASIN,
										ISBN,
										min_price ,
										status,
									  email
										)
										VALUES (now(), '".$product->data['products_id']."', '".$product->data['ASIN']."', '".$product->data['ISBN']."', '$minPrice', '".$_POST['status']."', '".$_POST['email']."' )");
    $info_smarty->assign('success', '1');
		}
	}



	if (isset ($_GET['action']) && ($_GET['action'] == 'success')) {
		$info_smarty->assign('success', '1');
		$status = 1;
	} else {
		if (isset ($_SESSION['customer_id']) && !$error) {
			$email_address = stripslashes($c_data['customers_email_address']);
		}
		$info_smarty->assign('FORM_ACTION_CONTACT', xtc_draw_form('contact_us', xtc_href_link(FILENAME_PRODUCT_INFO, 'action=send&products_id='.(int) $_GET['products_id'], 'SSL')));
	  $info_smarty->assign('INPUT_EMAIL', xtc_draw_input_field('email', ($error ? $_POST['email'] : $email_address), 'style="width:350px"'));
		$info_smarty->assign('BUTTON_SUBMIT', xtc_image_submit('button_send.gif', IMAGE_BUTTON_SEND));
		$info_smarty->assign('FORM_END', '</form>');
		$info_smarty->assign('STATUS', xtc_draw_hidden_field('status', $status));
    $info_smarty->assign('status', $status);
	}


  if (isset($_GET['products_id']) && substr(basename($PHP_SELF), 0,8) != 'shopping')
	  include(DIR_WS_BOXES . 'eurobuch.php');


	$info_smarty->assign('language', $_SESSION['language']);
		$info_smarty->caching = 0;
		$product_info = $info_smarty->fetch(CURRENT_TEMPLATE.'/module/product_info/'.$product->data['product_template']);
		 /*
	// set cache ID
	 if (!CacheCheck()) {
		$info_smarty->caching = 0;
		$product_info = $info_smarty->fetch(CURRENT_TEMPLATE.'/module/product_info/'.$product->data['product_template']);
	} else {
		$info_smarty->caching = 1;
		$info_smarty->cache_lifetime = CACHE_LIFETIME;
		$info_smarty->cache_modified_check = CACHE_CHECK;
		$cache_id = $product->data['products_id'].$_SESSION['language'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'];
		$product_info = $info_smarty->fetch(CURRENT_TEMPLATE.'/module/product_info/'.$product->data['product_template'], $cache_id);
	}
	 */
}
$smarty->assign('main_content', $product_info);
?>
