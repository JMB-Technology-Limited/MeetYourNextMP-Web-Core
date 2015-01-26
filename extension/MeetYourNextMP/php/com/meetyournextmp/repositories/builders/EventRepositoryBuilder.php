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


class EventRepositoryBuilder extends \repositories\builders\EventRepositoryBuilder {



	
	
	/** @var HumanModel **/
	protected $human;
	
	public function setHuman(HumanModel $human) {
		$this->human = $human;
	}

	protected $includeNoHumansOnly = false;

	/**
	 * @param boolean $includeNoHumansOnly
	 */
	public function setIncludeNoHumansOnly($includeNoHumansOnly)
	{
		$this->includeNoHumansOnly = $includeNoHumansOnly;
	}




	protected function build()
	{
		parent::build();

		
		if ($this->human) {
			$this->joins[] = "  JOIN event_has_human ON event_has_human.event_id = event_information.id AND  event_has_human.human_id = :human_id AND event_has_human.removed_at IS NULL";
			$this->params['human_id'] = $this->human->getId();	
		} else if ($this->includeNoHumansOnly) {
			$this->joins[] = "  LEFT JOIN event_has_human ON event_has_human.event_id = event_information.id AND event_has_human.removed_at IS NULL";
			$this->where[] = "   event_has_human.added_at IS NULL ";
		}
		
	}


}
