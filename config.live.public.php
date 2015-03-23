<?php


$CONFIG->isDebug = false;

$CONFIG->databaseType= 'pgsql';
$CONFIG->databaseName = 'meetyournextmp';
$CONFIG->databaseHost = 'localhost';
$CONFIG->databaseUser = 'meetyournextmp';



$CONFIG->assetsVersion = 17;

$CONFIG->isSingleSiteMode = true;
$CONFIG->singleSiteID = 1;

$CONFIG->webIndexDomain = "meetyournextmp.com";
$CONFIG->webSiteDomain = "meetyournextmp.com";
$CONFIG->webSysAdminDomain = "meetyournextmp.com";

$CONFIG->hasSSL = true;
$CONFIG->webIndexDomainSSL = "meetyournextmp.com";
$CONFIG->webSiteDomainSSL = "meetyournextmp.com";
$CONFIG->webSysAdminDomainSSL = "meetyournextmp.com";

$CONFIG->webCommonSessionDomain = "meetyournextmp.com";

$CONFIG->bcryptRounds = 5;


$CONFIG->siteTitle = "Meet Your Next MP";

$CONFIG->emailFrom = "meetyournextmp@democracyclub.org.uk";
$CONFIG->emailFromName = "Meet Your Next MP";

$CONFIG->copyrightNoticeText = "Copyright JMB Technology ltd 2013-2015";
$CONFIG->copyrightNoticeHTML = '&copy; <a href="http://jmbtechnology.co.uk/" target="_blank">JMB Technology ltd</a> 2013-2015';

$CONFIG->cacheFeedsInSeconds = 3600;

$CONFIG->cacheSiteLogoInSeconds = 604800; // 1 week

$CONFIG->siteReadOnly = false;
$CONFIG->siteReadOnlyReason = null;

$CONFIG->contactTwitter = "MeetYourNextMP";
$CONFIG->contactEmail = "meetyournextmp@democracyclub.org.uk";
$CONFIG->contactEmailHTML = "meetyournextmp at democracyclub dot org dot uk";
//$CONFIG->facebookPage="https://www.facebook.com/OpenTechCalendar";
//$CONFIG->googlePlusPage="https://plus.google.com/103293012309251213262";
//$CONFIG->ourBlog= "http://blog.opentechcalendar.co.uk/";

/** "12hr" or "24hr" **/
$CONFIG->clockDisplayDefault = "12hr";

$CONFIG->eventsCantBeMoreThanYearsInFuture = 1; 
$CONFIG->eventsCantBeMoreThanYearsInPast = 1; 	
$CONFIG->calendarEarliestYearAllowed = 2015;

$CONFIG->sysAdminLogInTimeOutSeconds = 900;  // 15 mins


$CONFIG->newUsersAreEditors = true;
$CONFIG->allowNewUsersToRegister = true;


$CONFIG->userAccountVerificationSecondsBetweenAllowedSends = 900;  // 15 mins


$CONFIG->googleAnalyticsTracking = "UA-59188723-1";
//$CONFIG->piwikServerHTTP = 'piwik.jmbtechnology.co.uk';
//$CONFIG->piwikServerHTTPS = 'piwik.jmbtechnology.co.uk';
//$CONFIG->piwikSiteID = 7;

//$CONFIG->fileStoreLocation= '/fileStore';
//$CONFIG->tmpFileCacheLocation = '/tmp/OpenTechCalendar3Cache/';
//$CONFIG->tmpFileCacheCreationPermissions = 0733;

$CONFIG->logFile = '/var/log/meetyournextmp/meetyournextmp.log';
$CONFIG->logFileMYNMPPostCodeSearch = '/var/log/meetyournextmp/meetyournextmpPostcodeSearch.log';
$CONFIG->logFileParseDateTimeRange = '/var/log/meetyournextmp/meetyournextmpParseDateTimeRange.log';

$CONFIG->sysAdminTimeZone = "Europe/London";

$CONFIG->widgetsJSPrefix = "ICanHasACalendarWidget";
$CONFIG->widgetsCSSPrefix = "ICanHasACalendarWidget";

$CONFIG->sessionLastsInSeconds = 14400; // 4 hours, 4 * 60 * 60

$CONFIG->resetEmailsGapBetweenInSeconds = 600; // 10 mins

$CONFIG->userWatchesGroupPromptEmailShowEvents = 3;
$CONFIG->userWatchesSitePromptEmailShowEvents = 3;
$CONFIG->userWatchesSiteGroupPromptEmailShowEvents = 3;
$CONFIG->userWatchesPromptEmailSafeGapDays = 30;

$CONFIG->newUserRegisterAntiSpam = true;
$CONFIG->contactFormAntiSpam = true;

$CONFIG->importURLExpireSecondsAfterLastEdit = 7776000; // 90 days
$CONFIG->importURLSecondsBetweenImports = 43200; // 12 hours
$CONFIG->importURLAllowEventsSecondsIntoFuture = 7776000; // 90 days

$CONFIG->upcomingEventsForUserEmailTextListsEvents = 20;
$CONFIG->upcomingEventsForUserEmailHTMLListsEvents = 5;

$CONFIG->siteSeenCookieStoreForDays = 30;

$CONFIG->mediaNormalSize = 500;
$CONFIG->mediaThumbnailSize = 100;
$CONFIG->mediaQualityJpeg = 90;
$CONFIG->mediaQualityPng = 2;
$CONFIG->mediaBrowserCacheExpiresInseconds = 7776000; // 90 days




$CONFIG->userNameReserved = array('james','jamesbaster','admin','doubtlesshouse','james.baster');


$CONFIG->memcachedServer = '127.0.0.1';
$CONFIG->memcachedPort = '11211';

$CONFIG->formWidgetTimeMinutesMultiples = 5;



$CONFIG->findDuplicateEventsNoMatchSummary = array('husting','hustings','the','of','a','church','st','st.','saint','school');


$CONFIG->SMTPHost="email-smtp.eu-west-1.amazonaws.com";
$CONFIG->SMTPEncyption="tls";

