<?php
/**
 * Theme Information (Changelog) in Iframe
 * 
 * @since 0.1.0
 */
/* get theme info var */
$info_var = get_query_var( 'ahtr_info' );

/* check valid date in download hash */
if ( $info_var != 'changelog' )
	return;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title>Theme Changelog</title>

<style type="text/css">body{width:700px;margin:150px auto;font:16px/25px Georgia,Times,'Times New Roman',serif}a:link,a:visited{color:#2f6eb9;text-decoration:none}a:hover,a:active{text-decoration:underline}h1,h2,h3,h4,h5,h6{margin:40px 0 30px 0;color:#000;font-weight:bold;font-family:Arial,sans-serif}h1{margin-top:80px;font-size:2.2em}code{padding:0 3px;background:#eee}pre code{padding:0}pre{padding:9px;background:#eee;border:1px solid #ccc}ul{list-style:square}p.first{font-size:21px}p.second{font-size:15px}ul.space li{margin-bottom:10px}.section{overflow:hidden}.columns-2{float:left;width:350px;margin:0 0 21px 25px}.columns-3{float:left;width:230px;margin:0 0 21px 20px}</style>

</head>
<body>

<?php auto_hosted_theme_information(); ?>

</body>
</html>