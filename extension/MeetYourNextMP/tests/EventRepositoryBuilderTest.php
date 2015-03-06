<?php
use models\EventModel;
use models\SiteModel;
use models\UserAccountModel;
use repositories\EventRepository;
use repositories\SiteRepository;
use repositories\UserAccountRepository;

/**
 *
 * @package org.openacalendar.comments
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class EventRepositoryBuilderTest extends \PHPUnit_Framework_TestCase {


	public function mktime($year=2012, $month=1, $day=1, $hour=0, $minute=0, $second=0, $timeZone='Europe/London') {
		$dt = new \DateTime('', new \DateTimeZone($timeZone));
		$dt->setTime($hour, $minute, $second);
		$dt->setDate($year, $month, $day);
		return $dt;
	}


	function testEventInNoAreaHumanInCentre() {

		$DB = getNewTestDB();
		addCountriesToTestDB();

		TimeSource::mock(2014,5,1,7,0,0);

		$user = new UserAccountModel();
		$user->setEmail("test@jarofgreen.co.uk");
		$user->setUsername("test");
		$user->setPassword("password");


		$userWatching = new UserAccountModel();
		$userWatching->setEmail("test1@jarofgreen.co.uk");
		$userWatching->setUsername("test1");
		$userWatching->setPassword("password");


		$userRepo = new UserAccountRepository();
		$userRepo->create($user);
		$userRepo->create($userWatching);

		$site = new SiteModel();
		$site->setTitle("Test");
		$site->setSlug("test");

		$siteRepo = new SiteRepository();
		$siteRepo->create($site, $user, array(), getSiteQuotaUsedForTesting());

		$countryRepo = new \repositories\CountryRepository();
		$gb = $countryRepo->loadByTwoCharCode("GB");

		$areaParent = new \models\AreaModel();
		$areaParent->setTitle("Parent");

		$areaChild = new \models\AreaModel();
		$areaChild->setTitle("Child");

		$areaOtherChild = new \models\AreaModel();
		$areaOtherChild->setTitle("Other Child");

		$areaRepo = new \repositories\AreaRepository();
		$areaRepo->create($areaParent, null, $site, $gb);
		$areaRepo->create($areaChild, $areaParent, $site, $gb);
		$areaRepo->create($areaOtherChild, $areaParent, $site, $gb);

		$areaRepo->buildCacheAreaHasParent($areaParent);
		$areaRepo->buildCacheAreaHasParent($areaChild);
		$areaRepo->buildCacheAreaHasParent($areaOtherChild);

		$event = new EventModel();
		$event->setSummary("test");
		$event->setDescription("test test");
		$event->setStartAt($this->mktime(2014,5,10,19,0,0,'Europe/London'));
		$event->setEndAt($this->mktime(2014,5,10,21,0,0,'Europe/London'));
		$event->setUrl("http://www.info.com");
		$event->setTicketUrl("http://www.tickets.com");

		$eventRepository = new EventRepository();
		$eventRepository->create($event, $site, $user);


		$human = new \com\meetyournextmp\models\HumanModel();
		$human->setTitle("Bob");

		$humanRepo = new \com\meetyournextmp\repositories\HumanRepository();
		$humanRepo->create($human, $site, null);
		$humanRepo->addHumanToArea($human, $areaChild, null);
		$humanRepo->addHumanToEvent($human, $event, null);

		$userWatchesAreaRepo = new \repositories\UserWatchesAreaRepository();

		//////////////////////////////// TEST ALL EVENTS

		$erb = new \repositories\builders\EventRepositoryBuilder();
		$events = $erb->fetchAll();
		$this->assertEquals(1, count($events));


		$hrb = new \repositories\builders\HistoryRepositoryBuilder();
		$hrb->setIncludeAreaHistory(false);
		$hrb->setIncludeEventHistory(true);
		$histories = $hrb->fetchAll();
		$this->assertEquals(1, count($histories));



		//////////////////////////////// TEST OTHER CHILD


		$erb = new \repositories\builders\EventRepositoryBuilder();
		$erb->setArea($areaOtherChild);
		$events = $erb->fetchAll();
		$this->assertEquals(0, count($events));


		$hrb = new \repositories\builders\HistoryRepositoryBuilder();
		$hrb->getHistoryRepositoryBuilderConfig()->setArea($areaOtherChild);
		$hrb->setIncludeAreaHistory(false);
		$hrb->setIncludeEventHistory(true);
		$histories = $hrb->fetchAll();
		$this->assertEquals(0, count($histories));

		//////////////////////////////// TEST CHILD

		$erb = new \repositories\builders\EventRepositoryBuilder();
		$erb->setArea($areaChild);
		$events = $erb->fetchAll();
		$this->assertEquals(1, count($events));



		$hrb = new \repositories\builders\HistoryRepositoryBuilder();
		$hrb->getHistoryRepositoryBuilderConfig()->setArea($areaChild);
		$hrb->setIncludeAreaHistory(false);
		$hrb->setIncludeEventHistory(true);
		$histories = $hrb->fetchAll();
		$this->assertEquals(1, count($histories));

		//////////////////////////////// TEST PARENT

		$erb = new \repositories\builders\EventRepositoryBuilder();
		$erb->setArea($areaChild);
		$events = $erb->fetchAll();
		$this->assertEquals(1, count($events));


		$hrb = new \repositories\builders\HistoryRepositoryBuilder();
		$hrb->getHistoryRepositoryBuilderConfig()->setArea($areaChild);
		$hrb->setIncludeAreaHistory(false);
		$hrb->setIncludeEventHistory(true);
		$histories = $hrb->fetchAll();
		$this->assertEquals(1, count($histories));

		//////////////////////////////// USER WATCHES NOTHING

		$erb = new \repositories\builders\EventRepositoryBuilder();
		$erb->setUserAccount($userWatching, false, true, true, true);
		$events = $erb->fetchAll();
		$this->assertEquals(0, count($events));


		//////////////////////////////// USER WATCHES PARENT

		$userWatchesAreaRepo->startUserWatchingArea($userWatching, $areaParent);

		$erb = new \repositories\builders\EventRepositoryBuilder();
		$erb->setUserAccount($userWatching, false, true, true, true);
		$events = $erb->fetchAll();
		$this->assertEquals(1, count($events));

		//////////////////////////////// USER WATCHES CHILD

		$userWatchesAreaRepo->stopUserWatchingArea($userWatching, $areaParent);
		$userWatchesAreaRepo->startUserWatchingArea($userWatching, $areaChild);

		$erb = new \repositories\builders\EventRepositoryBuilder();
		$erb->setUserAccount($userWatching, false, true, true, true);
		$events = $erb->fetchAll();
		$this->assertEquals(1, count($events));

	}




}
