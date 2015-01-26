<?php

namespace org\openacalendar\comments\site\controllers;

use org\openacalendar\comments\models\AreaCommentModel;
use org\openacalendar\comments\repositories\builders\AreaCommentRepositoryBuilder;
use org\openacalendar\comments\repositories\AreaCommentRepository;
use org\openacalendar\comments\site\forms\NewCommentForm;
use repositories\AreaRepository;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class AdminAreaController extends \site\controllers\AreaController {


	protected $parameters = array();

	protected function build($slug, Request $request, Application $app) {
		global $CONFIG;

		$this->parameters = array(

		);

		if (strpos($slug, "-")) {
			$slug = array_shift(explode("-", $slug, 2));
		}

		$areaRepository = new AreaRepository();
		$this->parameters['area'] =  $areaRepository->loadBySlug($app['currentSite'], $slug);
		if (!$this->parameters['area']) {
			return false;
		}

		return true;

	}

	function index($slug, Request $request, Application $app) {

		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Area does not exist.");
		}

		if ($request->request->get('areaCommentSlug') && $request->request->get('CSFRToken') == $app['websession']->getCSFRToken()) {
			$repo = new AreaCommentRepository();
			$areaComment = $repo->getByAreaAndSlug($this->parameters['area'], $request->request->get('areaCommentSlug'));
			if ($areaComment) {
				if ($request->request->get('action') == 'delete') {
					$repo->delete($areaComment, $app['currentUser']);
				} else if ($request->request->get('action') == 'undelete') {
					$repo->undelete($areaComment, $app['currentUser']);
				} else if ($request->request->get('action') == 'close') {
					$repo->closeByAdmin($areaComment, $app['currentUser']);
				} else if ($request->request->get('action') == 'unclose') {
					$repo->uncloseByAdmin($areaComment, $app['currentUser']);
				}
			}
		}

		$ecrb = new AreaCommentRepositoryBuilder();
		$ecrb->setIncludeDeleted(true);
		$ecrb->setIncludeClosedByAdmin(true);
		$ecrb->setArea($this->parameters['area']);
		$this->parameters['areaComments'] =  $ecrb->fetchAll();

		return $app['twig']->render('site/commentsadminarea/index.html.twig', $this->parameters);

	}
}
