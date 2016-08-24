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
		if(!is_array($GLOBALS['PERSISTENT_VERSION_TABLES']) || empty($GLOBALS['PERSISTENT_VERSION_TABLES']))
		{
			parent::purgeVersionTable();
		}

		$objDatabase = \Database::getInstance();

		// Delete entries from the table that are not persistent
		$objDatabase->execute("DELETE FROM tl_version WHERE fromTable NOT IN('" . implode('\',\'', $GLOBALS['PERSISTENT_VERSION_TABLES']) . "')");

		// Add a log entry
		$this->log('Cleared non persistent entries from version table', __METHOD__, TL_CRON);
	}
}