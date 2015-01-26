<?php

namespace org\openacalendar\comments\site\controllers;

use org\openacalendar\comments\models\EventCommentModel;
use org\openacalendar\comments\repositories\builders\EventCommentRepositoryBuilder;
use org\openacalendar\comments\repositories\EventCommentRepository;
use org\openacalendar\comments\site\forms\NewCommentForm;
use repositories\EventRepository;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class AdminEventController extends \site\controllers\EventController {


	protected $parameters = array();

	protected function build($slug, Request $request, Application $app) {
		global $CONFIG;

		$this->parameters = array(

		);

		if (strpos($slug, "-")) {
			$slug = array_shift(explode("-", $slug, 2));
		}

		$eventRepository = new EventRepository();
		$this->parameters['event'] =  $eventRepository->loadBySlug($app['currentSite'], $slug);
		if (!$this->parameters['event']) {
			return false;
		}

		return true;

	}

	function index($slug, Request $request, Application $app) {

		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Event does not exist.");
		}

		if ($request->request->get('eventCommentSlug') && $request->request->get('CSFRToken') == $app['websession']->getCSFRToken()) {
			$repo = new EventCommentRepository();
			$eventComment = $repo->getByEventAndSlug($this->parameters['event'], $request->request->get('eventCommentSlug'));
			if ($eventComment) {
				if ($request->request->get('action') == 'delete') {
					$repo->delete($eventComment, $app['currentUser']);
				} else if ($request->request->get('action') == 'undelete') {
					$repo->undelete($eventComment, $app['currentUser']);
				} else if ($request->request->get('action') == 'close') {
					$repo->closeByAdmin($eventComment, $app['currentUser']);
				} else if ($request->request->get('action') == 'unclose') {
					$repo->uncloseByAdmin($eventComment, $app['currentUser']);
				}
			}
		}

		$ecrb = new EventCommentRepositoryBuilder();
		$ecrb->setIncludeDeleted(true);
		$ecrb->setIncludeClosedByAdmin(true);
		$ecrb->setEvent($this->parameters['event']);
		$this->parameters['eventComments'] =  $ecrb->fetchAll();

		return $app['twig']->render('site/commentsadminevent/index.html.twig', $this->parameters);

	}
}
