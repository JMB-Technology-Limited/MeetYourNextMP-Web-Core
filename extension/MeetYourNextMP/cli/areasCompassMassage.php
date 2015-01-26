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


define('MODE_PRE',1);
define('MODE_AFTER',2);

$modeCurrent = 2;

$magicWords = array("North","South","East","West","And", "Central", "Leith", "Hessle", "Brightside and Hillsborough", "Hallam", "Heeley", "Penarth");

$ignoreWords = array("Brent");

function considerArea(\models\AreaModel $areaModel, $modeCurrent, $magicWords, $ignoreWords) {

	foreach($ignoreWords as $check) {
		if (strpos(strtolower($areaModel->getTitle()), strtolower($check)) !== FALSE) {
			return false;
		}
	}

	foreach($magicWords as $check) {
		if ($modeCurrent == MODE_AFTER) {
			if (strtolower(substr($areaModel->getTitle(), 0 - strlen(" ".$check))) == strtolower(" ".$check)) {
				return true;
			}
		}
		if ($modeCurrent == MODE_PRE) {
			if (strtolower(substr($areaModel->getTitle(), 0, strlen($check." "))) == strtolower($check." ")) {
				return true;
			}
		}
	}

	return false;
}

function stripCompassEndOfArea(\models\AreaModel $areaModel, $modeCurrent, $magicWords) {

	$title = str_replace(",", " ", $areaModel->getTitle());

	do {
		$flag = false;

		foreach($magicWords as $check) {
			if ($modeCurrent == MODE_AFTER) {
				if (strtolower(substr($title, 0 - strlen(" ".$check))) == strtolower(" ".$check)) {
					$title = trim(substr($title, 0, 0-strlen($check)));
					$flag = true;
				}
			}
			if ($modeCurrent == MODE_PRE) {
				if (strtolower(substr($title, 0, strlen($check." "))) == strtolower($check." ")) {
					$title = trim(substr($title, strlen($check)));
					$flag = true;
				}
			}
		}
	} while ($flag);

	return trim($title);

}


// AREAS

$parentAreas = array(
	'England'=>null,
	'Scotland'=>null,
	'Wales'=>null,
	'Northern Ireland'=>null,
);


$arb = new \repositories\builders\AreaRepositoryBuilder();
$arb->setNoParentArea(true);
$arb->setIncludeDeleted(false);
$arb->fetchAll();
foreach($arb->fetchAll() as $area) {
	foreach($parentAreas as $key=>$value) {
		if ($area->getTitle() == $key) {
			$parentAreas[$key] = $area;
		}
	}
}

$workToDo = array(
	'England'=>null,
	'Scotland'=>null,
	'Wales'=>null,
	'Northern Ireland'=>null,
);

foreach($parentAreas as $parentArea) {
	print "Parent Area ".$parentArea->getTitle()."\n";

	$arb = new \com\meetyournextmp\repositories\builders\AreaRepositoryBuilder();
	$arb->setParentArea($parentArea);
	$arb->setLimit(10000);
	foreach($arb->fetchAll() as $area) {


		if ($area->getParentAreaId() == $parentArea->getId()) {
			print "  -  ". $area->getTitle()."\n";

			if (considerArea($area, $modeCurrent, $magicWords, $ignoreWords)) {
				print "  -  -  Consider\n";


				$newParentTitle = stripCompassEndOfArea($area, $modeCurrent, $magicWords);
				if (!isset($workToDo[$parentArea->getTitle()][$newParentTitle])) {
					$workToDo[$parentArea->getTitle()][$newParentTitle] = array();
				}
				$workToDo[$parentArea->getTitle()][$newParentTitle][] = $area;

			}
		}

	}

}

print "\n\n\n\n\n";

foreach($parentAreas as $parentArea) {

	print "In: ".$parentArea->getTitle()."\n\n";

	if (isset($workToDo[$parentArea->getTitle()])) {
		foreach($workToDo[$parentArea->getTitle()] as $newParentAreaTitle=>$newParentAreaAreas) {

			if (count($newParentAreaAreas) > 1) {

				print "Make: ". $newParentAreaTitle."\n";
				foreach($newParentAreaAreas as $area) {
					print "  -  Area: ".$area->getTitle()."\n";
				}
				print "\n";

			}

		}
	}

}


sleep(10);

die();

$countryRepo = new \repositories\CountryRepository();
$areaRepo = new \repositories\AreaRepository();
$gb = $countryRepo->loadByTwoCharCode("GB");
$siteRepo = new \repositories\SiteRepository();
$site = $siteRepo->loadById($CONFIG->singleSiteID);

foreach($parentAreas as $parentArea) {

	print "In: ".$parentArea->getTitle()."\n\n";

	if (isset($workToDo[$parentArea->getTitle()])) {
		foreach($workToDo[$parentArea->getTitle()] as $newParentAreaTitle=>$newParentAreaAreas) {

			if (count($newParentAreaAreas) > 1) {

				print "Make: ". $newParentAreaTitle."\n";

				$newParentAreaModel = new \models\AreaModel();
				$newParentAreaModel->setTitle($newParentAreaTitle);

				$areaRepo->create($newParentAreaModel, $parentArea, $site, $gb);

				foreach($newParentAreaAreas as $area) {
					print "  -  Area: ".$area->getTitle()."\n";

					$area->setParentAreaId($newParentAreaModel->getId());

					$areaRepo->editParentArea($area);

				}
				print "\n";

			}

		}
	}

}


