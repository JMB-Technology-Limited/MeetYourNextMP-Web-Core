<?php

namespace org\openacalendar\comments\tasks;

use org\openacalendar\comments\models\AreaCommentHistoryModel;
use org\openacalendar\comments\repositories\AreaCommentHistoryRepository;
use Silex\Application;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class UpdateAreaCommentHistoryChangeFlagsTask extends \BaseTask {

	public function getExtensionId()
	{
		return 'org.openacalendar.comments';
	}

	public function getTaskId()
	{
		return 'UpdateAreaCommentHistoryChangeFlagsTask';
	}

	public function getShouldRunAutomaticallyNow() {
		return $this->getLastRunEndedAgoInSeconds() > 1800; // 30 mins
	}

	protected function run() {

		$areaHistoryRepo = new AreaCommentHistoryRepository();
		$stat = $this->app['db']->prepare("SELECT * FROM area_comment_history");
		$stat->execute();
		$count = 0;
		while($data = $stat->fetch()) {
			$areaHistory = new AreaCommentHistoryModel();
			$areaHistory->setFromDataBaseRow($data);

			$areaHistoryRepo->ensureChangedFlagsAreSet($areaHistory);
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
