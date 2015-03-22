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



$extensionMeet = $app['extensions']->getExtensionById('com.meetyournextmp');

$siteRepo = new \repositories\SiteRepository();
$app['currentSite'] = $siteRepo->loadById($CONFIG->singleSiteID);

$vrb = new \repositories\builders\VenueRepositoryBuilder();
$vrb->setIncludeDeleted(false);
foreach($vrb->fetchAll() as $venue) {


	if (!$venue->getAreaId()) {
		print "Venue ". $venue->getSlug() . " has no area\n";
	}

	if ($venue->getAreaId() && $venue->getAddressCode()) {
		$areaShouldBe = $extensionMeet->getAreaForPostCode(new \com\meetyournextmp\PostcodeParser($venue->getAddressCode()));
		if ($areaShouldBe && $areaShouldBe->getId() != $venue->getAreaId()) {

			print "Venue ". $venue->getSlug() . " should have area ". $areaShouldBe->getTitle() ." according to it's postcode\n";
		}
	}

	if ($venue->getCachedFutureEvents() > 0 && !$venue->hasLatLng()) {
		print "Venue ". $venue->getSlug() . " has cached future events but no lat/lng \n";
	}

}



print "\n\nDone\n\n";


