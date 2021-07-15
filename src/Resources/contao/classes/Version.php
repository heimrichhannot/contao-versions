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


use Contao\Controller;
use Contao\FrontendUser;
use Contao\Model;
use Contao\RequestToken;
use Contao\System;
use Contao\Versions;
use HeimrichHannot\UtilsBundle\Driver\DC_Table_Utils;
use HeimrichHannot\VersionsBundle\Version\VersionControl;

/**
 * Class Version
 * @package HeimrichHannot\Versions
 *
 * @deprecated Will be removed in next major version
 */
class Version
{
	/**
	 * Set a Versions object by a given model
	 *
	 * @param Model $objModel
	 *
	 * @return Versions|void
	 */
	public static function setFromModel(Model $objModel): ?Versions
	{
		Controller::loadDataContainer($objModel->getTable());

		if (!$GLOBALS['TL_DCA'][$objModel->getTable()]['config']['enableVersioning'])
		{
			return null;
		}

		return new  Versions($objModel->getTable(), $objModel->id);
	}

	/**
	 * Create new version or initialize depending on existing version
	 *
	 * @param Versions $objVersion
	 * @param Model $objModel
	 */
	public static function createVersion(Versions $objVersion, Model $objModel)
	{
	    $additionalData = null;

        if (FE_USER_LOGGED_IN && ($objMember = FrontendUser::getInstance()) !== null)
        {
            $additionalData = [
                'memberid' => $objMember->id,
                'memberusername' => $objMember->username,
                'username' => VersionUser::VERSION_USER_EMAIL,
                'userid' => 0,
            ];
            $backendUrl = self::generateBackendEditUrlForFrontend($objModel, $objVersion);
            if (!empty($backendUrl)) {
                $additionalData['editUrl'] = $backendUrl;
            }
        }

	    System::getContainer()->get(VersionControl::class)->createVersion($objModel->getTable(), $objModel->id, [
	        'additionalData' => $additionalData,
            'instance' => $objVersion,
        ]);

		$dc = DC_Table_Utils::createFromModel($objModel);

		// Call the onversion_callback
		if (is_array($GLOBALS['TL_DCA'][$objModel->getTable()]['config']['onversion_callback']))
		{
            @trigger_error('Using the "onversion_callback" has been deprecated and will no longer work in Contao 5.0. Use the "oncreate_version_callback" instead.', E_USER_DEPRECATED);

			foreach ($GLOBALS['TL_DCA'][$objModel->getTable()]['config']['onversion_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$objCallback = System::importStatic($callback[0]);
					$objCallback->{$callback[1]}($objModel->getTable(), $objModel->id, $dc);
				} elseif (is_callable($callback))
				{
					$callback($objModel->getTable(), $objModel->id, $dc);
				}
			}
		}

		System::log(
			'A new version of record "' . $objModel->getTable() . '.id=' . $objModel->id . '" has been created',
			__METHOD__,
			TL_GENERAL
		);
	}

	/**
	 * Create a version by a given model
	 *
	 * @param Model $objModel
	 */
	public static function createFromModel(Model $objModel)
	{
		static::createVersion(static::setFromModel($objModel), $objModel);
	}

    /**
     * @param Model $objModel
     * @param Versions $objVersion
     */
    private static function generateBackendEditUrlForFrontend(Model $objModel, Versions $objVersion): string
    {
        foreach ($GLOBALS['BE_MOD'] as $strGroup => $arrGroup) {
            foreach ($arrGroup as $strModule => $arrModule) {
                if (!isset($arrModule['tables']) || !is_array($arrModule['tables'])) {
                    continue;
                }

                if (in_array($objModel->getTable(), $arrModule['tables'])) {
                    return sprintf(
                        'contao?do=%s&table=%s&act=edit&id=%s&rt=%s',
                        $strModule,
                        $objModel->getTable(),
                        $objModel->id,
                        RequestToken::get()
                    );
                }
            }
        }
        return '';
    }
}