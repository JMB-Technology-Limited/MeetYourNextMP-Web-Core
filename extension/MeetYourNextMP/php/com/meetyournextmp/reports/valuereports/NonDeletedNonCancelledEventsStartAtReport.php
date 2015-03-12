<?php

namespace com\meetyournextmp\reports\valuereports;

use BaseValueReport;


/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class NonDeletedNonCancelledEventsStartAtReport extends BaseValueReport {

	function __construct()
	{
		$this->hasFilterTime = true;
		$this->hasFilterSite = true;
	}

	public function getExtensionID() { 	return 'com.meetyournextmp';}

	public function getReportTitle()
	{
		return "Non-deleted Non-cancelled Events Start At";
	}

	public function getReportID()
	{
		return "NonDeletedNonCancelledEventsStartAtReport";
	}

	public function run()
	{

		global $DB;

		$where = array("event_information.is_cancelled = '0' ","event_information.is_deleted = '0'");
		$params = array();

		if ($this->filterTimeStart) {
			$where[] = " event_information.start_at >= :start_at ";
			$params['start_at'] = $this->filterTimeStart->format("Y-m-d H:i:s");
		}


		if ($this->filterTimeEnd) {
			$where[] = " event_information.start_at <= :end_at ";
			$params['end_at'] = $this->filterTimeEnd->format("Y-m-d H:i:s");
		}

		if ($this->filterSiteId) {
			$where[] = " event_information.site_id = :site_id ";
			$params['site_id'] = $this->filterSiteId;
		}

		$sql = "SELECT COUNT(*) AS count  ".
			" FROM event_information ".
			($where ? " WHERE " . implode(" AND ",$where) : "");

		$stat = $DB->prepare($sql);
		$stat->execute($params);
		$data = $stat->fetch();
		$this->data = $data['count'];

	}

}
