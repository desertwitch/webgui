<?PHP
/* Copyright 2005-2016, Lime Technology
 * Copyright 2012-2016, Bergware International.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */
?>
<?
// Define root path
$docroot = $_SERVER['DOCUMENT_ROOT'];

require_once "$docroot/webGui/include/Helpers.php";
require_once "$docroot/webGui/include/PageBuilder.php";
require_once "$docroot/webGui/include/publish.php";

// Get the webGui configuration preferences
extract(parse_plugin_cfg('dynamix',true));

// Read emhttp status
refresh_emhttp_state();
$var     = parse_ini_file('state/var.ini');
$sec     = parse_ini_file('state/sec.ini',true);
$devs    = parse_ini_file('state/devs.ini',true);
$disks   = parse_ini_file('state/disks.ini',true);
$users   = parse_ini_file('state/users.ini',true);
$shares  = parse_ini_file('state/shares.ini',true);
$sec_nfs = parse_ini_file('state/sec_nfs.ini',true);
$sec_afp = parse_ini_file('state/sec_afp.ini',true);

// Read network settings
extract(parse_ini_file('state/network.ini',true));

// Merge SMART settings
require_once "$docroot/webGui/include/CustomMerge.php";

// Build webGui pages first, then plugins pages
$site = [];
build_pages('webGui/*.page');
foreach (glob('plugins/*', GLOB_ONLYDIR) as $plugin) {
  if ($plugin != 'plugins/dynamix') build_pages("$plugin/*.page");
}

// Extract the 'querystring'
extract($_GET);

// Need the following to make php-fpm & nginx work
if (empty($path)) {
  $path = substr(explode("?", $_SERVER["REQUEST_URI"])[0], 1);
  $prev = '';

  if (empty($path)) {
    $regTypes = array("Trial", "Basic", "Plus", "Pro");
    $path = in_array($var['regTy'], $regTypes)? 'Main' : 'Tools/Registration';
  }
}

// The current "task" is the first element of the path
$task = strtok($path, '/');

// Here's the page we're rendering
$myPage = $site[basename($path)];
$pageroot = $docroot.'/'.dirname($myPage['file']);
$update = $display['refresh']>0 || ($display['refresh']<0 && $var['mdResync']==0);

// Giddyup
require_once "$docroot/webGui/include/DefaultPageLayout.php";
?>
