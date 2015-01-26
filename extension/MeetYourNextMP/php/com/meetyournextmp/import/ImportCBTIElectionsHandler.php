<?php

namespace com\meetyournextmp\import;


use import\ImportedEventOccurrenceToEvent;
use import\ImportedEventToImportedEventOccurrences;
use import\ImportURLHandlerBase;
use JMBTechnologyLimited\ParseDateTimeRangeString\ParseDateTimeRangeString;
use models\ImportedEventModel;
use models\ImportURLResultModel;
use repositories\ImportedEventRepository;
use TimeSource;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class ImportCBTIElectionsHandler extends ImportURLHandlerBase {

	public function getSortOrder() {
		return 1000;
	}

	public function canHandle()
	{

		$urlBits = parse_url($this->importURLRun->getRealURL());

		if ($urlBits['host']== 'ctbielections.org.uk')  {
			return true;
		}

		return false;

	}

	protected $importedEventOccurrenceToEvent;

	public function handle()
	{

		$this->new = $this->existing = $this->saved = $this->inpast = $this->tofarinfuture = $this->notvalid = 0;

		$this->importedEventOccurrenceToEvent = new ImportedEventOccurrenceToEvent();
		$this->importedEventOccurrenceToEvent->setFromImportURlRun($this->importURLRun);

		$postdata = array(
			'action'=> 'searchHustings',
			'constitId'=> 0,
			'constitName'=> '',
		);


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://ctbielections.org.uk/wp-admin/admin-ajax.php");
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 5.0.1; Nexus 9 Build/LRX22C) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.93 Safari/537.36');
		$data = curl_exec($ch);
		$response = curl_getinfo( $ch );
		curl_close($ch);

		if ($response['http_code'] != 200) {
			$iurlr = new ImportURLResultModel();
			$iurlr->setIsSuccess(false);
			$iurlr->setMessage("Special CTBI feed found, but error code: ".$response['http_code']);
			return $iurlr;
		}

		$dataObj = json_decode($data);

		foreach($dataObj as $hustingJSON) {
			if ($hustingJSON->eventType == 'E') {
				$this->processEvent($hustingJSON);
			}
		}

		$iurlr = new ImportURLResultModel();
		$iurlr->setIsSuccess(true);
		$iurlr->setNewCount($this->new);
		$iurlr->setExistingCount($this->existing);
		$iurlr->setSavedCount($this->saved);
		$iurlr->setInPastCount($this->inpast);
		$iurlr->setToFarInFutureCount($this->tofarinfuture);
		$iurlr->setNotValidCount($this->notvalid);
		$iurlr->setMessage("Special CTBI feed found");
		return $iurlr;

	}

	protected function processEvent($event) {

		global $CONFIG;

		$importedEventRepo = new ImportedEventRepository();

		$importedEventChangesToSave = false;
		$importedEvent = $importedEventRepo->loadByImportURLIDAndImportId($this->importURLRun->getImportURL()->getId() ,$event->id);

		if (!$importedEvent) {

			$importedEvent = new ImportedEventModel();
			$importedEvent->setImportId($event->id);
			$importedEvent->setImportUrlId($this->importURLRun->getImportURL()->getId());
			$this->setOurEventFromEventObject($importedEvent, $event);
			$importedEventChangesToSave = true;

		} else {

			$importedEventChangesToSave = $this->setOurEventFromEventObject($importedEvent, $event);

		}

		$ietieo = new ImportedEventToImportedEventOccurrences($importedEvent);

		$this->importedEventOccurrenceToEvent->setEventRecurSet(null, false);

		foreach($ietieo->getImportedEventOccurrences() as $importedEventOccurrence) {

			if ($importedEventOccurrence->getEndAt()->getTimeStamp() < TimeSource::time()) {
				$this->inpast++;
			} else if ($importedEventOccurrence->getStartAt()->getTimeStamp() > TimeSource::time()+$CONFIG->importURLAllowEventsSecondsIntoFuture) {
				$this->tofarinfuture++;
			} else if ($this->saved < $this->limitToSaveOnEachRun) {

				// Imported Event
				if ($importedEventChangesToSave) {
					if ($importedEvent->getId()) {
						if ($importedEvent->getIsDeleted()) {
							$importedEventRepo->delete($importedEvent);
						} else {
							$importedEventRepo->edit($importedEvent);
						}
					} else {
						$importedEventRepo->create($importedEvent);
						// the ID will not be set until this point. So make sure we copy over the ID below just to be sure.
					}
					$importedEventChangesToSave = false;
				}

				// ... and the Imported Event Occurrence becomes a real event!
				$importedEventOccurrence->setId($importedEvent->getId());
				if ($this->importedEventOccurrenceToEvent->run($importedEventOccurrence)) {
					$this->saved++;
				}

			}

		}

	}

	protected function setOurEventFromEventObject(ImportedEventModel $importedEvent, $event) {
		$changesToSave = false;


		if ($importedEvent->getTitle() != $event->eventTitle) {
			$importedEvent->setTitle($event->eventTitle);
			$changesToSave = true;
		}

		$description = $event->eventDetails. "\n\n";
		$description.= $event->additionalInfo   ."\n\n";
		$description.= $event->practicalities   ."\n\n";
		$description.= $event->contactName  ."\n";
		$description.= $event->organisation  ."\n";
		$description.= $event->contactEmail  ."\n";
		$description.= $event->contactPhone  ."\n";
		$description.= $event->organisedBy  ."\n";
		$description.= $event->eventLocation  ."\n";
		$description.= $event->constitName ."\n";
		$description.= $event->eventPostcode  ."\n";
		$description.= $event->eventDate  ." ".$event->eventTime  ."\n";

		if ($importedEvent->getDescription() != $description) {
			$importedEvent->setDescription($description);
			$changesToSave = true;
		}

		if ($importedEvent->getUrl() != $event->url) {
			$importedEvent->setUrl($event->url);
			$changesToSave = true;
		}

		$importedEvent->setTimezone("Europe/London");

		$p = new ParseDateTimeRangeString(TimeSource::getDateTime(), "Europe/London");
		$r = $p->parse($event->eventDate  ." ".$event->eventTime);


		if (!$importedEvent->getStartAt() || $importedEvent->getStartAt()->getTimeStamp() != $r->getStart()->getTimeStamp()) {
			$importedEvent->setStartAt(clone $r->getStart());
			$changesToSave = true;
		}
		if (!$importedEvent->getEndAt() || $importedEvent->getEndAt()->getTimeStamp() != $r->getEnd()->getTimeStamp()) {
			$importedEvent->setEndAt(clone $r->getEnd());
			$changesToSave = true;
		}

		return $changesToSave;
	}

}
