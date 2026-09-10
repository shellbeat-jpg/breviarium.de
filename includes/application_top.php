<?php

/* -----------------------------------------------------------------------------------------
   $Id: application_top.php 1323 2005-10-27 17:58:08Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_top.php,v 1.273 2003/05/19); www.oscommerce.com
   (c) 2003	 nextcommerce (application_top.php,v 1.54 2003/08/25); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Add A Quickie v1.0 Autor  Harald Ponce de Leon

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
// start the timer for the page parse time log
define('PAGE_PARSE_START_TIME', microtime());
# D.L. 11/2022
define('DB_CACHE', false);

// set the level of error reporting

error_reporting(E_ALL);

// Set the local configuration parameters - mainly for developers - if exists else the mainconfigure
if (file_exists('includes/local/configure.php')) {
	include ('includes/local/configure.php');
} else {
	include ('includes/configure.php');
}
require_once (DIR_FS_INC . 'auto_include.inc.php');

 	#var_dump($_GET);
 	#exit;
// BOF - Tomcraft - 2009-11-08 - FIX for PHP5.3 date_default_timezone_set
  if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
	date_default_timezone_set('Europe/Berlin');
  }
// EOF - Tomcraft - 2009-11-08 - FIX for PHP5.3 date_default_timezone_set

$php4_3_10 = (0 == version_compare(phpversion(), "4.3.10"));
define('PHP4_3_10', $php4_3_10);
// define the project version
define('PROJECT_VERSION', 'xtcModified');

// BOF - Tomcraft - 2009-11-09 - Added missing definition for TAX_DECIMAL_PLACES
define('TAX_DECIMAL_PLACES', 0);
// EOF - Tomcraft - 2009-11-09 - Added missing definition for TAX_DECIMAL_PLACES

// set the type of request (secure or not)
//BOF - DokuMan - 2010-03-03 - added native support for SSL-proxy connections
//$request_type = (getenv('HTTPS') == '1' || getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';
$request_type = (getenv('HTTPS') == '1' || getenv('HTTPS') == 'on' || !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) ? 'SSL' : 'NONSSL';
//EOF - DokuMan - 2010-03-03 - added native support for SSL-proxy connections

// set php_self in the local scope
$PHP_SELF = $_SERVER['PHP_SELF'];
//--- SHOPSTAT -------------------------//
if (preg_match("/\.html$/",$PHP_SELF) )
    {
    if(!preg_match("/\.html$/",$_SERVER['SCRIPT_NAME']))
        {
        $PHP_SELF = $_SERVER['SCRIPT_NAME'];
        }
    elseif(!preg_match("/\.html$/",$_SERVER['SCRIPT_FILENAME']))
        {
        $PHP_SELF = $_SERVER['SCRIPT_FILENAME'];
        }
    }
//--- SHOPSTAT -------------------------//

// include the list of project filenames
require (DIR_WS_INCLUDES.'filenames.php');

// include the list of project database tables
require (DIR_WS_INCLUDES.'database_tables.php');

// SQL caching dir
define('SQL_CACHEDIR', DIR_FS_CATALOG.'cache/');

// Below are some defines which affect the way the discount coupon/gift voucher system work
// Be careful when editing them.
//
// Set the length of the redeem code, the longer the more secure
//define('SECURITY_CODE_LENGTH', '10');
//
// The settings below determine whether a new customer receives an incentive when they first signup
//
// Set the amount of a Gift Voucher that the new signup will receive, set to 0 for none
//  define('NEW_SIGNUP_GIFT_VOUCHER_AMOUNT', '10');  // placed in the admin configuration mystore
//
// Set the coupon ID that will be sent by email to a new signup, if no id is set then no email :)
//  define('NEW_SIGNUP_DISCOUNT_COUPON', '3'); // placed in the admin configuration mystore

// Store DB-Querys in a Log File
//BOF - DokuMan - 2010-02-25 - Constant STORE_DB_TRANSACTIONS already defined in DB
//define('STORE_DB_TRANSACTIONS', 'false');
//EOF - DokuMan - 2010-02-25 - Constant STORE_DB_TRANSACTIONS already defined in DB

// graduated prices model or products assigned ?
define('GRADUATED_ASSIGN', 'true');

require_once (DIR_FS_INC.'PasswordHash.php');
require_once (DIR_FS_INC.'xtc_bots.inc.php');

// include used functions
require_once (DIR_FS_INC.'xtc_amazon.inc.php');
require_once (DIR_FS_INC.'xtc_eurobuch.inc.php');
// Database
require_once (DIR_FS_INC.'db_functions_'.DB_MYSQL_TYPE.'.inc.php');
require_once (DIR_FS_INC.'db_functions.inc.php');
require_once (DIR_FS_INC.'xtc_get_top_level_domain.inc.php');


// html basics
require_once (DIR_FS_INC.'xtc_href_link.inc.php');
require_once (DIR_FS_INC.'xtc_php_mail.inc.php');

require_once (DIR_FS_INC.'xtc_product_link.inc.php');
require_once (DIR_FS_INC.'xtc_category_link.inc.php');
require_once (DIR_FS_INC.'xtc_manufacturer_link.inc.php');

// html functions
require_once (DIR_FS_INC.'xtc_draw_checkbox_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_form.inc.php');
require_once (DIR_FS_INC.'xtc_draw_hidden_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_input_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_password_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_pull_down_menu.inc.php');
require_once (DIR_FS_INC.'xtc_draw_radio_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_selection_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_separator.inc.php');
require_once (DIR_FS_INC.'xtc_draw_textarea_field.inc.php');
require_once (DIR_FS_INC.'xtc_image_button.inc.php');

require_once (DIR_FS_INC.'xtc_not_null.inc.php');
require_once (DIR_FS_INC.'xtc_update_whos_online.inc.php');
require_once (DIR_FS_INC.'xtc_activate_banners.inc.php');
require_once (DIR_FS_INC.'xtc_expire_banners.inc.php');
require_once (DIR_FS_INC.'xtc_expire_specials.inc.php');
require_once (DIR_FS_INC.'xtc_parse_category_path.inc.php');
require_once (DIR_FS_INC.'xtc_get_product_path.inc.php');

require_once (DIR_FS_INC.'xtc_get_category_path.inc.php');

require_once (DIR_FS_INC.'xtc_get_parent_categories.inc.php');
require_once (DIR_FS_INC.'xtc_redirect.inc.php');
require_once (DIR_FS_INC.'xtc_get_uprid.inc.php');
require_once (DIR_FS_INC.'xtc_get_all_get_params.inc.php');
require_once (DIR_FS_INC.'xtc_has_product_attributes.inc.php');
require_once (DIR_FS_INC.'xtc_image.inc.php');
require_once (DIR_FS_INC.'xtc_check_stock_attributes.inc.php');
require_once (DIR_FS_INC.'xtc_currency_exists.inc.php');
require_once (DIR_FS_INC.'xtc_remove_non_numeric.inc.php');
require_once (DIR_FS_INC.'xtc_get_ip_address.inc.php');
require_once (DIR_FS_INC.'xtc_setcookie.inc.php');
require_once (DIR_FS_INC.'xtc_check_agent.inc.php');
require_once (DIR_FS_INC.'xtc_count_cart.inc.php');
require_once (DIR_FS_INC.'xtc_get_qty.inc.php');
require_once (DIR_FS_INC.'create_coupon_code.inc.php');
require_once (DIR_FS_INC.'xtc_gv_account_update.inc.php');
require_once (DIR_FS_INC.'xtc_get_tax_rate_from_desc.inc.php');
require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');
require_once (DIR_FS_INC.'xtc_add_tax.inc.php');
require_once (DIR_FS_INC.'xtc_cleanName.inc.php');
require_once (DIR_FS_INC.'xtc_calculate_tax.inc.php');
require_once (DIR_FS_INC.'xtc_input_validation.inc.php');
require_once (DIR_FS_INC.'xtc_js_lang.php');

foreach(auto_include(DIR_FS_CATALOG.'includes/extra/functions/','php') as $file) require_once ($file);

// make a connection to the database... now
xtc_db_connect() or die('Unable to connect to database server!');
#if(strpos($_SERVER['REQUEST_URI'], "mobile")>0)
#	error_reporting(E_ALL & ~E_NOTICE);
$configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from '.TABLE_CONFIGURATION);
#while ($configuration = xtc_db_fetch_array($configuration_query)) {
#	define($configuration['cfgKey'], $configuration['cfgValue']);
#}
# MOBILE VERSION
while ($configuration = xtc_db_fetch_array($configuration_query)) {
	if($configuration['cfgKey']=="CURRENT_TEMPLATE") {
		$template = $configuration['cfgValue'];
	} else {
		define($configuration['cfgKey'], $configuration['cfgValue']);
	}
}
# MOBILE VERSION

// Set the length of the redeem code, the longer the more secure
// Kommt eigentlich schon aus der Table configuration
if(SECURITY_CODE_LENGTH=='')
  define('SECURITY_CODE_LENGTH', '10');

require_once (DIR_WS_CLASSES.'class.phpmailer.php');
if (EMAIL_TRANSPORT == 'smtp')
	require_once (DIR_WS_CLASSES.'class.smtp.php');
require_once (DIR_FS_INC.'xtc_Security.inc.php');


// set the application parameters



function CacheCheck() {   
    # D.L. 11/2022  
    return false;

	if (USE_CACHE == 'false') return false;
	if (!isset($_COOKIE['XTCsid'])) return false;
	return true;
}

// if gzip_compression is enabled, start to buffer the output
if ((GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded = extension_loaded('zlib')) && (PHP_VERSION >= '4')) {
	if (($ini_zlib_output_compression = (int) ini_get('zlib.output_compression')) < 1) {
		ob_start('ob_gzhandler');
	} else {
		ini_set('zlib.output_compression_level', GZIP_LEVEL);
	}
}
//--- SHOPSTAT -------------------------//
/*
// set the HTTP GET parameters manually if search_engine_friendly_urls is enabled
if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
// BOF - Tomcraft - 2009-10-25 - made capable for 1und1
	$pathinfo=((getenv('PATH_INFO')=='')?$_SERVER['ORIG_PATH_INFO']:getenv('PATH_INFO'));
// BOF - Tomcraft - 2009-10-25 - replaced deprecated function ereg with preg_match
//	if(ereg('.php',$pathinfo)):
	if(preg_match('/.php/',$pathinfo)):
// EOF - Tomcraft - 2009-10-25 - replaced deprecated function ereg with preg_match
		$PATH_INFO = substr(stristr('.php', $pathinfo),1);
	else:
		$PATH_INFO=$pathinfo;
	endif;
// EOF - Tomcraft - 2009-10-25 - made capable for 1und1
	if (strlen(getenv('PATH_INFO')) > 1) {
		$GET_array = array ();
		$PHP_SELF = str_replace(getenv('PATH_INFO'), '', $PHP_SELF);
		$vars = explode('/', substr(getenv('PATH_INFO'), 1));
		for ($i = 0, $n = sizeof($vars); $i < $n; $i ++) {
			if (strpos($vars[$i], '[]')) {
				$GET_array[substr($vars[$i], 0, -2)][] = $vars[$i +1];
			} else {
// BOF - Tomcraft - 2009-06-03 - fix magic quotes security issue
//                                $_GET[$key] = $value;
				$_GET[$vars[$i]] = htmlspecialchars($vars[$i +1]);
				if(get_magic_quotes_gpc()) $_GET[$vars[$i]] = addslashes($_GET[$vars[$i]]); // security Patch 20.11.2008
// EOF - Tomcraft - 2009-06-03 - fix magic quotes security issue
			}
			$i ++;
		}

		if (sizeof($GET_array) > 0) {
			while (list ($key, $value) = each($GET_array)) {
				$_GET[$key] = htmlspecialchars($value);
// BOF - Tomcraft - 2009-06-03 - fix magic quotes security issue
//                                $_GET[$key] = $value;
				if(get_magic_quotes_gpc()) $_GET[$key] = addslashes($_GET[$key]); // security Patch 20.11.2008
// EOF - Tomcraft - 2009-06-03 - fix magic quotes security issue
			}
		}
	}
}
*/
//--- SHOPSTAT -------------------------//

