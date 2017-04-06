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


use HeimrichHannot\Haste\DC_Table;

class Version
{
	/**
	 * Set a Versions object by a given model
	 *
	 * @param \Contao\Model $objModel
	 *
	 * @return \Contao\Versions|void
	 */
	public static function setFromModel(\Contao\Model $objModel)
	{
		\Controller::loadDataContainer($objModel->getTable());

		if (!$GLOBALS['TL_DCA'][$objModel->getTable()]['config']['enableVersioning'])
		{
			return;
		}

		$objUser = VersionUser::getInstance();

		if ($objUser === null)
		{
			return;
		}

		$objVersion = new  \Contao\Versions($objModel->getTable(), $objModel->id);
		$objVersion->setUserId($objUser->id);
		$objVersion->setUsername($objUser->email);

		foreach ($GLOBALS['BE_MOD'] as $strGroup => $arrGroup)
		{
			foreach ($arrGroup as $strModule => $arrModule)
			{
				if (!isset($arrModule['tables']) || !is_array($arrModule['tables']))
				{
					continue;
				}

				if (in_array($objModel->getTable(), $arrModule['tables']))
				{
					$objVersion->setEditUrl(
						sprintf(
							'contao/main.php?do=%s&table=%s&act=edit&id=%s&rt=%s',
							$strModule,
							$objModel->getTable(),
							$objModel->id,
							\RequestToken::get()
						)
					);
					break 2;
				}
			}
		}

		if (FE_USER_LOGGED_IN && ($objMember = \FrontendUser::getInstance()) !== null)
		{
			$objVersion->memberusername = $objMember->username;
			$objVersion->memberid       = $objMember->id;
		}

		return $objVersion;
	}

	/**
	 * Create new version or initialize depending on existing version
	 *
	 * @param \Contao\Versions $objVersion
	 * @param \Contao\Model    $objModel
	 */
	public static function createVersion(\Contao\Versions $objVersion, \Contao\Model $objModel)
	{
		$objVersion->create();

		$dc = DC_Table::getInstanceFromModel($objModel);

		// Call the onversion_callback
		if (is_array($GLOBALS['TL_DCA'][$objModel->getTable()]['config']['onversion_callback']))
		{
			foreach ($GLOBALS['TL_DCA'][$objModel->getTable()]['config']['onversion_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$objCallback = \Controller::importStatic($callback[0]);
					$objCallback->{$callback[1]}($objModel->getTable(), $objModel->id, $dc);
				} elseif (is_callable($callback))
				{
					$callback($objModel->getTable(), $objModel->id, $dc);
				}
			}
		}

		\System::log(
			'A new version of record "' . $objModel->getTable() . '.id=' . $objModel->id . '" has been created',
			__METHOD__,
			TL_GENERAL
		);
	}

	/**
	 * Create a version by a given model
	 *
	 * @param \Contao\Model $objModel
	 */
	public static function createFromModel(\Contao\Model $objModel)
	{
		static::createVersion(static::setFromModel($objModel), $objModel);
	}
}