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


$stat = $DB->prepare("SELECT area_information.*, ".
	"area_mapit_information.mapit_id, area_mapit_information.code_unit_id, area_mapit_information.code_gss, area_mapit_information.name ".
	" FROM area_information JOIN area_mapit_information ON area_mapit_information.area_id = area_information.id");

$stat->execute(array());

$fp = fopen(__DIR__."/../../../webSingleSite/datadump/seats.csv", 'w');
if ($fp) {
	fputcsv($fp, array('name', 'mapit_id', 'code_unit_id', 'code_gss', "URL"));

	while ($data = $stat->fetch()) {
		$area = new \models\AreaModel();
		$area->setFromDataBaseRow($data);
		fputcsv($fp, array($data['name'], $data['mapit_id'], $data['code_unit_id'], $data['code_gss'], "http://" . $CONFIG->webSiteDomain . "/area/" . $area->getSlugForUrl()));
	}

	fclose($fp);
} else {
	die ("OH NO");
}