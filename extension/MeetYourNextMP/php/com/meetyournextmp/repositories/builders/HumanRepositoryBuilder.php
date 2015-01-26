<?php

namespace com\meetyournextmp\repositories\builders;

use com\meetyournextmp\models\HumanModel;
use models\AreaModel;
use models\SiteModel;
use models\EventModel;
use repositories\builders\BaseRepositoryBuilder;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class HumanRepositoryBuilder  extends BaseRepositoryBuilder {
	

	/** @var SiteModel **/
	protected $site;
	
	public function setSite(SiteModel $site) {
		$this->site = $site;
	}
	
	
	/** @var EventModel **/
	protected $humansForEvent;
	
	public function setHumansForEvent(EventModel $event) {
		$this->humansForEvent = $event;
	}
	
	
	/** @var EventModel **/
	protected $humansNotForEvent;
	
	public function setHumansNotForEvent(EventModel $event) {
		$this->humansNotForEvent = $event;
	}

	/** @var AreaModel **/
	protected $area;

	/**
	 * @param \models\AreaModel $area
	 */
	public function setArea($area)
	{
		$this->area = $area;
	}

	protected $include_deleted = true;

	public function setIncludeDeleted($value) {
		$this->include_deleted = $value;
	}

	protected $freeTextSearch;

	public function setFreeTextsearch($freeTextSearch) {
		$this->freeTextSearch = $freeTextSearch;
	}

	protected function build() {

		if ($this->site) {
			$this->where[] =  " human_information.site_id = :site_id ";
			$this->params['site_id'] = $this->site->getId();
		}
		
		if ($this->humansForEvent) {
			$this->joins[] = "  JOIN event_has_human ON event_has_human.human_id = human_information.id AND  event_has_human.event_id = :event_id AND event_has_human.removed_at IS NULL";
			$this->params['event_id'] = $this->humansForEvent->getId();
		} else if ($this->humansNotForEvent) {
			$this->joins[] = " LEFT JOIN event_has_human ON event_has_human.human_id = human_information.id AND  event_has_human.event_id = :event_id AND event_has_human.removed_at IS NULL";
			$this->params['event_id'] = $this->humansNotForEvent->getId();
			$this->where[] = ' event_has_human.event_id IS NULL ';
		}

		if ($this->area) {
			// TODO direct areas only, should do child areas to. But not now.
			$this->joins[] = "  JOIN human_in_area ON human_in_area.human_id = human_information.id AND  human_in_area.area_id = :area_id AND human_in_area.removed_at IS NULL";
			$this->params['area_id'] = $this->area->getId();
		}
		
		if (!$this->include_deleted) {
			$this->where[] = " human_information.is_deleted = '0' ";
		}

		if ($this->freeTextSearch) {
			$this->where[] =  '(CASE WHEN human_information.title IS NULL THEN \'\' ELSE human_information.title END )  || \' \' || '.
					'(CASE WHEN human_information.description IS NULL THEN \'\' ELSE human_information.description END )'.
					' ILIKE :free_text_search ';
			$this->params['free_text_search'] = "%".strtolower($this->freeTextSearch)."%";
		}
	}
	
	protected function buildStat() {
				global $DB;
		
		
		
		$sql = "SELECT human_information.* FROM human_information ".
				implode(" ",$this->joins).
				($this->where?" WHERE ".implode(" AND ", $this->where):"").
				" ORDER BY human_information.title ASC ".( $this->limit > 0 ? " LIMIT ". $this->limit : "");
	
		$this->stat = $DB->prepare($sql);
		$this->stat->execute($this->params);
	}
	
	
	public function fetchAll() {
		
		$this->buildStart();
		$this->build();
		$this->buildStat();
		

		
		$results = array();
		while($data = $this->stat->fetch()) {
			$human = new HumanModel();
			$human->setFromDataBaseRow($data);
			$results[] = $human;
		}
		return $results;
		
	}

}

