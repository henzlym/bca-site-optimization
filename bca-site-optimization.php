<?php

/**
 * Plugin Name:       Blk Canvas - Site Optimization
 * Description:       Enhance your website's performance and user experience with optimization headers, like file compression and gzip!
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bca-site-optimization
 *
 * @package           create-block
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

include __DIR__  . '/includes/settings-api.php';
include __DIR__  . '/includes/settings.php';

/**
 * How to add .htaccess code through a function?
 * @see https://wordpress.stackexchange.com/questions/36233/how-to-add-htaccess-code-through-a-function
 */

function bca_add_secure_headers()
{
	$htaccess_file = ABSPATH . '.htaccess';
	$insertion = array(
		'<IfModule mod_headers.c>',
		'   <FilesMatch "\.(php|html)$">',
		'       Header always set X-Content-Type-Options "nosniff"',
		'       Header set X-XSS-Protection "1; mode=block"',
		'       Header set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" env=HTTPS',
		'       Header set Content-Security-Policy "script-src \'unsafe-inline\' \'unsafe-eval\' http: https:"',
		'       Header set Referrer-Policy "same-origin"',
		'       Header set Feature-Policy "geolocation \'self\'"',
		'   </FilesMatch>',
		'</IfModule>'
	);
	return insert_with_markers($htaccess_file, 'BCA_SECURE_HEADERS', $insertion);
}

function bca_add_compression_headers()
{
	$htaccess_file = ABSPATH . '.htaccess';
	$insertion = array(
		'<IfModule mod_deflate.c>',
		'   # Compress HTML, CSS, JavaScript, Text, XML and fonts',
		'   AddOutputFilterByType DEFLATE application/javascript',
		'   AddOutputFilterByType DEFLATE application/rss+xml',
		'   AddOutputFilterByType DEFLATE application/vnd.ms-fontobject',
		'   AddOutputFilterByType DEFLATE application/x-font',
		'   AddOutputFilterByType DEFLATE application/x-font-opentype',
		'   AddOutputFilterByType DEFLATE application/x-font-otf',
		'   AddOutputFilterByType DEFLATE application/x-font-truetype',
		'   AddOutputFilterByType DEFLATE application/x-font-ttf',
		'   AddOutputFilterByType DEFLATE application/x-javascript',
		'   AddOutputFilterByType DEFLATE application/xhtml+xml',
		'   AddOutputFilterByType DEFLATE application/xml',
		'   AddOutputFilterByType DEFLATE font/opentype',
		'   AddOutputFilterByType DEFLATE font/otf',
		'   AddOutputFilterByType DEFLATE font/ttf',
		'   AddOutputFilterByType DEFLATE image/svg+xml',
		'   AddOutputFilterByType DEFLATE image/x-icon',
		'   AddOutputFilterByType DEFLATE text/css',
		'   AddOutputFilterByType DEFLATE text/html',
		'   AddOutputFilterByType DEFLATE text/javascript',
		'   AddOutputFilterByType DEFLATE text/plain',
		'   AddOutputFilterByType DEFLATE text/xml',
		'   # Remove browser bugs (only needed for really old browsers)',
		'   BrowserMatch ^Mozilla/4\.0[678] no-gzip',
		'   BrowserMatch ^Mozilla/4\.0[678] no-gzip',
		'   BrowserMatch \bMSIE !no-gzip !gzip-only-text/html',
		'   Header append Vary User-Agent',
		'</IfModule>'
	);
	return insert_with_markers($htaccess_file, 'BCA_COMPRESSION_HEADERS', $insertion);
}

function bca_add_cache_headers()
{
	$htaccess_file = ABSPATH . '.htaccess';
	$insertion = array(
		'',
		'# BROWSER CACHING USING EXPIRES HEADERS',
		'<IfModule mod_expires.c>',
		'   ExpiresActive on',
		'   ExpiresDefault "access plus 2 days"',
		'   ExpiresByType image/jpg "access plus 1 year"',
		'   ExpiresByType image/svg+xml "access 1 year"',
		'   ExpiresByType image/gif "access plus 1 year"',
		'   ExpiresByType image/jpeg "access plus 1 year"',
		'   ExpiresByType image/webp "access plus 1 year"',
		'   ExpiresByType image/png "access plus 1 year"',
		'   ExpiresByType text/css "access plus 1 year"',
		'   ExpiresByType text/javascript "access plus 1 year"',
		'   ExpiresByType application/javascript "access plus 1 year"',
		'   ExpiresByType application/x-shockwave-flash "access plus 1 year"',
		'   ExpiresByType image/ico "access plus 1 year"',
		'   ExpiresByType image/x-icon "access plus 1 year"',
		'   ExpiresByType text/html "access plus 600 seconds"',
		'</IfModule>',
		'',
		'# BROWSER CACHING USING CACHE-CONTROL HEADERS',
		'<IfModule mod_headers.c>',
		'   # One year for image and video files',
		'   <filesMatch ".(flv|gif|ico|jpg|jpeg|mp4|mpeg|png|svg|swf|webp)$">',
		'       Header set Cache-Control "max-age=31536000, public"',
		'   </filesMatch>',
		'',
		'   # One month for JavaScript and PDF files',
		'   <filesMatch ".(js|pdf)$">',
		'       Header set Cache-Control "max-age=31536000, public"',
		'   </filesMatch>',
		'',
		'   # One week for CSS files',
		'   <filesMatch ".(css)$">',
		'       Header set Cache-Control "max-age=31536000, public"',
		'   </filesMatch>',
		'</IfModule>',
		''
	);
	return insert_with_markers($htaccess_file, 'BCA_CACHE_HEADERS', $insertion);
}

function bca_remove_headers($marker)
{
	if (!$marker) {
		return;
	}
	$htaccess_file = ABSPATH . '.htaccess';
	$insertion = '';
	return insert_with_markers($htaccess_file, $marker, $insertion);
}
function bca_remove_cache_headers()
{
	bca_remove_headers('BCA_CACHE_HEADERS');
}
function bca_remove_compression_headers()
{
	bca_remove_headers('BCA_COMPRESSION_HEADERS');
}

function bca_update_site_headers($old_value, $value, $option)
{

	if (isset($value['enable_cache']) && $value['enable_cache'] == 1) {
		bca_add_cache_headers();
	} else {
		bca_remove_cache_headers();
	}

	if (isset($value['enable_gzip']) && $value['enable_gzip'] == 1) {
		bca_add_compression_headers();
	} else {
		bca_remove_compression_headers();
	}
}

add_action('update_option_bca_site_optimizations_basics', 'bca_update_site_headers', 10, 3);
