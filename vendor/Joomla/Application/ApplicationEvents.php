<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application;

/**
 * Description of events dispatcher by the application
 *
 * @sicne  1.0
 */
final class ApplicationEvents
{
	const BEFORE_EXECUTE = 'application.before.execute';
	const AFTER_EXECUTE = 'application.after.execute';
	const BEFORE_RESPOND = 'application.before.respond';
	const AFTER_RESPOND = 'application.after.respond';
}