function base64url_encode($data) {
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

// check GET/POST/COOKIE VARS
require (DIR_WS_CLASSES.'class.inputfilter.php');
$InputFilter = new InputFilter();

// BOF - Hetfield - 2009-08-16 - correct inputfilter security-patch and remove double replacing
//$_GET = $InputFilter->process($_GET, true);
//$_POST = $InputFilter->process($_POST);
#$_GET = $InputFilter->process($_GET);
$_POST = $InputFilter->process($_POST);
$_REQUEST = $InputFilter->process($_REQUEST);
#$_GET = $InputFilter->safeSQL($_GET);
$_POST = $InputFilter->safeSQL($_POST);
$_REQUEST = $InputFilter->safeSQL($_REQUEST);


// EOF - Hetfield - 2009-08-16 - correct inputfilter security-patch and remove double replacing
 # var_dump($_GET);

if(isset($_GET['list']))
  $_GET['list']= substr($_GET['list'], 0, 1);
if(isset($_GET['page']))
	$_GET['page'] = (int) $_GET['page'];
if(isset($_GET['author']))
	$_GET['author'] = substr($_GET['author'], 0, 1);
if(isset($_GET['pub']))
	$_GET['pub'] = (int) $_GET['pub'];
if(isset($_GET['publisher']))
	$_GET['publisher'] = (int) $_GET['publisher'];
if(isset($_GET['foreword']))
	$_GET['foreword'] = (int) $_GET['foreword'];
if(isset($_GET['illustrator']))
	$_GET['illustrator'] = (int) $_GET['illustrator'];
if(isset($_GET['photographer']))
	$_GET['photographer'] = (int) $_GET['photographer'];
if(isset($_GET['coID']))
	$_GET['coID'] = (int) $_GET['coID'];
if(isset($_GET['products_id']))
	$_GET['products_id'] = (int) $_GET['products_id'];


// set the top level domains
$http_domain = xtc_get_top_level_domain(HTTP_SERVER);
$https_domain = xtc_get_top_level_domain(HTTPS_SERVER);
$current_domain = (($request_type == 'NONSSL') ? $http_domain : $https_domain);

// include shopping cart class
require (DIR_WS_CLASSES.'shopping_cart.php');

// include navigation history class
require (DIR_WS_CLASSES.'navigation_history.php');

// some code to solve compatibility issues
#require (DIR_WS_FUNCTIONS.'compatibility.php');

// define how the session functions will be used
require (DIR_WS_FUNCTIONS.'sessions.php');


if(isset ($_GET['target']) && $_SESSION['customer_id'] != '0' && (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'spider')===false)  && (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'seeker')===false)  && (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'crawler')===false)  && (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'bot')===false)  && (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'wget')===false)){
 $URL_=$_GET['target'];
 $pID = $_GET['pID'];
 $URL_ = base64url_decode($URL_);
 $URL_ = str_replace("~", "&", $URL_);
 xtc_db_query("Insert into affiliates (pID, datum, url) values('$pID', now(), '$URL_')");
 xtc_redirect($URL_);
}
// set the session name and save path
session_name('XTCsid');
if (STORE_SESSIONS != 'mysql') session_save_path(SESSION_WRITE_DIRECTORY);

