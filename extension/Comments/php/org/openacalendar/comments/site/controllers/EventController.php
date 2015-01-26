<?php

namespace org\openacalendar\comments\site\controllers;

use org\openacalendar\comments\models\EventCommentModel;
use org\openacalendar\comments\repositories\EventCommentRepository;
use org\openacalendar\comments\site\forms\NewCommentForm;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class EventController extends \site\controllers\EventController {

	function newComment($slug, Request $request, Application $app) {

		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Event does not exist.");
		}

		if ($this->parameters['event']->getIsDeleted()) {
			die("No"); // TODO
		}

		$comment = new EventCommentModel();

		$form = $app['form.factory']->create(new NewCommentForm(), $comment);


		if ('POST' == $request->getMethod()) {
			$form->bind($request);

			if ($form->isValid()) {


				$repo = new EventCommentRepository();
				$repo->create($comment, $this->parameters['event'], $app['currentUser']);

				return $app->redirect("/event/".$this->parameters['event']->getSlugforURL());

			}
		}

		$this->parameters['form'] = $form->createView();

		return $app['twig']->render('site/event/newComment.html.twig', $this->parameters);

	}
}
