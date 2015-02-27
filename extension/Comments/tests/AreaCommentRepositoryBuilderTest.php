<?php
use models\SiteModel;
use models\UserAccountModel;
use repositories\SiteRepository;
use repositories\UserAccountRepository;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class AreaCommentRepositoryBuilderTest extends \PHPUnit_Framework_TestCase {


	function testParentChildSearches() {

		$DB = getNewTestDB();
		addCountriesToTestDB();

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
		$siteRepo->create($site, $user, array(), getSiteQuotaUsedForTesting());

		$countryRepo = new \repositories\CountryRepository();
		$gb = $countryRepo->loadByTwoCharCode("GB");

		$areaParent = new \models\AreaModel();
		$areaParent->setTitle("Parent");

		$areaCentre = new \models\AreaModel();
		$areaCentre->setTitle("Centre");

		$areaChild = new \models\AreaModel();
		$areaChild->setTitle("Child");

		$areaRepo = new \repositories\AreaRepository();
		$areaRepo->create($areaParent, null, $site, $gb);
		$areaRepo->create($areaCentre, $areaParent, $site, $gb);
		$areaRepo->create($areaChild, $areaCentre, $site, $gb);

		$areaRepo->buildCacheAreaHasParent($areaParent);
		$areaRepo->buildCacheAreaHasParent($areaCentre);
		$areaRepo->buildCacheAreaHasParent($areaChild);

		$commentParent = new \org\openacalendar\comments\models\AreaCommentModel();
		$commentParent->setTitle("Parent");

		$commentCentre = new \org\openacalendar\comments\models\AreaCommentModel();
		$commentCentre->setTitle("Centre");

		$commentChild = new \org\openacalendar\comments\models\AreaCommentModel();
		$commentChild->setTitle("Child");

		$areaCommentRepo = new \org\openacalendar\comments\repositories\AreaCommentRepository();

		TimeSource::mock(2014,5,1,7,0,0);
		$areaCommentRepo->create($commentParent, $areaParent, $user);
		TimeSource::mock(2014,5,1,7,0,1);
		$areaCommentRepo->create($commentCentre, $areaCentre, $user);
		TimeSource::mock(2014,5,1,7,0,2);
		$areaCommentRepo->create($commentChild, $areaChild, $user);



		##################################### Test Centre

		$areaCommentRepoBuilder = new \org\openacalendar\comments\repositories\builders\AreaCommentRepositoryBuilder();
		$areaCommentRepoBuilder->setArea($areaCentre, false, false);
		$areaComments = $areaCommentRepoBuilder->fetchAll();

		$this->assertEquals(1, count($areaComments));
		$this->assertEquals($commentCentre->getId(), $areaComments[0]->getId());

		##################################### Test Centre + Parents

		$areaCommentRepoBuilder = new \org\openacalendar\comments\repositories\builders\AreaCommentRepositoryBuilder();
		$areaCommentRepoBuilder->setArea($areaCentre, true, false);
		$areaComments = $areaCommentRepoBuilder->fetchAll();

		$this->assertEquals(2, count($areaComments));
		$this->assertEquals($commentParent->getId(), $areaComments[0]->getId());
		$this->assertEquals($commentCentre->getId(), $areaComments[1]->getId());

		##################################### Test Centre + Children

		$areaCommentRepoBuilder = new \org\openacalendar\comments\repositories\builders\AreaCommentRepositoryBuilder();
		$areaCommentRepoBuilder->setArea($areaCentre, false, true);
		$areaComments = $areaCommentRepoBuilder->fetchAll();

		$this->assertEquals(2, count($areaComments));
		$this->assertEquals($commentCentre->getId(), $areaComments[0]->getId());
		$this->assertEquals($commentChild->getId(), $areaComments[1]->getId());

		##################################### Test Centre + Parents + Children

		$areaCommentRepoBuilder = new \org\openacalendar\comments\repositories\builders\AreaCommentRepositoryBuilder();
		$areaCommentRepoBuilder->setArea($areaCentre, true, true);
		$areaComments = $areaCommentRepoBuilder->fetchAll();

		$this->assertEquals(3, count($areaComments));
		$this->assertEquals($commentParent->getId(), $areaComments[0]->getId());
		$this->assertEquals($commentCentre->getId(), $areaComments[1]->getId());
		$this->assertEquals($commentChild->getId(), $areaComments[2]->getId());

		##################################### Test Child + Children

		$areaCommentRepoBuilder = new \org\openacalendar\comments\repositories\builders\AreaCommentRepositoryBuilder();
		$areaCommentRepoBuilder->setArea($areaChild, false, true);
		$areaComments = $areaCommentRepoBuilder->fetchAll();

		$this->assertEquals(1, count($areaComments));
		$this->assertEquals($commentChild->getId(), $areaComments[0]->getId());

	}




}
