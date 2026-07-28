<?php

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
//
// Overrides Laravel's bundled server.php (which `artisan serve` uses
// automatically when this file exists at the project root) to fix one bug:
// file_exists() is true for directories as well as files, so a request for
// an existing folder with no matching file (e.g. /media, /assets) was being
// handled directly by PHP's built-in server - which has no directory
// listing and no custom 404 page - instead of reaching Laravel's router.
// is_file() only matches actual files, so directory requests now correctly
// fall through to index.php and get the app's own 404 page, matching real
// Apache/Nginx hosting behavior.
if ($uri !== '/' && is_file($publicPath.$uri)) {
    return false;
}

$formattedDateTime = date('D M j H:i:s Y');

$requestMethod = $_SERVER['REQUEST_METHOD'];
$remoteAddress = $_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'];

file_put_contents('php://stdout', "[$formattedDateTime] $remoteAddress [$requestMethod] URI: $uri\n");

require_once $publicPath.'/index.php';
