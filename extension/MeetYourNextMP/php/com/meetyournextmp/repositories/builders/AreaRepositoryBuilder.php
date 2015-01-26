<?php

namespace com\meetyournextmp\repositories\builders;

use com\meetyournextmp\models\HumanModel;
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


class AreaRepositoryBuilder extends \repositories\builders\AreaRepositoryBuilder {

	/** @var  HumanModel */
	protected $human;

	/**
	 * @param \com\meetyournextmp\models\HumanModel $human
	 */
	public function setHuman($human)
	{
		$this->human = $human;
	}


	protected $isMapItAreaOnly = false;

	/**
	 * @param boolean $isMapItAreaOnly
	 */
	public function setIsMapItAreaOnly($isMapItAreaOnly)
	{
		$this->isMapItAreaOnly = $isMapItAreaOnly;
	}


	protected $includeAreasWithNoEventsOnly = false;

	/**
	 * @param boolean $includeAreasWithNoEventsOnly
	 */
	public function setIncludeAreasWithNoEventsOnly($includeAreasWithNoEventsOnly)
	{
		$this->includeAreasWithNoEventsOnly = $includeAreasWithNoEventsOnly;
	}




	protected function build()
	{
		parent::build();

		if ($this->isMapItAreaOnly) {
			$this->joins[] = " JOIN area_mapit_information ON area_mapit_information.area_id = area_information.id ";
		}

		if ($this->human) {
			$this->joins[] = "  JOIN human_in_area ON human_in_area.area_id = area_information.id AND  human_in_area.human_id = :human_id AND human_in_area.removed_at IS NULL";
			$this->params['human_id'] = $this->human->getId();
		}

		if ($this->includeAreasWithNoEventsOnly) {

			$this->joins[] = 	" LEFT JOIN meetyournextmp_event_in_area  ON meetyournextmp_event_in_area.area_id = area_information.id ";
			$this->joins[] = " LEFT JOIN event_information ON event_information.id = meetyournextmp_event_in_area.event_id ".
				"AND event_information.is_deleted = '0' AND event_information.is_cancelled ='0'";
			$this->where[] = "  event_information.id IS NULL  ";
		}

	}

}



