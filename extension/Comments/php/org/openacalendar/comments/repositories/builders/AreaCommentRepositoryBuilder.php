<?php

namespace org\openacalendar\comments\repositories\builders;

use org\openacalendar\comments\models\AreaCommentModel;
use repositories\builders\BaseRepositoryBuilder;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class AreaCommentRepositoryBuilder extends BaseRepositoryBuilder {

	/** @var AreaModel **/
	protected $area;

	protected $includeParentAreas = false;

	protected $includeChildrenAreas = false;

	/**
	 * @param \org\openacalendar\comments\repositories\builders\AreaModel $area
	 */
	public function setArea($area, $includeParentAreas = false, $includeChildrenAreas=false)
	{
		$this->area = $area;
		$this->includeParentAreas = $includeParentAreas;
		$this->includeChildrenAreas = $includeChildrenAreas;
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

		$this->select[] = " area_comment_information.*  ";
		$this->select[] = " user_account_information.username AS user_account_username  ";
		$this->select[] = " area_information.slug AS area_information_slug ";
		$this->select[] = " area_information.title AS area_information_title ";
		$this->joins[] = "  LEFT JOIN area_information ON area_information.id = area_comment_information.area_id    ";
		$this->joins[] = "   LEFT JOIN user_account_information ON user_account_information.id = area_comment_information.user_account_id   ";

		if ($this->area) {
			if ($this->includeChildrenAreas && $this->includeParentAreas) {
				$this->joins[] = " LEFT JOIN cached_area_has_parent AS cached_area_has_parent_1 ON ".
					"cached_area_has_parent_1.area_id = area_comment_information.area_id AND cached_area_has_parent_1.has_parent_area_id = :area_id ";
				$this->joins[] = " LEFT JOIN cached_area_has_parent AS cached_area_has_parent_2 ON ".
					"cached_area_has_parent_2.has_parent_area_id = area_comment_information.area_id AND cached_area_has_parent_2.area_id = :area_id ";
				$this->where[] = " ( area_comment_information.area_id = :area_id OR  ".
					"cached_area_has_parent_1.has_parent_area_id = :area_id OR  cached_area_has_parent_2.area_id = :area_id )";
			} else if ($this->includeChildrenAreas) {
				$this->joins[] = " LEFT JOIN cached_area_has_parent ON ".
					"cached_area_has_parent.area_id = area_comment_information.area_id AND cached_area_has_parent.has_parent_area_id = :area_id   ";
				$this->where[] = " ( area_comment_information.area_id = :area_id OR  cached_area_has_parent.has_parent_area_id = :area_id  )";
			} else if ($this->includeParentAreas) {
				$this->joins[] = " LEFT JOIN cached_area_has_parent ON ".
					"cached_area_has_parent.has_parent_area_id = area_comment_information.area_id AND cached_area_has_parent.area_id = :area_id ";
				$this->where[] = " ( area_comment_information.area_id = :area_id OR  cached_area_has_parent.area_id = :area_id  )";
			} else {
				$this->where[] = "  area_comment_information.area_id = :area_id ";
			}
			$this->params['area_id'] = $this->area->getId();
		}

		if (!$this->include_deleted) {
			$this->where[] = " area_comment_information.is_deleted = '0' ";
		}

		if (!$this->include_closed_by_admin) {
			$this->where[] = " area_comment_information.is_closed_by_admin = '0' ";
		}
	}

	protected function buildStat() {
				global $DB;



		$sql = "SELECT ".implode(",", $this->select)." FROM area_comment_information ".
			implode(" ",$this->joins).
				($this->where?" WHERE ".implode(" AND ", $this->where):"").
				" ORDER BY area_comment_information.created_at ASC ".($this->limit > 0 ? " LIMIT ".$this->limit : "");

		//print $sql;
		//var_dump($this->params);
		//die();

		$this->stat = $DB->prepare($sql);
		$this->stat->execute($this->params);
	}


	public function fetchAll() {

		$this->buildStart();
		$this->build();
		$this->buildStat();



		$results = array();
		while($data = $this->stat->fetch()) {
			$ecm = new AreaCommentModel();
			$ecm->setFromDataBaseRow($data);
			$results[] = $ecm;
		}
		return $results;

	}


}
