<?php

namespace org\openacalendar\comments\repositories\builders;

use org\openacalendar\comments\models\EventCommentModel;
use repositories\builders\BaseRepositoryBuilder;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class EventCommentRepositoryBuilder extends BaseRepositoryBuilder {

	/** @var EventModel **/
	protected $event;

	/**
	 * @param \org\openacalendar\comments\repositories\builders\EventModel $event
	 */
	public function setEvent($event)
	{
		$this->event = $event;
	}

	protected $include_deleted = false;

	public function setIncludeDeleted($value) {
		$this->include_deleted = $value;
	}


	protected $include_closed_by_admin = false;

	/**
	 * @param boolean $include_closed_by_admin
	 */
	public function setIncludeClosedByAdmin($include_closed_by_admin)
	{
		$this->include_closed_by_admin = $include_closed_by_admin;
	}




	protected function build() {

		if ($this->event) {
			$this->where[] = "  event_comment_information.event_id = :event_id ";
			$this->params['event_id'] = $this->event->getId();
		}

		if (!$this->include_deleted) {
			$this->where[] = " event_comment_information.is_deleted = '0' ";
		}

		if (!$this->include_closed_by_admin) {
			$this->where[] = " event_comment_information.is_closed_by_admin = '0' ";
		}
	}

	protected function buildStat() {
				global $DB;



		$sql = "SELECT event_comment_information.* , user_account_information.username AS user_account_username FROM event_comment_information ".
			" LEFT JOIN user_account_information ON user_account_information.id = event_comment_information.user_account_id ".
			implode(" ",$this->joins).
				($this->where?" WHERE ".implode(" AND ", $this->where):"").
				" ORDER BY event_comment_information.created_at ASC ";

		$this->stat = $DB->prepare($sql);
		$this->stat->execute($this->params);
	}


	public function fetchAll() {

		$this->buildStart();
		$this->build();
		$this->buildStat();



		$results = array();
		while($data = $this->stat->fetch()) {
			$ecm = new EventCommentModel();
			$ecm->setFromDataBaseRow($data);
			$results[] = $ecm;
		}
		return $results;

	}


}