// set the session cookie parameters
if (function_exists('session_set_cookie_params')) {
	session_set_cookie_params(0, '/', (xtc_not_null($current_domain) ? '.'.$current_domain : ''));
}
elseif (function_exists('ini_set')) {
	ini_set('session.cookie_lifetime', '0');
	ini_set('session.cookie_path', '/');
	ini_set('session.cookie_domain', (xtc_not_null($current_domain) ? '.'.$current_domain : ''));
}

// set the session ID if it exists
if (isset ($_POST[session_name()])) {
	session_id($_POST[session_name()]);
}
elseif (($request_type == 'SSL') && isset ($_GET[session_name()])) {
	session_id($_GET[session_name()]);
}


// start the session
$session_started = false;
if (SESSION_FORCE_COOKIE_USE == 'True') {
	xtc_setcookie('cookie_test', 'please_accept_for_session', time() + 60 * 60 * 24 * 30, '/', $current_domain);

	if (isset ($_COOKIE['cookie_test'])) {
		session_start();
		include (DIR_WS_INCLUDES.'tracking.php');
		$session_started = true;
	}
} else {
	session_start();
	include (DIR_WS_INCLUDES.'tracking.php');
	$session_started = true;
}


# MOBILE VERSION
if(strpos($_SERVER['REQUEST_URI'], "tpl/mobile/")>0){
  $_GET["tpl"]="mobile";
  $_SESSION["tpl"] = $_GET["tpl"];
}elseif(strpos($_SERVER['REQUEST_URI'], "tpl/clear/")>0){
  $_GET["tpl"]="clear";
  $_SESSION["tpl"] = $_GET["tpl"];
}

