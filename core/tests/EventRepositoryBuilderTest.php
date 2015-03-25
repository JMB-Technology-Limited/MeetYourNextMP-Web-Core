<?php

use models\EventModel;
use models\UserAccountModel;
use models\SiteModel;
use models\AreaModel;
use repositories\EventRepository;
use repositories\UserAccountRepository;
use repositories\SiteRepository;
use repositories\CountryRepository;
use repositories\AreaRepository;
use repositories\builders\EventRepositoryBuilder;


/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */




class EventRepositoryBuilderTest  extends \BaseAppWithDBTest {

	
	
	function testFilterAreaAndIncludeAreaAndIncludeVenue() {
		$this->addCountriesToTestDB();
		
		$countryRepo = new CountryRepository();
		$areaRepo = new AreaRepository();
		$userRepo = new UserAccountRepository();
		$siteRepo = new SiteRepository();
		
		$user = new UserAccountModel();
		$user->setEmail("test@jarofgreen.co.uk");
		$user->setUsername("test");
		$user->setPassword("password");
		
		$userRepo->create($user);
		
		$site = new SiteModel();
		$site->setTitle("Test");
		$site->setSlug("test");
		
		$siteRepo->create($site, $user, array( $countryRepo->loadByTwoCharCode('GB') ), $this->getSiteQuotaUsedForTesting());
		
		$area = new AreaModel();
		$area->setTitle("test");
		$area->setDescription("test test");
		
		$areaRepo->create($area, null, $site, $countryRepo->loadByTwoCharCode('GB') , $user);
		$areaRepo->buildCacheAreaHasParent($area);
		
		
		
		######################## For now just test it doesn't crash, I commited a bug that did crash here
		
		$erb = new EventRepositoryBuilder();
		$erb->setArea($area);
		$erb->setIncludeVenueInformation(true);
		$erb->setIncludeAreaInformation(true);
		$erb->fetchAll();

	}
	

	function testFreeTextSearch() {

		TimeSource::mock(2014,5,1,7,0,0);

		$user = new UserAccountModel();
		$user->setEmail("test@jarofgreen.co.uk");
		$user->setUsername("test");
		$user->setPassword("password");

		$userRepo = new UserAccountRepository();
		$userRepo->create($user);

		$site = new SiteModel();
		$site->setTitle("Test");
		$site->setSlug("test");

		$siteRepo = new SiteRepository();
		$siteRepo->create($site, $user, array(), $this->getSiteQuotaUsedForTesting());

		$event = new EventModel();
		$event->setSummary("test");
		$event->setDescription("test test");
		$event->setStartAt(getUTCDateTime(2014,5,10,19,0,0,'Europe/London'));
		$event->setEndAt(getUTCDateTime(2014,5,10,21,0,0,'Europe/London'));
		$event->setUrl("http://www.info.com");
		$event->setTicketUrl("http://www.tickets.com");

		$eventRepository = new EventRepository();
		$eventRepository->create($event, $site, $user);

		///////////// Test No Search
		$erb = new EventRepositoryBuilder();
		$this->assertEquals(1, count($erb->fetchAll()));

		$erb = new EventRepositoryBuilder();
		$this->assertEquals(1, $erb->fetchCount());


		///////////// Test Search Pass
		$erb = new EventRepositoryBuilder();
		$erb->setFreeTextsearch("test");
		$this->assertEquals(1, count($erb->fetchAll()));

		$erb = new EventRepositoryBuilder();
		$erb->setFreeTextsearch("test");
		$this->assertEquals(1, $erb->fetchCount());

		///////////// Test Search Fail
		$erb = new EventRepositoryBuilder();
		$erb->setFreeTextsearch("eodueoth dlhtunkn ethh5f 8l79,35dheutn");
		$this->assertEquals(0, count($erb->fetchAll()));

		$erb = new EventRepositoryBuilder();
		$erb->setFreeTextsearch("eodueoth dlhtunkn ethh5f 8l79,35dheutn");
		$this->assertEquals(0, $erb->fetchCount());


	}



}
