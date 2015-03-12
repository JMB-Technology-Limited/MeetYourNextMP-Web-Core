<?php


/**
 *
 * @package com.meetyournextmp
 * @license Closed Source
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

$app->match('/postcode', "com\meetyournextmp\site\controllers\MapItAreaController::postcode") ;
$app->match('/postcode/', "com\meetyournextmp\site\controllers\MapItAreaController::postcode") ;

$app->match('/linktoseat.html', "com\meetyournextmp\site\controllers\MapItAreaController::linkToSeatHTML") ;
$app->match('/linktocandidate.html', "com\meetyournextmp\site\controllers\PopItCandidateController::linkToCandidateHTML") ;


$app->match('/seat', "com\meetyournextmp\site\controllers\SeatListController::index") ;
$app->match('/seat/', "com\meetyournextmp\site\controllers\SeatListController::index") ;



$app->match('/human', "com\meetyournextmp\site\controllers\HumanListController::index");
$app->match('/human/', "com\meetyournextmp\site\controllers\HumanListController::index");

$app->match('/human/{slug}', "com\meetyournextmp\site\controllers\HumanController::show");
$app->match('/human/{slug}/', "com\meetyournextmp\site\controllers\HumanController::show");


$app->match('/event/{slug}/edit/humans', "com\meetyournextmp\site\controllers\EventController::editHumans")
		->assert('slug', FRIENDLY_SLUG_REGEX)
		->before($permissionEventsChangeRequired)
		->before($canChangeSite);

$app->match('/area/{slug}/humans', "com\meetyournextmp\site\controllers\AreaController::humans")
		->assert('slug', FRIENDLY_SLUG_REGEX) ;

$app->match('/formedia/',"com\meetyournextmp\site\controllers\IndexController::formedia");

$app->match('/numbers/',"com\meetyournextmp\site\controllers\IndexController::numbers");

$app->match('/help',"com\meetyournextmp\site\controllers\HelpController::index");
$app->match('/help/',"com\meetyournextmp\site\controllers\HelpController::index");

$app->match('/help/who',"com\meetyournextmp\site\controllers\HelpController::indexWho");

$app->match('/help/noeventsseat',"com\meetyournextmp\site\controllers\HelpController::indexNoEventsSeat");

$app->match('/area/{slug}/tweetToCandidates', "com\meetyournextmp\site\controllers\AreaController::tweetHumans")
		->assert('slug', FRIENDLY_SLUG_REGEX);;