if(!isset($_SESSION["tpl"]) && preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $_SERVER['HTTP_USER_AGENT'])||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($_SERVER['HTTP_USER_AGENT'],0,4)))
	header('Location: http://buch.breviarium.de/index.php?tpl=mobile');

// check the Agent
if(isset($_GET["tpl"])) {
	$_SESSION["tpl"] = $_GET["tpl"];
}
if(isset($_SESSION["tpl"]) && file_exists(DIR_FS_CATALOG."templates/".$_SESSION["tpl"])) {
	define("CURRENT_TEMPLATE", $_SESSION["tpl"]);
} else {
	define("CURRENT_TEMPLATE", $template);
}
# MOBILE VERSION
# echo CURRENT_TEMPLATE;
$truncate_session_id = false;
if (CHECK_CLIENT_AGENT) {
	if (xtc_check_agent() == 1) {
		$truncate_session_id = true;
	}
}

// verify the ssl_session_id if the feature is enabled
if (($request_type == 'SSL') && (SESSION_CHECK_SSL_SESSION_ID == 'True') && (ENABLE_SSL == true) && ($session_started == true)) {
	$ssl_session_id = getenv('SSL_SESSION_ID');
	if (!isset($_SESSION['SSL_SESSION_ID'])) { // Hetfield - 2009-08-19 - removed deprecated function session_is_registered to be ready for PHP >= 5.3
		$_SESSION['SESSION_SSL_ID'] = $ssl_session_id;
	}

	if ($_SESSION['SESSION_SSL_ID'] != $ssl_session_id) {
		session_destroy();
		xtc_redirect(xtc_href_link(FILENAME_SSL_CHECK));
	}
}

