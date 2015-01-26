<?php

namespace com\meetyournextmp;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class PostcodeParser  {

	protected $postcode;

	function __construct($postcode)
	{
		$this->postcode = $postcode;
	}


	function isValid() {
		$len = strlen(trim($this->postcode));

		return $len > 3 && $len < 20;
	}

	function getCanonical() {
		return strtoupper(str_replace(" ","",$this->postcode));
	}

}
