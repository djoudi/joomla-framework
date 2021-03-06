<?php
/**
 * @package    Joomla\Framework\Test
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace animal;

/**
 * A lambda class to test the namespace loader.
 *
 * @package  Joomla\Framework\Test
 * @since    12.3
 */
class Dog
{
	/**
	 * Return hello class name for testing
	 *
	 * @return   string
	 *
	 * @since    12.3
	 */
	public function say()
	{
		return 'hello';
	}
}
