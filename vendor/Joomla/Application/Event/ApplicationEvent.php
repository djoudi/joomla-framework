<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Event;

use Symfony\Component\EventDispatcher\Event;
use Joomla\Application\Base as BaseApplication;

/**
 * Base Event for the Application
 *
 * @since  1.0
 */
class ApplicationEvent extends Event
{
	private $application;
	
	public function __construct(BaseApplication $application)
	{
		$this->application = $application;
	}
	
	public function getApplication()
	{
		return $this->application;
	}
}
