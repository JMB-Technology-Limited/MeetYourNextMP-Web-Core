<?php

namespace org\openacalendar\comments;

use org\openacalendar\comments\models\AreaCommentHistoryModel;
use org\openacalendar\comments\newsfeedmodels\AreaCommentHistoryNewsFeedModel;
use org\openacalendar\comments\repositories\AreaCommentHistoryRepository;
use org\openacalendar\comments\repositories\builders\HistoryRepositoryBuilder;
use org\openacalendar\comments\userpermissions\CommentsChangeUserPermission;
use Silex\Application;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class ExtensionComments extends \BaseExtension {

	function __construct(Application $app)
	{
		parent::__construct($app);
	}


	public function getId() {
		return 'org.openacalendar.comments';
	}

	public function getTitle() {
		return "Comments";
	}

	public function getDescription() {
		return "Comments";
	}

	public function getTasks() {
		return array(
			new \org\openacalendar\comments\tasks\UpdateAreaCommentHistoryChangeFlagsTask($this->app),
		);
	}


	public function getAddContentToEventShowPages($parameters) {
		return array(
			new AddCommentsContentToEventShowPage($parameters, $this->app),
		);
	}

	public function getHistoryRepositoryBuilderData(\repositories\builders\config\HistoryRepositoryBuilderConfig $historyRepositoryBuilderConfig) {
		$hrb = new HistoryRepositoryBuilder($historyRepositoryBuilderConfig);
		return $hrb->fetchAll();
	}


	public function getNewsFeedModel( $interfaceHistoryModel) { // TODO can't set type InterfaceHistoryModel!!!!!!!
		if ($interfaceHistoryModel instanceof AreaCommentHistoryModel) {
			return new AreaCommentHistoryNewsFeedModel($interfaceHistoryModel);
		}
	}

	public function getUserPermissions() {
		return array('COMMENTS_CHANGE');
	}

	public function getUserPermission($key) {
		if ($key == 'COMMENTS_CHANGE') {
			return new CommentsChangeUserPermission();
		}
	}

	public function makeSureHistoriesAreCorrect( $interfaceHistoryModel) {  // @TODO InterfaceHistoryModel type!!!!!!
		if ($interfaceHistoryModel instanceof \org\openacalendar\comments\models\AreaCommentHistoryModel) {
			$repo = new AreaCommentHistoryRepository();
			$repo->ensureChangedFlagsAreSet($interfaceHistoryModel);
		}
	}

}
