<?php
/**
* XML Sitemap generator using PHP.
*
* This tool generates XML Sitemapss using the open standard defined on sitemaps.org. These sitemaps are supported by search engines and optimizes search results.
* The software is profided as is and the creator is not liable to any damages (none should happen).
*
*/
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
require_once dirname(__FILE__).'/autoloader/loader.class.php';

//        RewriteRule ^/?sitemap.xml?$            /sitemap/index.php [NC,PT]
//        Alias   /sitemap                        /home/dunky13/www/public/sitemap/


define('HOST', $_SERVER['HTTP_HOST']);
$ini = parse_ini_file("config.ini.php", true);

$cFiles = new Install($ini["Apache Site Locations"]["path"], $ini["Apache Site Locations"]["host"]);

exec("apache2 -v", $output);
if(preg_match("/Server version:\s+Apache\/2\.4\.[0-9]*\s+/",$output[0])){
	$cFiles->findConfigurations();
	$cFiles->installRewrite();
}
else{
	echo "Cannot install this script, it has been written for Apache 2.4";
}
?>
