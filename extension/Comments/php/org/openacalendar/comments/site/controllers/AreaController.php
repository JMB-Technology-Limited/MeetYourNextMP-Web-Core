<?php

namespace org\openacalendar\comments\site\controllers;

use org\openacalendar\comments\models\AreaCommentModel;
use org\openacalendar\comments\repositories\AreaCommentRepository;
use org\openacalendar\comments\repositories\builders\AreaCommentRepositoryBuilder;
use org\openacalendar\comments\site\forms\NewCommentForm;
use repositories\AreaRepository;
use repositories\UserWatchesAreaRepository;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class AreaController extends \site\controllers\AreaController {

	function newComment($slug, Request $request, Application $app) {

		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Area does not exist.");
		}

		if ($this->parameters['area']->getIsDeleted()) {
			die("No"); // TODO
		}

		$comment = new AreaCommentModel();

		$form = $app['form.factory']->create(new NewCommentForm(), $comment);


		if ('POST' == $request->getMethod()) {
			$form->bind($request);

			if ($form->isValid()) {


				$area = $this->parameters['area'];

				if ($request->request->get("area")) {
					foreach($this->parameters['parentAreas'] as $parentArea) {
						if ($parentArea->getId() == $request->request->get("area")) {
							$area = $parentArea;
						}
					}
				}

				$repo = new AreaCommentRepository();
				$repo->create($comment, $area, $app['currentUser']);

				$userWatchesAreaRepo = new UserWatchesAreaRepository();
				$userWatchesAreaRepo->startUserWatchingAreaIfNotWatchedBefore($app['currentUser'], $area);

				return $app->redirect("/area/".$area->getSlugforURL().'/comment');

			}
		}

		$this->parameters['form'] = $form->createView();

		return $app['twig']->render('site/area/newComment.html.twig', $this->parameters);

	}

	function comments($slug, Request $request, Application $app) {

		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Area does not exist.");
		}

		$ecrb = new AreaCommentRepositoryBuilder();
		$ecrb->setIncludeDeleted(false);
		$ecrb->setIncludeClosedByAdmin(false);
		$ecrb->setArea($this->parameters['area'], false, true);
		$ecrb->setLimit(1000);
		$this->parameters['areaComments'] =  $ecrb->fetchAll();


		// TODO should it really start "action"?
		$app['currentUserActions']->set("org.openacalendar.comments","actionAreaNewComment",
			$app['currentUserPermissions']->hasPermission("org.openacalendar.comments","COMMENTS_CHANGE")
			&& !$this->parameters['area']->getIsDeleted());


		return $app['twig']->render('site/area/comments.html.twig', $this->parameters);

	}


}
