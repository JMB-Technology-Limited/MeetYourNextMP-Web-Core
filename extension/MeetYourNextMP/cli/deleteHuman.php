<?php
define('APP_ROOT_DIR',__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
require_once (defined('COMPOSER_ROOT_DIR') ? COMPOSER_ROOT_DIR : APP_ROOT_DIR).'/vendor/autoload.php';
require_once APP_ROOT_DIR.'/core/php/autoload.php';
require_once APP_ROOT_DIR.'/core/php/autoloadCLI.php';


/**
 *
 * @package com.meetyournextmp
 * @license Closed Source
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

$slug = intval($argv[1]);

if ($slug < 1) {
	die("PASS SLUG");
}

$siteRepo = new \repositories\SiteRepository();
$app['currentSite'] = $siteRepo->loadById($CONFIG->singleSiteID);

$humanRepo = new \com\meetyournextmp\repositories\HumanRepository();
$human = $humanRepo->loadBySlug($app['currentSite'], $slug);
if ($human) {
	$humanRepo->delete($human);
}


