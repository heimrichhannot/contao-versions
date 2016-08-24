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


class VersionModel extends \Contao\Model
{
	protected static $strTable = 'tl_version';

	/**
	 * Find the current version of a given model
	 *
	 * @param \Model $objModel The parent entity model
	 * @param array  $arrOptions An optional options array
	 *
	 * @return \VersionModel|null The model or null if there is no previous version
	 */
	public static function findCurrentByModel(\Model $objModel, array $arrOptions = array())
	{
		$t = static::$strTable;

		$arrColumns = array("$t.fromTable = ? AND $t.pid = ? AND $t.active = 1");

		if (!$arrOptions['order'])
		{
			$arrOptions['order'] = "$t.version DESC";
		}

		return static::findOneBy($arrColumns, array($objModel->getTable(), $objModel->id), $arrOptions);
	}

	/**
	 * Find the previous version of a given model
	 *
	 * @param \Model $objModel The parent entity model
	 * @param array  $arrOptions An optional options array
	 *
	 * @return \VersionModel|null The model or null if there is no previous version
	 */
	public static function findPreviousByModel(\Model $objModel, array $arrOptions = array())
	{
		$t = static::$strTable;

		$arrColumns = array("$t.fromTable = ? AND $t.pid = ? AND $t.active != 1");

		if (!$arrOptions['order'])
		{
			$arrOptions['order'] = "$t.version DESC";
		}

		return static::findOneBy($arrColumns, array($objModel->getTable(), $objModel->id), $arrOptions);
	}

	/**
	 * Find all previous versions of a given model
	 *
	 * @param \Model $objModel The parent entity model
	 * @param array  $arrOptions An optional options array
	 *
	 * @return \Model\Collection|\VersionModel[]|\VersionModel|null A collection of models or null if there are no previous versions
	 */
	public static function findAllPreviousByModel(\Model $objModel, array $arrOptions = array())
	{
		$t = static::$strTable;

		$arrColumns = array("$t.fromTable = ? AND $t.pid = ? AND $t.active != 1");

		if (!$arrOptions['order'])
		{
			$arrOptions['order'] = "$t.version DESC";
		}

		return static::findBy($arrColumns, array($objModel->getTable(), $objModel->id), $arrOptions);
	}

	/**
	 * Find all versions of a given model
	 *
	 * @param \Model $objModel The parent entity model
	 * @param array  $arrOptions An optional options array
	 *
	 * @return \Model\Collection|\VersionModel[]|\VersionModel|null A collection of models or null if there are no versions
	 */
	public static function findAllByModel(\Model $objModel, array $arrOptions = array())
	{
		$t = static::$strTable;

		$arrColumns = array("$t.fromTable = ? AND $t.pid = ?");

		if (!$arrOptions['order'])
		{
			$arrOptions['order'] = "$t.version DESC";
		}

		return static::findBy($arrColumns, array($objModel->getTable(), $objModel->id), $arrOptions);
	}
}