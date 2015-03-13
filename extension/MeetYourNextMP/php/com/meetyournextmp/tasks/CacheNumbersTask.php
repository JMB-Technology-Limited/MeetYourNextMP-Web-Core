<?php

namespace com\meetyournextmp\tasks;
use com\meetyournextmp\models\HumanPopItInfoModel;
use com\meetyournextmp\repositories\builders\EventRepositoryBuilder;
use com\meetyournextmp\repositories\HumanRepository;
use reports\SeriesOfValueByTimeReport;
use repositories\AreaRepository;
use Silex\Application;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class CacheNumbersTask extends \BaseTask {


	public function getExtensionId()
	{
		return "com.meetyournextmp";
	}

	public function getTaskId()
	{
		return "CacheNumbersTask";
	}

	public function getShouldRunAutomaticallyNow() {
		return $this->getLastRunEndedAgoInSeconds() > 4*60*60; // TODO $config
	}

	protected function run()
	{

		$siteRepo = new \repositories\SiteRepository();
		$site = $siteRepo->loadById($this->app['config']->singleSiteID); // TODO assumes single site!

		$out = array();

		$erb = new EventRepositoryBuilder();
		$erb->setIncludeDeleted(false);
		$erb->setIncludeCancelled(false);
		$erb->setSite($site);
		$out['countEventsTotal'] = count($erb->fetchAll());

		$erb = new EventRepositoryBuilder();
		$erb->setIncludeDeleted(false);
		$erb->setIncludeCancelled(false);
		$erb->setSite($site);
		$erb->setBefore($this->app['timesource']->getDateTime());
		$out['countEventsBeforeNow'] = count($erb->fetchAll());

		$erb = new EventRepositoryBuilder();
		$erb->setIncludeDeleted(false);
		$erb->setIncludeCancelled(false);
		$erb->setSite($site);
		$erb->setAfterNow();
		$out['countEventsAfterNow'] = count($erb->fetchAll());

		$arb = new \com\meetyournextmp\repositories\builders\AreaRepositoryBuilder();
		$arb->setIsMapItAreaOnly(true);
		$arb->setIncludeDeleted(false);
		$arb->setIncludeAreasWithNoEventsOnly(true);
		$arb->setLimit(800);
		$out['countSeatsWithNoEvents'] = count($arb->fetchAll());

		$areaRepo = new AreaRepository();

		foreach(array(3=>'countEventsInScotland',1=>'countEventsInEngland',2=>'countEventsInWales',4=>'countEventsInNIreland',712=>'countEventsInGreaterLondon') as $areaSlug=>$key) {
			$erb = new EventRepositoryBuilder();
			$erb->setIncludeDeleted(false);
			$erb->setIncludeCancelled(false);
			$erb->setSite($site);
			$erb->setArea($areaRepo->loadBySlug($site, $areaSlug));
			$out[$key] = count($erb->fetchAll());
		}

		// =================================== Events by day
		$report = $this->getValueReport('com.meetyournextmp','NonDeletedNonCancelledEventsStartAtReport', $this->app);
		$startAt = \TimeSource::getDateTime();
		$startAt->setTime(0,0,0);
		$endAt = new \DateTime('2015-05-07 10:00:00');
		$period = "P1D";
		$report->setFilterSiteId($this->app['config']->singleSiteID);

		$reportByTime = new SeriesOfValueByTimeReport($report, $startAt, $endAt, $period);
		$reportByTime->run();

		$out['countEventsByDay'] = array();
		foreach($reportByTime->getData() as $data) {
			$out['countEventsByDay'][] = array(
				'count'=>$data->getData(),
				'date'=>$data->getLabelStart()->format('D d F Y'),
			);
		}

		// =================================== Users with edits
		$report = $this->getSeriesReport("org.openacalendar","UsersWithEventsEdited",$this->app);
		$report->run();

		$out['userEventsEdited'] = array();
		foreach($report->getData() as $data) {
			$out['userEventsEdited'][] = array(
				'count'=>$data->getData(),
				'userID'=>$data->getLabelID(),
				'userUserName'=>$data->getLabelText(),
			);
		}










		//var_dump($out);



		file_put_contents(APP_ROOT_DIR.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'numbers.json', json_encode($out));



		return array('result'=>'ok');
	}

	function getValueReport($extid, $reportId, Application $app) {
		$extension = $app['extensions']->getExtensionById($extid);
		if (!$extension) return;
		foreach($extension->getValueReports() as $report) {
			if ($report->getReportID() == $reportId && $report->getHasFilterTime()) {
				return $report;
			}
		}
	}

	function getSeriesReport($extid, $reportId, Application $app) {
		$extension = $app['extensions']->getExtensionById($extid);
		if (!$extension) return;
		foreach($extension->getSeriesReports() as $report) {
			if ($report->getReportID() == $reportId && $report->getHasFilterTime()) {
				return $report;
			}
		}
	}


}


