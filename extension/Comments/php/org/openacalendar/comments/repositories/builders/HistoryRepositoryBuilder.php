<?php

namespace org\openacalendar\comments\repositories\builders;

use org\openacalendar\comments\models\AreaCommentHistoryModel;
use org\openacalendar\comments\models\EventCommentHistoryModel;
use org\openacalendar\comments\models\EventCommentModel;
use repositories\builders\BaseRepositoryBuilder;
use repositories\builders\config\HistoryRepositoryBuilderConfig;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class HistoryRepositoryBuilder {

	/** @var \repositories\builders\config\HistoryRepositoryBuilderConfig  */
	protected $historyRepositoryBuilderConfig;

	function __construct(HistoryRepositoryBuilderConfig $historyRepositoryBuilderConfig = null)
	{
		$this->historyRepositoryBuilderConfig = $historyRepositoryBuilderConfig ? $historyRepositoryBuilderConfig : new HistoryRepositoryBuilderConfig();
	}



	public function fetchAll() {
		global $DB;

		$results = array();

		//var_dump($this->historyRepositoryBuilderConfig);

		if ($this->historyRepositoryBuilderConfig->getIncludeAreaHistory()) {


			/////////////////////////////////////////////////////////// AREA COMMENTS

			$where = array(" area_comment_information.is_closed_by_admin='0' ");
			$joins = array();
			$params = array();

			if ($this->historyRepositoryBuilderConfig->getSite()) {
				$where[] = 'area_information.site_id =:site';
				$params['site'] = $this->historyRepositoryBuilderConfig->getSite()->getId();
			}

			if ($this->historyRepositoryBuilderConfig->getSince()) {
				$where[] = ' area_comment_history.created_at >= :since ';
				$params['since'] = $this->historyRepositoryBuilderConfig->getSince()->format("Y-m-d H:i:s");
			}

			if ($this->historyRepositoryBuilderConfig->getNotUser()) {
				$where[] = 'area_comment_history.user_account_id != :userid ';
				$params['userid'] = $this->historyRepositoryBuilderConfig->getNotUser()->getId();
			}

			if ($this->historyRepositoryBuilderConfig->getApi2app()) {
				$where[] = 'area_comment_history.api2_application_id  = :api2app';
				$params['api2app'] = $this->historyRepositoryBuilderConfig->getApi2app()->getId();
			}

			if ($this->historyRepositoryBuilderConfig->getArea()) {

				$areaids = array( $this->historyRepositoryBuilderConfig->getArea()->getId() );

				$this->statAreas = $DB->prepare("SELECT area_id FROM cached_area_has_parent WHERE has_parent_area_id=:id");
				$this->statAreas->execute(array('id'=>$this->historyRepositoryBuilderConfig->getArea()->getId()));
				while($d = $this->statAreas->fetch()) {
					$areaids[] = $d['area_id'];
				}

				$where[] = ' area_comment_information.area_id IN ('.  implode(",", $areaids).') ';

			}



			$sql = "SELECT area_comment_history.*,  ".
				" area_information.slug AS area_information_slug,  area_information.title AS area_information_title, ".
				" area_comment_information.is_deleted AS is_currently_deleted, ".
				" user_account_information.username AS user_account_username ".
				" FROM area_comment_history ".
				" LEFT JOIN user_account_information ON user_account_information.id = area_comment_history.user_account_id ".
				" LEFT JOIN area_comment_information ON area_comment_information.id = area_comment_history.area_comment_id ".
				" LEFT JOIN area_information ON area_information.id = area_comment_information.area_id ".
				implode(" ",$joins).
				($where ? " WHERE  ".implode(" AND ", $where) : "").
				" ORDER BY area_comment_history.created_at DESC LIMIT ".$this->historyRepositoryBuilderConfig->getLimit();

			// print($sql); var_dump($params);

			$stat = $DB->prepare($sql);
			$stat->execute($params);

			while($data = $stat->fetch()) {
				$eventHistory = new AreaCommentHistoryModel();
				$eventHistory->setFromDataBaseRow($data);
				$results[] = $eventHistory;
			}


			/////////////////////////////////////////////////////////// EVENT COMMENTS

			$where = array(" event_comment_information.is_closed_by_admin='0' ");
			$joins = array();
			$params = array();

			if ($this->historyRepositoryBuilderConfig->getSite()) {
				$where[] = 'event_information.site_id =:site';
				$params['site'] = $this->historyRepositoryBuilderConfig->getSite()->getId();
			}

			if ($this->historyRepositoryBuilderConfig->getSince()) {
				$where[] = ' event_comment_history.created_at >= :since ';
				$params['since'] = $this->historyRepositoryBuilderConfig->getSince()->format("Y-m-d H:i:s");
			}

			if ($this->historyRepositoryBuilderConfig->getNotUser()) {
				$where[] = 'event_comment_history.user_account_id != :userid ';
				$params['userid'] = $this->historyRepositoryBuilderConfig->getNotUser()->getId();
			}

			if ($this->historyRepositoryBuilderConfig->getApi2app()) {
				$where[] = 'event_comment_history.api2_application_id  = :api2app';
				$params['api2app'] = $this->historyRepositoryBuilderConfig->getApi2app()->getId();
			}

			if ($this->historyRepositoryBuilderConfig->getArea()) {

				$areaids = array( $this->historyRepositoryBuilderConfig->getArea()->getId() );

				$this->statAreas = $DB->prepare("SELECT area_id FROM cached_area_has_parent WHERE has_parent_area_id=:id");
				$this->statAreas->execute(array('id'=>$this->historyRepositoryBuilderConfig->getArea()->getId()));
				while($d = $this->statAreas->fetch()) {
					$areaids[] = $d['area_id'];
				}

				$where[] = ' ( event_information.area_id IN ('.  implode(",", $areaids).') OR '.
					' venue_information.area_id IN ('. implode(",", $areaids) .')  ) ';

			}


			$sql = "SELECT event_comment_history.*,  ".
				" event_information.slug AS event_information_slug,  event_information.summary AS event_information_summary, ".
				" event_comment_information.is_deleted AS is_currently_deleted, ".
				" user_account_information.username AS user_account_username ".
				" FROM event_comment_history ".
				" LEFT JOIN user_account_information ON user_account_information.id = event_comment_history.user_account_id ".
				" LEFT JOIN event_comment_information ON event_comment_information.id = event_comment_history.event_comment_id ".
				" LEFT JOIN event_information ON event_information.id = event_comment_information.event_id ".
				" LEFT JOIN venue_information ON venue_information.id = event_information.venue_id ".
				implode(" ",$joins).
				($where ? " WHERE  ".implode(" AND ", $where) : "").
				" ORDER BY event_comment_history.created_at DESC LIMIT ".$this->historyRepositoryBuilderConfig->getLimit();

			// print($sql); var_dump($params);

			$stat = $DB->prepare($sql);
			$stat->execute($params);

			while($data = $stat->fetch()) {
				$eventHistory = new EventCommentHistoryModel();
				$eventHistory->setFromDataBaseRow($data);
				$results[] = $eventHistory;
			}

		}

		return $results;


	}


}
