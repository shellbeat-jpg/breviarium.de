<?php

/* -----------------------------------------------------------------------------------------
   $Id: product.php 1316 2005-10-21 15:30:58Z mz $ 

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2005 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(Coding Standards); www.oscommerce.com 

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class product {

	/**
	 * 
	 * Constructor
	 * 
	 */
	function product($pID = 0) {
		$this->pID = $pID;
// BOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
		//$this->useStandardImage=false;
		$this->useStandardImage=true;
// EOF - Tomcraft - 2009-10-30 - noimage.gif is displayed, when no image is defined
		$this->standardImage='noimage.gif';
// BOF - DokuMan - 2010-03-12 - bugfix, wrong comparison
		//if ($pID = 0) {
		if ($pID == 0) {
// EOF - DokuMan - 2010-03-12 - bugfix, wrong comparison
			$this->isProduct = false;
			return;
		}
		// query for Product
		$group_check = "";
		if (GROUP_CHECK == 'true') {
			$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		}

		$fsk_lock = "";
		if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
			$fsk_lock = ' and p.products_fsk18!=1';
		}

		$product_query = "select *, p.Title as products_name FROM ".TABLE_PRODUCTS." p
										                                      where p.products_status = '1'
										                                      and p.products_id = '".$this->pID."' ".$group_check.$fsk_lock;

		$product_query = xtc_db_query($product_query);

		if (!xtc_db_num_rows($product_query, true)) {
			$this->isProduct = false;
		} else {
			$this->isProduct = true;

			$db_fetch_array = xtc_db_fetch_array($product_query, false);
			$this->products_name=$db_fetch_array['products_name']   ;
			$this->ASIN=$db_fetch_array['ASIN']   ;
			$this->CHECKED = ($db_fetch_array['date_checked']==date("Y-m-d"));
			$this->data = $db_fetch_array;

    	if($this->CHECKED == false){
        $db_fetch_array['products_quantity'] = setOffersFromASIN($this->ASIN, $this->pID);
        

        $this->data['products_quantity']=$db_fetch_array['products_quantity'];
       # echo $db_fetch_array['products_quantity'];
			}

			$this->offers = $this->getOffers();
			$this->persons = $this->getPersons();
		}

	}

	/**
	 * 
	 *  Query for attributes count
	 * 
	 */

	function getAttributesCount() {

		$products_attributes_query = xtDBquery("select count(*) as total from ".TABLE_PRODUCTS_OPTIONS." popt, ".TABLE_PRODUCTS_ATTRIBUTES." patrib where patrib.products_id='".$this->pID."' and patrib.options_id = popt.products_options_id and popt.language_id = '".(int) $_SESSION['languages_id']."'");
		$products_attributes = xtc_db_fetch_array($products_attributes_query, true);
		return $products_attributes['total'];

	}

	/**
	 * 
	 * Query for reviews count
	 * 
	 */

	function getReviewsCount() {
		$reviews_query = xtDBquery("select count(*) as total from ".TABLE_REVIEWS." r, ".TABLE_REVIEWS_DESCRIPTION." rd where r.products_id = '".$this->pID."' and r.reviews_id = rd.reviews_id and rd.languages_id = '".$_SESSION['languages_id']."' and rd.reviews_text !=''");
		$reviews = xtc_db_fetch_array($reviews_query, true);
		return $reviews['total'];
	}

	/**
	 * 
	 * select reviews
	 * 
	 */

	function getPersons($products_id = 0) {
		$data_reviews = array ();
		$products_id >0?$pID=$products_id:$pID=$this->pID;
		$reviews_query = xtDBquery("SELECT DISTINCT creator, 	role
																FROM amazonCreator
																WHERE id_amazon = ".$pID."
																GROUP BY creator
																ORDER BY  role  ASC");

		if (xtc_db_num_rows($reviews_query, true)) {
			$row = 0;
			$data_reviews = array ();
			while ($reviews = xtc_db_fetch_array($reviews_query, true)) {
				$row ++;
				if(!isset($arrRoles[$reviews['role']]))
				  	$arrRoles[$reviews['role']] = array();
				if(!in_array($reviews['creator'], $arrRoles[$reviews['role']]))
				    $arrRoles[$reviews['role']][]=$reviews['creator'];
			}
			$keys = array_keys($arrRoles);
			for($i=0; $i < count($keys); $i++){
					$data_reviews[] = array ('role' => $keys[$i], 'creator' => join($arrRoles[$keys[$i]], ", "));
			}
		}
		return $data_reviews;
	}
	
	
	function getOffers() {
    global $xtPrice, $arrCondition;

		$data_reviews = array ();
		$reviews_query = xtc_db_query("SELECT   MerchantId, OfferListingId, ExchangeId, oAmount, oCurrencyCode, SubCondition, ConditionNote, date_added
																FROM amazonOffers
																WHERE id_amazon = ".$this->pID." AND EurobuchID                 = 0
																ORDER BY  oAmount  ASC");

		if (xtc_db_num_rows($reviews_query, true)) {
			$row = 0;
			$data_reviews = array ();
			while ($reviews = xtc_db_fetch_array($reviews_query, false)) {
				$row ++;
				$data_reviews[] = array ('BuyNowButton' => $this->getBuyNowButton($this->pID, $this->products_name, $reviews['OfferListingId'], $this->ASIN), 'ExchangeId' => $reviews['ExchangeId'], 'date_added' => substr($reviews['date_added'], 0, 10), 'OfferListingId' => $reviews['OfferListingId'], 'MerchantId' => $reviews['MerchantId'], 'oAmount' => $xtPrice->xtcGetPrice($this->pID, 1, 1, 2, $reviews['oAmount'], 1), 'oCurrencyCode' =>  $reviews['oCurrencyCode'] ,  'SubCondition' => $arrCondition[$reviews['SubCondition']],  'ConditionNote' => $reviews['ConditionNote']);
			}
		}

		return $data_reviews;
	}

	function getReviews() {
		$data_reviews = array ();
		$reviews_query = xtDBquery("select
									                                 r.reviews_rating,
									                                 r.reviews_id,
									                                 r.customers_name,
									                                 r.date_added,
									                                 r.last_modified,
									                                 r.reviews_read,
									                                 rd.reviews_text
									                                 from ".TABLE_REVIEWS." r,
									                                 ".TABLE_REVIEWS_DESCRIPTION." rd
									                                 where r.products_id = '".$this->pID."'
									                                 and  r.reviews_id=rd.reviews_id
									                                 and rd.languages_id = '".$_SESSION['languages_id']."'
									                                 order by reviews_id DESC");
		if (xtc_db_num_rows($reviews_query, true)) {
			$row = 0;
			$data_reviews = array ();
			while ($reviews = xtc_db_fetch_array($reviews_query, true)) {
				$row ++;
				$data_reviews[] = array ('AUTHOR' => $reviews['customers_name'], 'DATE' => xtc_date_short($reviews['date_added']), 'RATING' => xtc_image('templates/'.CURRENT_TEMPLATE.'/img/stars_'.$reviews['reviews_rating'].'.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])), 'TEXT' => $reviews['reviews_text']);
				if ($row == PRODUCT_REVIEWS_VIEW)
					break;
			}
		}
		return $data_reviews;
	}

	/**
	 * 
	 * return model if set, else return name
	 * 
	 */

	function getBreadcrumbModel() {

		if ($this->data['products_model'] != "")
			return $this->data['products_model'];
		return $this->data['products_name'];

	}

	/**
	 * 
	 * get also purchased products related to current
	 * 
	 */

	function getAlsoPurchased() {
		global $xtPrice;

		$module_content = array ();

		$fsk_lock = "";
		if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
			$fsk_lock = ' and p.products_fsk18!=1';
		}
		$group_check = "";
		if (GROUP_CHECK == 'true') {
			$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		}

		// BOF - vr - 2010-04-21 make sql human readable, update to SQL-92-Standard
		$orders_query = "select p.products_fsk18, p.products_id, p.products_price, p.products_tax_class_id,
							 p.products_image, pd.products_name, p.products_vpe, p.products_vpe_status,
							 p.products_vpe_value, pd.products_short_description
						from ".TABLE_ORDERS_PRODUCTS." op1 
						join ".TABLE_ORDERS_PRODUCTS." op2 on op2.orders_id = op1.orders_id
						join ".TABLE_ORDERS." o on o.orders_id = op2.orders_id
						join ".TABLE_PRODUCTS." p on p.products_id = op2.products_id 
						join ".TABLE_PRODUCTS_DESCRIPTION." pd on pd.products_id = op2.products_id
						where op1.products_id = '".$this->pID."'
						and op2.products_id != '".$this->pID."'
						and p.products_status = '1'
						and pd.language_id = '".(int) $_SESSION['languages_id']."'
						".$group_check."
						".$fsk_lock."
						group by p.products_id 
						order by o.date_purchased desc 
						limit ".MAX_DISPLAY_ALSO_PURCHASED;
		// EOF - vr - 2010-04-21 make sql human readable
		$orders_query = xtDBquery($orders_query);
		while ($orders = xtc_db_fetch_array($orders_query, true)) {

			$module_content[] = $this->buildDataArray($orders);

		}

		return $module_content;

	}

	/**
	 * 
	 * 
	 *  Get Cross sells 
	 * 
	 * 
	 */
	function getCrossSells() {
		global $xtPrice;

		$cs_groups = "SELECT products_xsell_grp_name_id FROM ".TABLE_PRODUCTS_XSELL." WHERE products_id = '".$this->pID."' GROUP BY products_xsell_grp_name_id";
		$cs_groups = xtDBquery($cs_groups);
		$cross_sell_data = array ();
		if (xtc_db_num_rows($cs_groups, true)>0) {
		while ($cross_sells = xtc_db_fetch_array($cs_groups, true)) {

			$fsk_lock = '';
			if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
				$fsk_lock = ' and p.products_fsk18!=1';
			}
			$group_check = "";
			if (GROUP_CHECK == 'true') {
				$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
			}

				$cross_query = "select p.*, xp.sort_order from ".TABLE_PRODUCTS_XSELL." xp, ".TABLE_PRODUCTS." p
																								                                            where xp.products_id = '".$this->pID."' and xp.xsell_id = p.products_id ".$fsk_lock.$group_check."
																								                                              and xp.products_xsell_grp_name_id='".$cross_sells['products_xsell_grp_name_id']."'

																								                                            and p.products_status = '1'
																								                                            order by xp.sort_order asc";

			$cross_query = xtDBquery($cross_query);
			if (xtc_db_num_rows($cross_query, true) > 0)
				$cross_sell_data[$cross_sells['products_xsell_grp_name_id']] = array ('GROUP' => xtc_get_cross_sell_name($cross_sells['products_xsell_grp_name_id']), 'PRODUCTS' => array ());

			while ($xsell = xtc_db_fetch_array($cross_query, true)) {

				$cross_sell_data[$cross_sells['products_xsell_grp_name_id']]['PRODUCTS'][] = $this->buildDataArray($xsell);
			}

		}
		return $cross_sell_data;
		}
	}
	
	
	/**
	 * 
	 * get reverse cross sells
	 * 
	 */
	 
	 function getReverseCrossSells() {
	 			global $xtPrice;


			$fsk_lock = '';
			if ($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
				$fsk_lock = ' and p.products_fsk18!=1';
			}
			$group_check = '';
			if (GROUP_CHECK == 'true') {
				$group_check = " and p.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
			}

			$cross_query = xtDBquery("select p.*,  xp.sort_order from ".TABLE_PRODUCTS_XSELL." xp, ".TABLE_PRODUCTS." p
																                                            where xp.xsell_id = '".$this->pID."' and xp.products_id = p.products_id ".$fsk_lock.$group_check."

																                                            and p.products_status = '1'
																                                            order by xp.sort_order asc");

        $cross_sell_data = array(); //DokuMan - 2010-03-12 - set undefined array

			while ($xsell = xtc_db_fetch_array($cross_query, true)) {

				$cross_sell_data[] = $this->buildDataArray($xsell);
			}


		return $cross_sell_data;
	 	
	 	
	 	
	 }
	

	function getGraduated() {
		global $xtPrice;
		
		$discount = $xtPrice->xtcCheckDiscount($this->pID);	// Hetfield - 2010-03-15 - BUGFIX show VPE with discount for graduated prices	
		$staffel_query = xtDBquery("SELECT
				                                     quantity,
				                                     personal_offer
				                                     FROM
				                                     ".TABLE_PERSONAL_OFFERS_BY.(int) $_SESSION['customers_status']['customers_status_id']."
				                                     WHERE
				                                     products_id = '".$this->pID."'
				                                     ORDER BY quantity ASC");

		$staffel = array ();
		while ($staffel_values = xtc_db_fetch_array($staffel_query, true)) {
			$staffel[] = array ('stk' => $staffel_values['quantity'], 'price' => $staffel_values['personal_offer']);
		}		
		
		$staffel_data = array ();
		for ($i = 0, $n = sizeof($staffel); $i < $n; $i ++) {
			//BOF - web28 - 2010-07-13 - BUGFIX display same quantity only once for graduated prices / FIX max value info for graduated prices
			/*
			if ($staffel[$i]['stk'] == 1) {			    
				$quantity = $staffel[$i]['stk'];				
				if ($staffel[$i +1]['stk'] != '')
					$quantity = $staffel[$i]['stk'].'-'. ($staffel[$i +1]['stk'] - 1);
			} else {			    
				$quantity = ' > '.$staffel[$i]['stk'];				
				if ($staffel[$i +1]['stk'] != '')
					$quantity = $staffel[$i]['stk'].'-'. ($staffel[$i +1]['stk'] - 1);
			}
			*/
            if ($staffel[$i]['stk'] == 1 || $staffel[$i +1]['stk'] != ''){
                $quantity = $staffel[$i]['stk'];
                if ($staffel[$i +1]['stk'] != '' && $staffel[$i +1]['stk'] != $staffel[$i]['stk'] + 1)
                    $quantity .= ' - '. ($staffel[$i +1]['stk'] - 1);
            } else {
                $quantity = GRADUATED_PRICE_MAX_VALUE.' '.$staffel[$i]['stk'];
            }
            //EOF - web28 - 2010-07-13 - BUGFIX display same quantity only once for graduated prices	/FIX max value info for graduated prices		
			$vpe = '';
			// BOF - Hetfield - 2009-08-24 - BUGFIX show VPE for graduated prices
			if ($this->data['products_vpe_status'] == 1 && $this->data['products_vpe_value'] != 0.0 && $staffel[$i]['price'] > 0) {
				$vpe = $staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount;
				$vpe = $vpe * (1 / $this->data['products_vpe_value']);
				$vpe = BASICPRICE_VPE_TEXT.$xtPrice->xtcFormat($vpe, true, $this->data['products_tax_class_id']).TXT_PER.xtc_get_vpe_name($this->data['products_vpe']);
			}
			// EOF - Hetfield - 2009-08-24 - BUGFIX show VPE for graduated prices
			$staffel_data[$i] = array ('QUANTITY' => $quantity, 'VPE' => $vpe, 'PRICE' => $xtPrice->xtcFormat($staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount, true, $this->data['products_tax_class_id']));
		}

		return $staffel_data;

	}
	/**
	 * 
	 * valid flag
	 * 
	 */

	function isProduct() {
		return $this->isProduct;
	}
	
	// beta
	function getBuyNowButton($id, $name, $OfferListingId = '', $ASIN = '') {
		global $PHP_SELF;
		if($OfferListingId!=''){
      return '<a rel="nofollow" href="'.xtc_href_link(basename($PHP_SELF), 'action=buy_now&BUYproducts_id='.$id.'&'.xtc_get_all_get_params(array ('action')), 'NONSSL').'&OfferListingId='.$OfferListingId.'&ASIN='.$ASIN.'">'.xtc_image_button('button_in_cart_small.gif', TEXT_BUY.$name.TEXT_NOW).'</a>';
		}else{
			$reviews_query = xtDBquery("SELECT DISTINCT MerchantId, OfferListingId, ExchangeId, oAmount, oCurrencyCode, SubCondition, ConditionNote, date_added
																	FROM amazonOffers
																	WHERE id_amazon = $id AND EurobuchID  = 0
																	GROUP BY MerchantId
																	ORDER BY  oAmount  ASC");

			if (xtc_db_num_rows($reviews_query, true)) {
	        $reviews = xtc_db_fetch_array($reviews_query, true);
	        return '<a rel="nofollow" href="'.xtc_href_link(basename($PHP_SELF), 'action=buy_now&BUYproducts_id='.$id.'&'.xtc_get_all_get_params(array ('action')), 'NONSSL').'&OfferListingId='.$reviews['OfferListingId'].'&ASIN='.$ASIN.'">'.xtc_image_button('button_in_cart_small.gif', TEXT_BUY.$name.TEXT_NOW).'</a>';
  		}
		}
	}

	function getVPEtext($product, $price) {
		global $xtPrice;

		require_once (DIR_FS_INC.'xtc_get_vpe_name.inc.php');

		if (!is_array($product))
			$product = $this->data;

		if ($product['products_vpe_status'] == 1 && $product['products_vpe_value'] != 0.0 && $price > 0) {
			return $xtPrice->xtcFormat($price * (1 / $product['products_vpe_value']), true).TXT_PER.xtc_get_vpe_name($product['products_vpe']);
		}

		return;

	}
	
	function buildDataArray(&$array,$image='thumbnail') {
		global $xtPrice,$main;

			$tax_rate = $xtPrice->TAX[$array['products_tax_class_id']];

			$products_price = $xtPrice->xtcGetPrice($array['products_id'], $format = true, 1, $array['products_tax_class_id'], $array['products_price'], 1);
			#if($array['products_quantity']<1)
			#   $products_price['formated']=="--";
     #  var_dump($array);
      
			$buy_now = ''; //DokuMan: Undefined variable: buy_now

			if ($_SESSION['customers_status']['customers_status_show_price'] != '0') {
        if ($_SESSION['customers_status']['customers_fsk18'] == '1') {
          if (isset($array['products_fsk18']) && $array['products_fsk18'] == '0')
            $buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name'], '',  $array['ASIN']);
        
        } else {
          $buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name'], '',  $array['ASIN']);
        }
			}
		#	var_dump($this->offers);
     # $this->offers[0]['BuyNowButton']
      //BOF - DokuMan - 2010-02-26 - Set Undefined index: products_shippingtime
			//$shipping_status_name = $main->getShippingStatusName($array['products_shippingtime']);
			//$shipping_status_image = $main->getShippingStatusImage($array['products_shippingtime']);
        if (isset($array['products_shippingtime'])) {
            $shipping_status_name = $main->getShippingStatusName($array['products_shippingtime']);
            $shipping_status_image = $main->getShippingStatusImage($array['products_shippingtime']);
        } else {
            $shipping_status_name = '';
            $shipping_status_image = '';
        }



			 #echo $array['SmallImage']!=''?$array['SmallImage']:$this->productImage($array['products_image'], DIR_WS_POPUP_IMAGES);
      //EOF - DokuMan - 2010-02-26 - Set Undefined index: products_shippingtime


	$arrTemp=array (
    'PRODUCTS_SAVED' => $array['products_last_modified']!= '0000-00-00 00:00:00'?$this->xtc_date_short($array['products_last_modified']):"x",

		    'PRODUCTS_NumberOfPages' => $array['NumberOfPages'],
		    'PRODUCTS_EditorialSource' => $array['EditorialSource'],
		    'PRODUCTS_EditorialReview' => $array['EditorialReview'],
		    'PRODUCTS_Edition' => $array['Edition'],
		    'PRODUCTS_ISBN' => $array['ISBN'],
		    'PRODUCTS_EAN' => $array['EAN'],
		    'PRODUCTS_OfferListing' => $array['OfferListing'],
		    'PRODUCTS_CurrencyCode' => $array['CurrencyCode'],
		    'PRODUCTS_ListPrice' => $array['ListPrice'],
		    'PRODUCTS_LowestNewPrice' => $array['LowestNewPrice'],
		    'PRODUCTS_LowestUsedprice' => $array['LowestUsedprice'],
		    'PRODUCTS_LowestCollectiblePrice' => $array['LowestCollectiblePrice'],
		    'PRODUCTS_TotalUsed' => $array['TotalUsed'],
		    'PRODUCTS_TotalNew' => $array['TotalNew'],
		    'PRODUCTS_TotalCollectible' => $array['TotalCollectible'],
		    #'PRODUCTS_Offers' =>  $this->getOffers(),
		    'PRODUCTS_Persons' =>  $this->getPersons($array['products_id']),
				'PRODUCTS_NAME' => $array['Title'],
				 'PRODUCTS_Author' => $array['Author'],
				 'PRODUCTS_Publisher' => $array['Publisher'],
				# 'PRODUCTS_PublicationDate' => $array['PublicationDate']!='0000-00-00 00:00:00'?$array['PublicationDate']:'',
				 'PRODUCTS_Binding' => $array['Binding'],
       #  'PRODUCTS_DetailPageURL' => $array['DetailPageURL'],
         'PRODUCTS_DetailPageURL' => 'http://www.amazon.de/gp/offer-listing/'.$array['ASIN'].'/ref=dp_olp_0?ie=UTF8&condition=all&tag=breviarium-21',
				 'PRODUCTS_ASIN' => $array['ASIN'],
				 'PRODUCTS_AddToCartUrl' => $array['AddToCartUrl'],
				 'PRODUCTS_MerchantId' => $array['MerchantId'],
				 'PRODUCTS_SubCondition' => $array['SubCondition'],
				 'PRODUCTS_ConditionNote' => $array['ConditionNote'],
				'COUNT'=>$array['ID'],
				'PRODUCTS_ID'=>$array['products_id'],
				'PRODUCTS_MODEL'=>$array['products_model'],
				'PRODUCTS_VPE' => $this->getVPEtext($array, $products_price['plain']),
				'PRODUCTS_IMAGE' => $array['SmallImage']!=''?$array['SmallImage']:$this->productImage($array['products_image'], DIR_WS_POPUP_IMAGES),
        'PRODUCTS_IMAGE_MED' => $array['MediumImage']!=''&&$array['MediumImage']!='1'?$array['MediumImage']:$this->productImage($array['products_image'], DIR_WS_POPUP_IMAGES),
				'PRODUCTS_LINK' => xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($array['products_id'], $array['Title'])),
				'PRODUCTS_PRICE' =>  $products_price['formated'],
				 'MIN_PRICE' => $xtPrice->xtcFormat($array['minPrice'], true, $this->data['products_tax_class_id']),
				'PRODUCTS_TAX_INFO' => $main->getTaxInfo($tax_rate),
				'PRODUCTS_SHIPPING_LINK' => $main->getShippingLink(),
				'PRODUCTS_BUTTON_BUY_NOW' => $buy_now,
				'PRODUCTS_SHIPPING_NAME'=>$shipping_status_name,
				'PRODUCTS_SHIPPING_IMAGE'=>$shipping_status_image,

				//'PRODUCTS_DESCRIPTION' => $array['products_description'],
        'PRODUCTS_DESCRIPTION' => isset($array['products_description']) ? $array['products_description'] : '', //DokuMan - 2010-02-26 - set Undefined index

        //BOF - Tomcraft - 2010-07-15 - Added PRODUCTS_QUANTITY for further use in template
        'PRODUCTS_QUANTITY' => (int)$array['products_quantity'],
        //EOF - Tomcraft - 2010-07-15 - Added PRODUCTS_QUANTITY for further use in template

				//'PRODUCTS_EXPIRES' => $array['expires_date'],
        'PRODUCTS_EXPIRES' => isset($array['expires_date']) ? $array['expires_date'] : 0, //DokuMan - 2010-02-26 - set Undefined index

				//'PRODUCTS_CATEGORY_URL'=>$array['cat_url'],
        'PRODUCTS_CATEGORY_URL' => isset($array['cat_url']) ? $array['cat_url'] : '', //DokuMan - 2010-02-26 - set Undefined index

				//'PRODUCTS_SHORT_DESCRIPTION' => $array['products_short_description'],
				'PRODUCTS_SHORT_DESCRIPTION' => isset($array['products_short_description']) ? $array['products_short_description'] : '', //DokuMan - 2010-02-26 - set Undefined index

				//'PRODUCTS_FSK18' => $array['products_fsk18']);
				'PRODUCTS_FSK18' => isset($array['products_fsk18']) ? $array['products_fsk18'] : 0, //DokuMan - 2010-02-26 - set Undefined index
        );

  return  $arrTemp;


					#echo $array['SmallImage']!=''&&$array['SmallImage']!='1'?$array['SmallImage']:$this->productImage($array['products_image'])     ;
	}
	
  function xtc_date_short($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || empty($raw_date) ) return false;

    $year = substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
      return date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
    } else {
      return preg_replace('/2037' . '$/', $year, date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, 2037))); // Hetfield - 2009-08-19 - replaced deprecated function ereg_replace with preg_replace to be ready for PHP >= 5.3
    }
  }
  
	function productImage($name, $type) {
	    $path = DIR_WS_THUMBNAIL_IMAGES;
	    switch ($type) {
			case 'info' :
				$path = DIR_WS_THUMBNAIL_IMAGES;
				break;
			case 'thumbnail' :
				$path = DIR_WS_THUMBNAIL_IMAGES;
				break;
			case 'popup' :
				$path = DIR_WS_POPUP_IMAGES;
				break;
		}

		// BOF - vr - 2010-04-09 no distinction between "name is null" and "name == ''"
		// if ($name == '')) {
		if (empty($name)) {
		// EOF - vr - 2010-04-09 no distinction between "name is null" and "name == ''"
// BOF - Tomcraft - 2009-11-12 - noimage.gif is displayed, when no image is defined
			//if ($this->useStandardImage == 'true' && $this->standardImage != '') // comment in when "noimage.gif" should be displayed when there is no image defined in the database
			//	return $path.$this->standardImage; // comment in when "noimage.gif" should be displayed when there is no image defined in the database
			return $name; // comment out when "noimage.gif" should be displayed when there is no image defined in the database
// EOF - Tomcraft - 2009-11-12 - noimage.gif is displayed, when no image is defined
		} else {
			// check if image exists
			if (!file_exists($path.$name)) {
				if ($this->useStandardImage == 'true' && $this->standardImage != '')
					$name = $this->standardImage;
			}
			return $path.$name;
		}
	}
	
}
?>
