<?php
/**
* XML Sitemap generator using PHP.
*
* This tool generates XML Sitemapss using the open standard defined on sitemaps.org. These sitemaps are supported by search engines and optimizes search results.
* The software is profided as is and the creator is not liable to any damages (none should happen).
*
*/

header('Content-Type: application/xml; charset=utf-8');
require_once dirname(__FILE__).'/autoloader/loader.class.php';

define('HOST', $_SERVER['HTTP_HOST']);
$ini = parse_ini_file("config.ini.php", true);

$time_pre = microtime(true);

$cFiles = new Files($ini["Apache Site Locations"]["path"], $ini["Apache Site Locations"]["host"]);
$cFiles->findConfigurations();
$cFiles->parseFiles();
?>
<?xml version="1.0" encoding="UTF-8"?>
<!--<?xml-stylesheet type="text/xsl" href="sitemap.xsl"?>-->
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
	http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php 
foreach($cFiles->vHosts as $vHost){
	$ssl = $vHost->ssl ? "https://" : "http://";
	$host = $vHost->ServerName;
	foreach($vHost->alias as $alias){
?>
	<url>
		<loc><?php echo $ssl.$host.$alias->url;?></loc>
		<lastmod><?=date("c",$alias->lastMod);?></lastmod>
		<priority><?=$alias->priority;?></priority>
<?php
		if($alias->images){
		foreach($alias->images as $image){
?>
		<image:image>
			<image:loc><?php echo $ssl.$host.$alias->url.$image->src;?></image:loc>
			<image:caption><![CDATA[<?=$image->alt;?>]]></image:caption>
		</image:image>
<?php
				}
			}
?>
	</url>
<?php
		}
	}

?>
</urlset>
<?php
$time_post = microtime(true);
$exec_time = $time_post - $time_pre;
?>
<!-- Dynamic page generated in <?=number_format($exec_time,4)?> seconds. -->
<!-- https://mwent.info/ Marc Went 2015 (c)-->
