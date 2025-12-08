<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'rss_controller';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['rss/import'] = 'rss_controller/import';
$route['rss/manage'] = 'rss_controller/manage';
$route['rss/dashboard'] = 'rss_controller/dashboard';
$route['rss/fetch_feed'] = 'rss_controller/fetch_feed';
$route['rss/delete/(:num)'] = 'rss_controller/delete/$1';
$route['rss/update_priority'] = 'rss_controller/update_priority';
$route['rss/toggle_platform'] = 'rss_controller/toggle_platform';
$route['rss/update_platforms'] = 'rss_controller/update_platforms';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
