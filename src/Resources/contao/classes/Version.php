<?php

/**
 * Contao Open Source CMS.
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Versions;

use Contao\Controller;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\FrontendUser;
use Contao\Model;
use Contao\RequestToken;
use Contao\System;
use Contao\Versions;
use HeimrichHannot\UtilsBundle\Driver\DC_Table_Utils;
use HeimrichHannot\VersionsBundle\Version\VersionControl;
use Psr\Log\LogLevel;

/**
 * @deprecated Will be removed in next major version
 */
class Version
{
    /**
     * Set a Versions object by a given model.
     *
     * @return Versions|void
     */
    public static function setFromModel(Model $objModel): ?Versions
    {
        Controller::loadDataContainer($objModel->getTable());

        if (!$GLOBALS['TL_DCA'][$objModel->getTable()]['config']['enableVersioning']) {
            return null;
        }

        return new Versions($objModel->getTable(), $objModel->id);
    }

    /**
     * Create new version or initialize depending on existing version.
     */
    public static function createVersion(Versions $objVersion, Model $objModel): void
    {
        $additionalData = null;

        if (System::getContainer()->get('security.helper')->isGranted('ROLE_MEMBER') && ($objMember = FrontendUser::getInstance()) !== null) {
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
        if (is_array($GLOBALS['TL_DCA'][$objModel->getTable()]['config']['onversion_callback'])) {
            @trigger_error('Using the "onversion_callback" has been deprecated and will no longer work in Contao 5.0. Use the "oncreate_version_callback" instead.', E_USER_DEPRECATED);

            foreach ($GLOBALS['TL_DCA'][$objModel->getTable()]['config']['onversion_callback'] as $callback) {
                if (is_array($callback)) {
                    $objCallback = System::importStatic($callback[0]);
                    $objCallback->{$callback[1]}($objModel->getTable(), $objModel->id, $dc);
                } elseif (is_callable($callback)) {
                    $callback($objModel->getTable(), $objModel->id, $dc);
                }
            }
        }

        System::getContainer()->get('monolog.logger.contao')->log(LogLevel::INFO, 'A new version of record "' . $objModel->getTable() . '.id=' . $objModel->id . '" has been created', [
            'contao' => new ContaoContext(__METHOD__, ContaoContext::GENERAL),
        ]);
    }

    /**
     * Create a version by a given model.
     */
    public static function createFromModel(Model $objModel): void
    {
        static::createVersion(static::setFromModel($objModel), $objModel);
    }

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
