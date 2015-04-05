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



$actuallyANON = isset($argv[1]) && strtolower($argv[1]) == 'yes';
print "Actually ANON: ". ($actuallyANON ? "YES":"nah")."\n";
if (!$actuallyANON) die("DIE\n");

$actuallyReallyANON = isset($argv[2]) && strtolower($argv[2]) == 'really';
print "Really ANON: ". ($actuallyReallyANON ? "YES":"nah")."\n";
if (!$actuallyReallyANON) die("DIE\n");

die("GONNA DIE ANYWAY\n");

print "Waiting ...\n";
sleep(5);
print "Running\n";

$stat = $DB->prepare("UPDATE human_popit_information ".
		" SET email= id || '@jarofgreen.co.uk', twitter='jogtest1' ".
		" WHERE email IS NOT NULL ");
$stat->execute();


$stat = $DB->prepare("UPDATE human_information ".
		" SET email= id || '@jarofgreen.co.uk', twitter='jogtest1' ".
		" WHERE email IS NOT NULL ");
$stat->execute();

$stat = $DB->prepare("UPDATE human_history ".
		" SET email= human_id || '@jarofgreen.co.uk', twitter='jogtest1' ".
		" WHERE email IS NOT NULL ");
$stat->execute();

print "Done\n";


