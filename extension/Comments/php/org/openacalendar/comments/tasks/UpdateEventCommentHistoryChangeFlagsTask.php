<?php

namespace org\openacalendar\comments\tasks;

use org\openacalendar\comments\models\EventCommentHistoryModel;
use org\openacalendar\comments\repositories\EventCommentHistoryRepository;
use Silex\Application;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class UpdateEventCommentHistoryChangeFlagsTask extends \BaseTask {

	public function getExtensionId()
	{
		return 'org.openacalendar.comments';
	}

	public function getTaskId()
	{
		return 'UpdateEventCommentHistoryChangeFlagsTask';
	}

	public function getShouldRunAutomaticallyNow() {
		return $this->getLastRunEndedAgoInSeconds() > 1800; // 30 mins
	}

	protected function run() {

		$eventHistoryRepo = new EventCommentHistoryRepository();
		$stat = $this->app['db']->prepare("SELECT * FROM event_comment_history");
		$stat->execute();
		$count = 0;
		while($data = $stat->fetch()) {
			$eventHistory = new EventCommentHistoryModel();
			$eventHistory->setFromDataBaseRow($data);

			$eventHistoryRepo->ensureChangedFlagsAreSet($eventHistory);
			++$count;
		}


		return array('result'=>'ok','count'=>$count);
	}

	public function getResultDataAsString(\models\TaskLogModel $taskLogModel) {
		if ($taskLogModel->getIsResultDataHaveKey("result") && $taskLogModel->getResultDataValue("result") == "ok") {
			return "Ok";
		} else {
			return "Fail";
		}

	}




}