// verify the browser user agent if the feature is enabled
if (SESSION_CHECK_USER_AGENT == 'True') {
	$http_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	$http_user_agent2 = strtolower(getenv("HTTP_USER_AGENT"));
	$http_user_agent = ($http_user_agent == $http_user_agent2) ? $http_user_agent : $http_user_agent.';'.$http_user_agent2;
	if (!isset ($_SESSION['SESSION_USER_AGENT'])) {
		$_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
	}

	if ($_SESSION['SESSION_USER_AGENT'] != $http_user_agent) {
		session_destroy();
		xtc_redirect(xtc_href_link(FILENAME_LOGIN));
	}
}

// verify the IP address if the feature is enabled
if (SESSION_CHECK_IP_ADDRESS == 'True') {
	$ip_address = xtc_get_ip_address();
	if (!isset ($_SESSION['SESSION_IP_ADDRESS'])) {
		$_SESSION['SESSION_IP_ADDRESS'] = $ip_address;
	}

	if ($_SESSION['SESSION_IP_ADDRESS'] != $ip_address) {
		session_destroy();
		xtc_redirect(xtc_href_link(FILENAME_LOGIN));
	}
}

//BOF - DokuMan - 2010-05-20
// Redirect search engines with session id to the same url without session id to prevent indexing session id urls
if ( $truncate_session_id == true ) {
    if (preg_match('/' . xtc_session_name() . '/i', $_SERVER['REQUEST_URI']) ){
        $location = xtc_href_link(basename($_SERVER['SCRIPT_NAME']), xtc_get_all_get_params(array(xtc_session_name())), 'NONSSL', false);
        header("HTTP/1.0 301 Moved Permanently");
        header("Location: $location");
    }
}

