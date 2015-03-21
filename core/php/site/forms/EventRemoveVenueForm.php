<?php

namespace site\forms;

use BaseFormWithEditComment;
use Silex\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;


/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class EventRemoveVenueForm extends BaseFormWithEditComment {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		parent::buildForm($builder, $options);
		
		$builder->add("remove",
				"checkbox",
					array(
						'required'=>true,
						'label'=>'This event is not at this venue'
					)
			    );
		
	}
	
	public function getName() {
		return 'EventRemoveVenueForm';
	}
	
	public function getDefaultOptions(array $options) {
		return array(
		);
	}
	
}
