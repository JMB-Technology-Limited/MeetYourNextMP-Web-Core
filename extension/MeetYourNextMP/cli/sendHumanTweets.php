<?php
use Abraham\TwitterOAuth\TwitterOAuth;

define('APP_ROOT_DIR',__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
require_once (defined('COMPOSER_ROOT_DIR') ? COMPOSER_ROOT_DIR : APP_ROOT_DIR).'/vendor/autoload.php';
require_once APP_ROOT_DIR.'/core/php/autoload.php';
require_once APP_ROOT_DIR.'/core/php/autoloadCLI.php';


sleep(5);

/**
 *
 * @package com.meetyournextmp
 * @license Closed Source
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

define('EVENTS_NEEDED_IN_AREA',2);

$tweetTexts = array(
	'We list independent #ge2015 events your voters can attend at [URL] - pls RT!',
	'Independent #ge2015 events for [AREA] are listed at [URL] - pls RT!',
);

print date("c");


// really??????????
$actuallySEND = isset($argv[1]) && strtolower($argv[1]) == 'yes';
print "Actually SEND: ". ($actuallySEND ? "YES":"nah")."\n";
if (!$actuallySEND) die("DIE\n");


// setup
$siteRepo = new \repositories\SiteRepository();
$site = $siteRepo->loadById($CONFIG->singleSiteID);
configureAppForSite($site);


$innerSQL = "SELECT human_in_area.area_id, COUNT(human_tweet.id) As tweets FROM human_in_area ".
	" LEFT JOIN human_tweet ON human_tweet.human_id = human_in_area.human_id AND human_in_area.removed_at IS NULL  ".
	"GROUP BY human_in_area.area_id";

// Get area
$stat = $DB->prepare("SELECT area_information.* FROM area_information ".
	" JOIN area_mapit_information ON area_mapit_information.area_id = area_information.id ".
	" JOIN (".$innerSQL.") AS area_tweets ON area_tweets.area_id = area_information.id ".
	" WHERE area_tweets.tweets = 0 AND  area_information.is_deleted = '0' AND area_information.cached_future_events >  ".EVENTS_NEEDED_IN_AREA.
	" ORDER BY area_information.cached_future_events DESC LIMIT 1 ");
$stat->execute();
if ($stat->rowCount() == 0) {
	die("No AREA Data");
}

$area = new \models\AreaModel();
$area->setFromDataBaseRow($stat->fetch());

$areaURL = "http://".$CONFIG->webSiteDomain."/area/".$area->getSlugForUrl();

print "Found Area ".$area->getTitle()."\n";

$erb = new \com\meetyournextmp\repositories\builders\EventRepositoryBuilder();
$erb->setAfterNow();
$erb->setArea($area);
$erb->setIncludeCancelled(false);
$erb->setIncludeDeleted(false);
$events = $erb->fetchCount();

if ($events < EVENTS_NEEDED_IN_AREA) {
	die("We checked and found less than ".EVENTS_NEEDED_IN_AREA." events; is cache broken!");
}

$hrb = new \com\meetyournextmp\repositories\builders\HumanRepositoryBuilder();
$hrb->setIncludeDeleted(false);
$hrb->setArea($area);
$humans = $hrb->fetchAll();

$humanTweets = array();
$humanTweetRepo = new \com\meetyournextmp\repositories\HumanTweetRepository();

foreach($humans as $human) {
	if ($human->getTwitter()) {
		$humanTweet = new \com\meetyournextmp\models\HumanTweetModel();
		$humanTweet->setHumanId($human->getId());
		$txt = $tweetTexts[rand(0, count($tweetTexts)-1)];
		$txt = str_replace("[AREA]",$area->getTitle(),$txt);
		$txt = str_replace("[URL]",$areaURL,$txt);
		$humanTweet->setText("@".$human->getTwitter()." ". $txt);
		print $humanTweet->getText()." = ".$humanTweet->getTextLength()."\n";
		$humanTweetRepo->create($humanTweet);
		$humanTweets[] = $humanTweet;
	}
}

sleep(5);

$connection = new TwitterOAuth($CONFIG->TWITTER_APP_APP_KEY, $CONFIG->TWITTER_APP_APP_SECRET, $CONFIG->TWITTER_APP_USER_TOKEN, $CONFIG->TWITTER_APP_USER_SECRET);

foreach($humanTweets as $humanTweet) {

	$data = $connection->post("statuses/update", array("status" => $humanTweet->getText()));

	$humanTweet->setTwitterId($data->id_str);

	$humanTweetRepo->markSent($humanTweet);

}

