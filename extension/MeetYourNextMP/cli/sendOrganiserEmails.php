<?php
use Abraham\TwitterOAuth\TwitterOAuth;

define('APP_ROOT_DIR',__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
require_once (defined('COMPOSER_ROOT_DIR') ? COMPOSER_ROOT_DIR : APP_ROOT_DIR).'/vendor/autoload.php';
require_once APP_ROOT_DIR.'/core/php/autoload.php';
require_once APP_ROOT_DIR.'/core/php/autoloadCLI.php';


sleep(5);

/**
 *
 * @package com.meetyournextmp
 * @license Closed Source
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */


// setup
$siteRepo = new \repositories\SiteRepository();
$site = $siteRepo->loadById($CONFIG->singleSiteID);
$customFields = $site->getCachedEventCustomFieldDefinitionsAsModels();
$customFieldOrganiserEmail = $customFields[0];
$userRepo = new \repositories\UserAccountRepository();
$organiserEmailRepo = new \com\meetyournextmp\repositories\OrganiserEmailRepository();
configureAppForSite($site);
$nowPlusSomeEmailReadingTime = TimeSource::getDateTime();
$nowPlusSomeEmailReadingTime->add(new \DateInterval("P1D"));


// Events
$erb = new \repositories\builders\EventRepositoryBuilder();
$erb->setSite($site);
$erb->setAfterNow();
$erb->setIncludeCancelled(false);
$erb->setIncludeDeleted(false);
foreach($erb->fetchAll() as $event) {
	print $event->getSlug()." ".$event->getSummary()."\n";
	$email = \com\meetyournextmp\models\OrganiserEmailModel::getEmailFrom( $event->getCustomField($customFieldOrganiserEmail));
	if ($email) {
		print "  -  email: ".$email."\n";

		if ($event->getStartAtInUTC()->getTimestamp() < $nowPlusSomeEmailReadingTime->getTimestamp()) {
			print "  - event to soon, not sending\n";
		} else {
			print "  - event not to soon\n";
			$organiserEmailFoundInHistory = false;
			$hrb = new \repositories\builders\HistoryRepositoryBuilder();
			$hrb->setEvent($event);
			foreach ($hrb->fetchAll() as $history) {
				if ($history->getUserAccountId()) {
					$user = $userRepo->loadByID($history->getUserAccountId());
					if ($user && $user->getEmail() == $email) {
						$organiserEmailFoundInHistory = true;
					}
				}
			}

			if ($organiserEmailFoundInHistory) {
				print "  -  organiser email also edited event so ignoring\n";
			} else {
				print "  -  organiser email did not edit event\n";

				if ($organiserEmailRepo->hasBeenSentToEvent($event)) {
					print "  -  email already sent\n";
				} else {
					print "  - CREATING\n";

					$emailObj = new \com\meetyournextmp\models\OrganiserEmailModel();
					$emailObj->setFromAppAndEventAndEmail($app, $event, $email);

					$organiserEmailRepo->create($emailObj);

					print "  - SENDING\n";
					$emailObj->send($app);

					$organiserEmailRepo->markSent($emailObj);

					die();
				}

			}

		}

	} else {
		print "  -  No email\n";
	}










}

print "Done\n";
