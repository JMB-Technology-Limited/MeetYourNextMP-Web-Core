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

// really??????????
$actuallySEND = isset($argv[1]) && strtolower($argv[1]) == 'yes';
print "Actually SEND: ". ($actuallySEND ? "YES":"nah")."\n";
if (!$actuallySEND) die("DIE\n");


// setup
$siteRepo = new \repositories\SiteRepository();
$site = $siteRepo->loadById($CONFIG->singleSiteID);
configureAppForSite($site);


// Get Human
$stat = $DB->prepare("SELECT human_information.* FROM human_information ".
		" LEFT JOIN human_email ON human_email.human_id = human_information.id ".
		" WHERE human_information.email IS NOT NULL AND human_email.id IS NULL".
		" LIMIT 1");
$stat->execute();
if ($stat->rowCount() == 0) {
	die("No Human Data");
}

$human = new \com\meetyournextmp\models\HumanModel();
$human->setFromDataBaseRow($stat->fetch());

// Get Area
$stat = $DB->prepare("SELECT area_information.* FROM area_information ".
	" JOIN human_in_area ON human_in_area.area_id = area_information.id AND human_in_area.human_id = :human_id");
$stat->execute(array('human_id'=>$human->getId()));
$stat->execute();
if ($stat->rowCount() == 0) {
	die("No Area Data");
}

$area = new \models\AreaModel();
$area->setFromDataBaseRow($stat->fetch());

// make email

$email = new \com\meetyournextmp\models\HumanEmailModel();
$email->setFromAppAndHumanAndArea($app, $human, $area);

// save email

$repo = new \com\meetyournextmp\repositories\HumanEmailRepository();
$repo->create($email);

// send email

//$email->send($app);

// record sent
$repo->markSent($email);

