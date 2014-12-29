<?PHP
ob_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        (?head_begin/)
        <title>(?title/)</title>
        (?head_end/)
    </head>
    <body>
    (?body_begin/)
        (?title_section/)
        <div id="page">
            <div id="topbar">(?topbar/)</div>
            <div id="menu">(?sidebar/)</div>
            <div id="content">(?content/)</div>
        </div>    
        <div id="footer"><a href='(?info.domain_name/)'>Home</a> | <a href='/contactus.php'>Contact us</a> | <a href='/aboutus.php'>About us</a></div>
    (?body_end/)    
    </body>
</html>
<?
$template_0=ob_get_contents();
ob_clean();