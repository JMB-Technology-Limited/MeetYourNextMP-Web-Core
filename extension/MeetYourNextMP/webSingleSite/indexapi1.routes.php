<?php
/**
 *
 * @package com.meetyournextmp
 * @license Closed Source
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */




$app->match('/api1/mapitid/{slug}/events.ical', "com\meetyournextmp\siteapi1\controllers\MapItIDController::ical")
		->assert('slug', FRIENDLY_SLUG_REGEX);
$app->match('/api1/mapitid/{slug}/events.json', "com\meetyournextmp\siteapi1\controllers\MapItIDController::json")
		->assert('slug', FRIENDLY_SLUG_REGEX);
$app->match('/api1/mapitid/{slug}/events.jsonp', "com\meetyournextmp\siteapi1\controllers\MapItIDController::jsonp")
		->assert('slug', FRIENDLY_SLUG_REGEX);
$app->match('/api1/mapitid/{slug}/events.csv', "com\meetyournextmp\siteapi1\controllers\MapItIDController::csv")
		->assert('slug', FRIENDLY_SLUG_REGEX);
$app->match('/api1/mapitid/{slug}/events.create.atom', "com\meetyournextmp\siteapi1\controllers\MapItIDController::atomCreate")
		->assert('slug', FRIENDLY_SLUG_REGEX);
$app->match('/api1/mapitid/{slug}/events.before.atom', "com\meetyournextmp\siteapi1\controllers\MapItIDController::atomBefore")
		->assert('slug', FRIENDLY_SLUG_REGEX);


$app->match('/api1/areamapitid/{slug}/events.ical', "com\meetyournextmp\siteapi1\controllers\MapItIDController::ical")
		->assert('slug', FRIENDLY_SLUG_REGEX);
$app->match('/api1/areamapitid/{slug}/events.json', "com\meetyournextmp\siteapi1\controllers\MapItIDController::json")
		->assert('slug', FRIENDLY_SLUG_REGEX);
$app->match('/api1/areamapitid/{slug}/events.jsonp', "com\meetyournextmp\siteapi1\controllers\MapItIDController::jsonp")
		->assert('slug', FRIENDLY_SLUG_REGEX);
$app->match('/api1/areamapitid/{slug}/events.csv', "com\meetyournextmp\siteapi1\controllers\MapItIDController::csv")
		->assert('slug', FRIENDLY_SLUG_REGEX);
$app->match('/api1/areamapitid/{slug}/events.create.atom', "com\meetyournextmp\siteapi1\controllers\MapItIDController::atomCreate")
		->assert('slug', FRIENDLY_SLUG_REGEX);
$app->match('/api1/areamapitid/{slug}/events.before.atom', "com\meetyournextmp\siteapi1\controllers\MapItIDController::atomBefore")
		->assert('slug', FRIENDLY_SLUG_REGEX);
