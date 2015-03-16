# PHP Sitemap Generator
PHP Sitemap Generator is a PHP script that allows your webpage to server a dynamic sitemap as a 
static one. This script reads the VirtualHost file from Apache2.4 and parses it to create the proper 
sitemap. This script is limited to the domain it is running on, so it won't create a sitemap for 
parallel running webpages.
##Requirements
  - Apache2.4 (2.2 support will be added later)
  - PHP 5.5+
  - Access to write VirtualHosts
### Version
0.1
## Installation
``` cd /var/www/ git clone https://github.com/Dunky13/PHP-Sitemap-Generator.git sitemap_generator ```
 * #### Semi-automatic installation
    Browse to your domain/sitemap_generator/install.php
 * #### Manual Installation
Add the following part at the bottom of the appropriate VirtualHost file: ``` <IfModule mod_alias.c>
    <IfModule mod_rewrite.c>
        Alias /sitemap <directory of the sitemap generator>
		RewriteEngine on
		RewriteRule ^/?sitemap.xml?$ /sitemap/index.php [NC,PT]
	</IfModule> </IfModule> ```
### Todo's
 - Make it work with Apache2.2
 - Make it work with NGinx License ---- MIT
