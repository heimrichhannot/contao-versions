<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Versions;


class Automator extends \Automator
{
	/**
	 * Purge the version table
	 */
	public function purgeVersionTable()
	{
		$blnPurge = static::clearVersionTable(true);

		// Add a log entry
		if($blnPurge)
		{
			\System::log('Purged the version table', __METHOD__, TL_CRON);
			return;
		}

		\System::log('Cleared non persistent entries from version table', __METHOD__, TL_CRON);
	}


	/**
	 * Clean up tl_version table
	 * @param bool $blnPurge Set to true if you want to purge the table tl_versions ($GLOBALS['PERSISTENT_VERSION_TABLES'] entries will persist anyways)
	 *
	 * @return bool Return true, if $GLOBALS['PERSISTENT_VERSION_TABLES'] were skipped from cleaning
	 */
	public static function cleanVersionTable($blnPurge = false)
	{
		$objDatabase = \Database::getInstance();

		// Delete old versions from the database if no persistent
		if(!is_array($GLOBALS['PERSISTENT_VERSION_TABLES']) || empty($GLOBALS['PERSISTENT_VERSION_TABLES']))
		{
			if(!$blnPurge)
			{
				// Delete old versions from the database
				$tstamp = time() - intval(\Config::get('versionPeriod'));
				$objDatabase->query("DELETE FROM tl_version WHERE tstamp<$tstamp");
			}
			else
			{
				// Truncate the table
				$objDatabase->execute("TRUNCATE TABLE tl_version");
			}

			return true;
		}

		// Delete entries from the table that are not persistent
		$objDatabase->execute("DELETE FROM tl_version WHERE fromTable NOT IN('" . implode('\',\'', $GLOBALS['PERSISTENT_VERSION_TABLES']) . "')");

		return false;
	}
}