if (!(preg_match('/^[a-z0-9]{26}$/i', session_id()) || preg_match('/^[a-z0-9]{32}$/i', session_id()))) {
    // Thanks to HHGAG ;-)
    session_regenerate_id(true);
}
//EOF - DokuMan - 2010-05-20

// set the language
if (!isset ($_SESSION['language']) || isset ($_GET['language'])) {

	include (DIR_WS_CLASSES.'language.php');
	$lng = new language(xtc_input_validation($_GET['language'], 'char', ''));

	if (!isset ($_GET['language']))
		$lng->get_browser_language();

	$_SESSION['language'] = $lng->language['directory'];
	$_SESSION['languages_id'] = $lng->language['id'];
	$_SESSION['language_charset'] = $lng->language['language_charset'];
	$_SESSION['language_code'] = $lng->language['code'];
}

if (isset($_SESSION['language']) && !isset($_SESSION['language_charset'])) {

	include (DIR_WS_CLASSES.'language.php');
	$lng = new language(xtc_input_validation($_SESSION['language'], 'char', ''));


	$_SESSION['language'] = $lng->language['directory'];
	$_SESSION['languages_id'] = $lng->language['id'];
	$_SESSION['language_charset'] = $lng->language['language_charset'];
	$_SESSION['language_code'] = $lng->language['code'];

}

// include the language translations
require (DIR_WS_LANGUAGES.$_SESSION['language'].'/'.$_SESSION['language'].'.php');

// currency
if (!isset ($_SESSION['currency']) || isset ($_GET['currency']) || ((USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && (LANGUAGE_CURRENCY != $_SESSION['currency']))) {

	if (isset ($_GET['currency'])) {
		if (!$_SESSION['currency'] = xtc_currency_exists($_GET['currency']))
			$_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
	} else {
		$_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
	}
}
if (isset ($_SESSION['currency']) && $_SESSION['currency'] == '') {
	$_SESSION['currency'] = DEFAULT_CURRENCY;
}

// write customers status in session
require (DIR_WS_INCLUDES.'write_customers_status.php');

// testing new price class

require (DIR_WS_CLASSES.'main.php');
$main = new main();

require (DIR_WS_CLASSES.'xtcPrice.php');
$xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);

//--- SHOPSTAT -------------------------//
    $shopstat_ref = __FILE__;
   # require("shopstat/shopstat.php");
//--- SHOPSTAT -------------------------//

// econda tracking
if (TRACKING_ECONDA_ACTIVE=='true') {
	require(DIR_WS_INCLUDES . 'econda/class.econda304SP2.php');
	$econda = new econda();
}

// BOF - web28 - 2010-05-06 - PayPal API Modul
require_once (DIR_WS_CLASSES.'paypal_checkout.php');
$o_paypal = new paypal_checkout();
// EOF -  web28 - 2010-05-06 - PayPal API Modul

require (DIR_WS_INCLUDES.FILENAME_CART_ACTIONS);
// create the shopping cart & fix the cart if necesary
if (!isset($_SESSION['cart']) || !is_object($_SESSION['cart'])) { //DokuMan - 2010-02-28 - set undefined variable cart
	$_SESSION['cart'] = new shoppingCart();
}

// include the who's online functions
xtc_update_whos_online();

// split-page-results
require (DIR_WS_CLASSES.'split_page_results.php');

// infobox
require (DIR_WS_CLASSES.'boxes.php');

// auto activate and expire banners
xtc_activate_banners();
xtc_expire_banners();

// auto expire special products
xtc_expire_specials();
#echo DIR_WS_CLASSES.'product.php';
require (DIR_WS_CLASSES.'product.php');
// new p URLS


if (isset ($_GET['info'])) {
	$site = explode('_', $_GET['info']);
	$pID = $site[0];
	$actual_products_id = (int) str_replace('p', '', $pID);
	$product = new product($actual_products_id);
} // also check for old 3.0.3 URLS
elseif (isset($_GET['products_id'])) {
	$actual_products_id = (int) $_GET['products_id'];
	$product = new product($actual_products_id);

}




