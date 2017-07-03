<?PHP
/************************************************************
        Basic error handler 
************************************************************/
$urlParts = parse_url($_SERVER["REQUEST_URI"]);
?>
<html>
<head>
<title>Page not found !!</title>
<style>
*{margin:0;padding:0}
body{background-color:#F0FFF0;margin:7% auto 0;max-width:390px;min-height:180px;padding:30px 0 15px;} 
code{font:15px/20px arial,sans-serif;font-weight:bold;}
code:hover{text-decoration:underline;}
p{margin:11px 0 22px;overflow:hidden;color:#556B2F;}
p.title {font-size:100px;color:#556B2F;background-color:#FFFFF0;border:1px dashed #556B2F;padding:0px 15px 0px 15px;white-space:nowrap;}
</style>
</head>
<body>
<p class="title">404 !! </p>
<p>The requested URL <code><?=$urlParts['path']?></code> was not found on this server.  
</body>
</html>