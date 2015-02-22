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
$_SESSION['url'] = parse_url('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$_SESSION['request_uri']=$_SERVER['REQUEST_URI'];
$_SESSION['domain']=DOMAIN_NAME;


// Set Include path to current directory;
set_include_path(__DIR__);