if($_SESSION['customer_id'] != '0'  && $product->isProduct && $product->data['products_id'] > 0 && (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'spider')===false)  && (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'crawler')===false)  && (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'bot')===false)  && (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'wget')===false)){
	 $offers_query = xtc_db_query("select oAmount, date_added from amazonOffers  where id_amazon = '".$product->data['products_id']."' and  amazonOffers.EurobuchID > 0 and amazonOffers.oAmount <= ".$product->data['products_price']."   order by oAmount");
   if (xtc_db_num_rows($offers_query)) {
       $offers = xtc_db_fetch_array($offers_query);
       $product->data['oAmount'] = $offers['oAmount'];
       $lastAdded = $offers['date_added'];
   }else{
       $lastAdded = '000000000000000000000000000000000000000000000000';
	 }
	# || $product->data['products_id'] == 214051
	 if(date("Y-m-d")!=substr($lastAdded, 0, 10))
  		$eurobuch = addEurobuchListings();
	 if($eurobuch){
	   # Eurobuch: Anfrage erfolgreich gestartet
     if($product->eurobuchInsert){
       updateOfferData($actual_products_id);
		 }else{
		     # Eurobuch: kein Titel gefunden => Dummy einfügen um weitere Anfragen zu vermeiden
         xtc_db_query("Insert into amazonOffers (id_amazon, date_added, EurobuchID, oAmount) values(".$product->data['products_id'].", now(), '1',  0)");
		 }
	 }else{
     updateOfferData($actual_products_id);
	 }
}




//BOF - DokuMan - 2010-02-25 - check for defined variable: product
//if (!is_object($product)) {
if (!isset($product) || !is_object($product)) {
//EOF - DokuMan - 2010-02-25 - check for defined variable: product
	$product = new product();
}
 if($actual_products_id==177508){
  #var_dump($product);
 }
// new c URLS
if (isset ($_GET['cat'])) {
	$site = explode('_', $_GET['cat']);
	$cID = $site[0];
	$cID = str_replace('c', '', $cID);
	$_GET['cPath'] = xtc_get_category_path($cID);
}
// new m URLS
if (isset ($_GET['manu'])) {
	$site = explode('_', $_GET['manu']);
	$mID = $site[0];
	$mID = (int)str_replace('m', '', $mID);
	$_GET['manufacturers_id'] = $mID;
}
if($_SESSION["tpl"]=="mobile"){
	#echo "<br>\$cPath: ".$_GET['cPath'];

}
// calculate category path
if (isset ($_GET['cPath'])) {
	$cPath = xtc_input_validation($_GET['cPath'], 'cPath', '');
}elseif (is_object($product) && !isset ($_GET['manufacturers_id'])) {
	if ($product->isProduct()) {
		$cPath = xtc_get_product_path($actual_products_id);
	} else {
		$cPath = '';
	}
} else {
	$cPath = '';
}
if($_SESSION["tpl"]=="mobile"){
	#echo "<br>\$cPath: $cPath " ;
}
if (xtc_not_null($cPath)) {
	$cPath_array = xtc_parse_category_path($cPath);
	if($_SESSION["tpl"]=="mobile"){
		#var_dump($cPath_array);
	}
	$cPath = implode('_', $cPath_array);
	$current_category_id = $cPath_array[(sizeof($cPath_array) - 1)];


} else {
	$current_category_id = 0;
}
	if($_SESSION["tpl"]=="mobile"){
	  #echo "<br>\$current_category_id: $current_category_id " ;
		#exit;
	}
// include the breadcrumb class and start the breadcrumb trail
require (DIR_WS_CLASSES.'breadcrumb.php');
$breadcrumb = new breadcrumb;

$breadcrumb->add(HEADER_TITLE_TOP, HTTP_SERVER);
$breadcrumb->add(HEADER_TITLE_CATALOG, xtc_href_link(FILENAME_DEFAULT));

