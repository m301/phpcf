<?PHP
/*******************************************************************
*                   Optional Configuration
********************************************************************/

//set_time_limit(0);
//error_reporting(~E_NOTICE);
error_reporting(E_ALL);
ini_set('display_errors', true);


/*******************************************************************
*                   Constant Configuration
********************************************************************/

//Set default time zone to current region
date_default_timezone_set('Asia/Kolkata'); 
//Start session.We need. 
session_start();


/*******************************************************************
*                   Variable Configuration
********************************************************************/

define('SHARED_HOSTING',1);
//ini_set('include_path', '.:'.dirname(__FILE__));echo get_include_path();
ini_set('include_path', '.:/home/'.USER_NAME.'/public_html/include/');


$_SESSION['url'] = parse_url('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$_SESSION['request_uri']=$_SERVER['REQUEST_URI'];
$_SESSION['domain']=DOMAIN_NAME;




