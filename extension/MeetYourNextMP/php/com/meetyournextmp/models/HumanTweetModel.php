<?php

namespace com\meetyournextmp\models;
use com\meetyournextmp\repositories\builders\EventRepositoryBuilder;
use models\AreaModel;
use Silex\Application;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class HumanTweetModel
{

	protected $id;
	protected $human_id;
	protected $text;
	protected $created_at;
	protected $sent_at;
	protected $twitter_id;

	/**
	 * @return mixed
	 */
	public function getCreatedAt()
	{
		return $this->created_at;
	}

	/**
	 * @param mixed $created_at
	 */
	public function setCreatedAt($created_at)
	{
		$this->created_at = $created_at;
	}

	/**
	 * @return mixed
	 */
	public function getHumanId()
	{
		return $this->human_id;
	}

	/**
	 * @param mixed $human_id
	 */
	public function setHumanId($human_id)
	{
		$this->human_id = $human_id;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getSentAt()
	{
		return $this->sent_at;
	}

	/**
	 * @param mixed $sent_at
	 */
	public function setSentAt($sent_at)
	{
		$this->sent_at = $sent_at;
	}

	/**
	 * @return mixed
	 */
	public function getText()
	{
		return $this->text;
	}


	/**
	 * @return mixed
	 */
	public function getTextLength()
	{
		// http://stackoverflow.com/questions/8864767/calculate-final-tweet-character-length-in-php
		return mb_strlen(preg_replace('~https?://([^\s]*)~', 'http://890123456789012', $this->text), 'UTF-8');
	}



	/**
	 * @param mixed $text
	 */
	public function setText($text)
	{
		$this->text = $text;
	}

	/**
	 * @return mixed
	 */
	public function getTwitterId()
	{
		return $this->twitter_id;
	}

	/**
	 * @param mixed $twitter_id
	 */
	public function setTwitterId($twitter_id)
	{
		$this->twitter_id = $twitter_id;
	}



}