// add category names or the manufacturer name to the breadcrumb trail
if (isset ($cPath_array)) {
	for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i ++) {
		if (GROUP_CHECK == 'true') {
			$group_check = "and c.group_permission_".$_SESSION['customers_status']['customers_status_id']."=1 ";
		}
		$categories_query = xtDBquery("select cd.categories_name
				                           from ".TABLE_CATEGORIES_DESCRIPTION." cd,
				                                ".TABLE_CATEGORIES." c
				                           where cd.categories_id = '".$cPath_array[$i]."'
				                           and c.categories_id=cd.categories_id
				                                ".$group_check."
				                           and cd.language_id='".(int) $_SESSION['languages_id']."'");
		if (xtc_db_num_rows($categories_query,true) > 0) {
			$categories = xtc_db_fetch_array($categories_query,true);

			$breadcrumb->add($categories['categories_name'], xtc_href_link(FILENAME_DEFAULT, xtc_category_link($cPath_array[$i], $categories['categories_name'])));
		} else {
			break;
		}
	}
}
//elseif (xtc_not_null($_GET['manufacturers_id'])) {
elseif (isset($_GET['manufacturers_id']) && xtc_not_null($_GET['manufacturers_id'])) { //DokuMan - 2010-02-26 - set undefined variable manufacturers_id
	$manufacturers_query = xtDBquery("select manufacturers_name from ".TABLE_MANUFACTURERS." where manufacturers_id = '".(int) $_GET['manufacturers_id']."'");
	$manufacturers = xtc_db_fetch_array($manufacturers_query, true);

	$breadcrumb->add($manufacturers['manufacturers_name'], xtc_href_link(FILENAME_DEFAULT, xtc_manufacturer_link((int) $_GET['manufacturers_id'], $manufacturers['manufacturers_name'])));

}

// add the products model/name to the breadcrumb trail
if ($product->isProduct()) {
// BOF - Tomcraft - 2009-10-25 - replaced model-number with products_name in breadcrumb navigation
//	$breadcrumb->add($product->getBreadcrumbModel(), xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($product->data['products_id'], $product->data['products_name'])));
	$breadcrumb->add($product->data['products_name'], xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($product->data['products_id'], $product->data['products_name'])));
// EOF - Tomcraft - 2009-10-25 - replaced model-number with products_name in breadcrumb navigation
}

 #var_dump($_GET);
// initialize the message stack for output messages
require (DIR_WS_CLASSES.'message_stack.php');
$messageStack = new messageStack;

// set which precautions should be checked
define('WARN_INSTALL_EXISTENCE', 'true');
define('WARN_CONFIG_WRITEABLE', 'true');
define('WARN_SESSION_DIRECTORY_NOT_WRITEABLE', 'true');
define('WARN_SESSION_AUTO_START', 'true');
define('WARN_DOWNLOAD_DIRECTORY_NOT_READABLE', 'true');

// Include Template Engine
// BOF - Tomcraft - 2009-05-26 - update smarty template engine to 2.6.26
//require (DIR_WS_CLASSES.'Smarty_2.6.22/Smarty.class.php');

require (DIR_WS_CLASSES.'Smarty_2.6.26/Smarty.class.php');
// EOF - Tomcraft - 2009-05-26 - update smarty template engine to 2.6.26

if (isset ($_SESSION['customer_id'])) {
	$account_type_query = xtc_db_query("SELECT account_type,
                                             customers_default_address_id
		                                  FROM ".TABLE_CUSTOMERS."
		                                  WHERE customers_id = '".(int) $_SESSION['customer_id']."'");
	$account_type = xtc_db_fetch_array($account_type_query);

	// check if zone id is unset bug #0000169
	if (!isset ($_SESSION['customer_country_id'])) {
		$zone_query = xtc_db_query("SELECT entry_country_id
				                        FROM ".TABLE_ADDRESS_BOOK."
				                        WHERE customers_id='".(int) $_SESSION['customer_id']."'
				                        AND address_book_id='".$account_type['customers_default_address_id']."'");

		$zone = xtc_db_fetch_array($zone_query);
		$_SESSION['customer_country_id'] = $zone['entry_country_id'];
	}
	$_SESSION['account_type'] = $account_type['account_type'];
} else {
	$_SESSION['account_type'] = '0';
}

// modification for nre graduated system
unset ($_SESSION['actual_content']);

// econda tracking
if (TRACKING_ECONDA_ACTIVE=='true') {

	require(DIR_WS_INCLUDES . 'econda/emos.php');
}

xtc_count_cart();
?>