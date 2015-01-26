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


foreach($parentAreas as $key=>$value) {
	if (!$value) {
		die("No area for ". $key);
	}
}

// JSON

$file = $argv[1];

$data = json_decode(file_get_contents($file));
if (!$data) {
	die();
}

$areaMapItInfoRepo = new \com\meetyournextmp\repositories\AreaMapItInfoRepository();
$countryRepo = new \repositories\CountryRepository();
$areaRepo = new \repositories\AreaRepository();
$gb = $countryRepo->loadByTwoCharCode("GB");
$siteRepo = new \repositories\SiteRepository();
$site = $siteRepo->loadById($CONFIG->singleSiteID);

foreach($data as $json_blob) {
	print $json_blob->name."\n";

	$areaMapItInfo = $areaMapItInfoRepo->getByCodeGSS($json_blob->codes->gss);
	if ($areaMapItInfo) {
		print " ... found area ID ". $areaMapItInfo->getAreaId()."\n";

		$areaMapItInfo->setName($json_blob->name);
		$areaMapItInfo->setCodeGss($json_blob->codes->gss);
		if (isset($json_blob->codes->unit_id)) {
			$areaMapItInfo->setCodeUnitId($json_blob->codes->unit_id);
		}
		$areaMapItInfo->setMapitId($json_blob->id);
		$areaMapItInfoRepo->edit($areaMapItInfo);

	} else {
		print " ... creating!\n";

		if (!array_key_exists($json_blob->country_name, $parentAreas)) {
			die("No Parent Area: ".$json_blob->country_name."\n");
		}

		$area = new \models\AreaModel();
		$area->setTitle($json_blob->name);
		$area->setCountryId($gb->getId());
		$area->setSiteId($CONFIG->singleSiteID);
		$areaRepo->create($area, $parentAreas[$json_blob->country_name],  $site, $gb, null);


		$areaMapItInfo = new \com\meetyournextmp\models\AreaMapItInfoModel();
		$areaMapItInfo->setName($json_blob->name);
		$areaMapItInfo->setCodeGss($json_blob->codes->gss);
		if (isset($json_blob->codes->unit_id)) {
			$areaMapItInfo->setCodeUnitId($json_blob->codes->unit_id);
		}
		$areaMapItInfo->setMapitId($json_blob->id);
		$areaMapItInfo->setAreaId($area->getId());
		$areaMapItInfoRepo->create($areaMapItInfo);

	}

}




