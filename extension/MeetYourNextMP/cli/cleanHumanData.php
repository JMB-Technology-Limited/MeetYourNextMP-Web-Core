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




$statFindPopit = $DB->prepare("SELECT * FROM human_popit_information WHERE name=:name");

$humanRepo = new \com\meetyournextmp\repositories\HumanRepository();

$stat = $DB->prepare("SELECT human_information.* from human_information ".
	" LEFT JOIN human_popit_information ON human_popit_information.human_id = human_information.id ".
	" WHERE human_popit_information.id IS NULL ");
$stat->execute();
while($data = $stat->fetch()) {
	$human = new \com\meetyournextmp\models\HumanModel();
	$human->setFromDataBaseRow($data);
	print "This human has no POPIT record: ". $human->getId()."\n";
	if ($human->getIsDeleted()) {
		print " - already deleted\n";
	} else {
		print " - DELETING!\n";
		$humanRepo->delete($human);
	}


	$statFindPopit->execute(array('name'=>$human->getTitle()));
	while($data = $statFindPopit->fetch()) {
		print " - for the name ".$human->getTitle()." we found PopIt IDs:  ". $data['popit_id']."\n";
	}
}


print "\n\ndone\n\n";
