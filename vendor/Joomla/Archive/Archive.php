<?php
/**
 * Part of the Joomla Framework Archive Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive;

use Joomla\Factory;
use Joomla\Filesystem\Path;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

/**
 * An Archive handling class
 *
 * @since  1.0
 */
class Archive
{
	/**
	 * The array of instantiated archive adapters.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected static $adapters = array();

	/**
	 * Extract an archive file to a directory.
	 *
	 * @param   string  $archivename  The name of the archive file
	 * @param   string  $extractdir   Directory to unpack into
	 *
	 * @return  boolean  True for success
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	public static function extract($archivename, $extractdir)
	{
		$untar = false;
		$result = false;
		$ext = File::getExt(strtolower($archivename));

		// Check if a tar is embedded...gzip/bzip2 can just be plain files!
		if (File::getExt(File::stripExt(strtolower($archivename))) == 'tar')
		{
			$untar = true;
		}

		switch ($ext)
		{
			case 'zip':
				$adapter = self::getAdapter('zip');

				if ($adapter)
				{
					$result = $adapter->extract($archivename, $extractdir);
				}
				break;

			case 'tar':
				$adapter = self::getAdapter('tar');

				if ($adapter)
				{
					$result = $adapter->extract($archivename, $extractdir);
				}
				break;

			case 'tgz':
				// This format is a tarball gzip'd
				$untar = true;

			case 'gz':
			case 'gzip':
				// This may just be an individual file (e.g. sql script)
				$adapter = self::getAdapter('gzip');

				if ($adapter)
				{
					$config = Factory::getConfig();
					$tmpfname = $config->get('tmp_path') . '/' . uniqid('gzip');
					$gzresult = $adapter->extract($archivename, $tmpfname);

					if ($gzresult instanceof \Exception)
					{
						@unlink($tmpfname);

						return false;
					}

					if ($untar)
					{
						// Try to untar the file
						$tadapter = self::getAdapter('tar');

						if ($tadapter)
						{
							$result = $tadapter->extract($tmpfname, $extractdir);
						}
					}
					else
					{
						$path = Path::clean($extractdir);
						Folder::create($path);
						$result = File::copy($tmpfname, $path . '/' . File::stripExt(basename(strtolower($archivename))), null, 1);
					}

					@unlink($tmpfname);
				}
				break;

			case 'tbz2':
				// This format is a tarball bzip2'd
				$untar = true;

			case 'bz2':
			case 'bzip2':
				// This may just be an individual file (e.g. sql script)
				$adapter = self::getAdapter('bzip2');

				if ($adapter)
				{
					$config = Factory::getConfig();
					$tmpfname = $config->get('tmp_path') . '/' . uniqid('bzip2');
					$bzresult = $adapter->extract($archivename, $tmpfname);

					if ($bzresult instanceof \Exception)
					{
						@unlink($tmpfname);

						return false;
					}

					if ($untar)
					{
						// Try to untar the file
						$tadapter = self::getAdapter('tar');

						if ($tadapter)
						{
							$result = $tadapter->extract($tmpfname, $extractdir);
						}
					}
					else
					{
						$path = Path::clean($extractdir);
						Folder::create($path);
						$result = File::copy($tmpfname, $path . '/' . File::stripExt(basename(strtolower($archivename))), null, 1);
					}

					@unlink($tmpfname);
				}
				break;

			default:
				throw new \InvalidArgumentException('Unknown Archive Type');
		}

		if (!$result || $result instanceof \Exception)
		{
			return false;
		}

		return true;
	}

	/**
	 * Get a file compression adapter.
	 *
	 * @param   string  $type  The type of adapter (bzip2|gzip|tar|zip).
	 *
	 * @return  Joomla\Archive\ExtractableInterface  Adapter for the requested type
	 *
	 * @since   1.0
	 * @throws  \UnexpectedValueException
	 */
	public static function getAdapter($type)
	{
		if (!isset(self::$adapters[$type]))
		{
			// Try to load the adapter object
			$class = 'Joomla\\Archive\\' . ucfirst($type);

			if (!class_exists($class))
			{
				throw new \UnexpectedValueException(sprintf('Unable to load archive adapter %s.', $type) , 500);
			}

			self::$adapters[$type] = new $class;
		}

		return self::$adapters[$type];
	}
}
