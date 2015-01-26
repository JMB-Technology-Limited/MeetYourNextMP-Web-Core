<?php

namespace org\openacalendar\comments\newsfeedmodels;

use api1exportbuilders\TraitATOM;
use models\AreaCommentHistoryModel;

/**
 *
 * @package Core
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */


class AreaCommentHistoryNewsFeedModel implements  \InterfaceNewsFeedModel {


	/** @var AreaCommentHistoryModel */
	protected $areaCommentHistoryModel;

	function __construct($areaCommentHistoryModel)
	{
		$this->areaCommentHistoryModel = $areaCommentHistoryModel;
	}


	/** @return \DateTime */
	public function getCreatedAt()
	{
		return $this->areaCommentHistoryModel->getCreatedAt();
	}

	public function getID()
	{
		// For ID, must make sure we use Slug, not SlugForURL otherwise ID will change!
		return $this->getURL().'/history/'.$this->areaCommentHistoryModel->getCreatedAtTimeStamp();
	}

	public function getURL()
	{
		global $CONFIG;
		return $CONFIG->isSingleSiteMode ?
			'http://'.$CONFIG->webSiteDomain.'/area/'.$this->areaCommentHistoryModel->getArea()->getSlug().'/comment' :
			'http://'.$this->areaCommentHistoryModel->getSiteSlug().".".$CONFIG->webSiteDomain.
				'/area/'.$this->areaCommentHistoryModel->getArea()->getSlug().'/comment';
	}

	public function getTitle()
	{
		return $this->areaCommentHistoryModel->getTitle() ? $this->areaCommentHistoryModel->getTitle() : "Comment";
	}

	public function getSummary()
	{
		$txt = '';

		// TODO if isCurrentlyDeleted

		if ($this->areaCommentHistoryModel->getIsNew()) {
			$txt .= 'New! '."\n";
		}
		if ($this->areaCommentHistoryModel->isAnyChangeFlagsUnknown()) {
			$txt .= $this->areaCommentHistoryModel->getComment();
		} else {
			if ($this->areaCommentHistoryModel->getTitleChanged()) {
				$txt .= 'Title Changed. '."\n";
			}
			if ($this->areaCommentHistoryModel->getCommentChanged()) {
				$txt .= 'Comment Changed. '."\n";
			}
			if ($this->areaCommentHistoryModel->getIsDeletedChanged()) {
				$txt .= 'Deleted Changed: '.($this->areaCommentHistoryModel->getIsDeleted() ? "Deleted":"Restored")."\n\n";
			}
		}
		return $txt;
	}
}

