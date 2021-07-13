<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

/**
 * Add persistent version table names
 */

use HeimrichHannot\VersionsBundle\DataContainer\VersionsContainer;
use HeimrichHannot\VersionsBundle\Model\VersionModel;

array_insert($GLOBALS['PERSISTENT_VERSION_TABLES'], 0, []);

/**
 * Purge jobs
 */
$GLOBALS['TL_PURGE']['tables']['versions'] = [
    'callback' => [VersionsContainer::class, 'purgeTable'],
    'affected' => ['tl_version']];

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_version'] = VersionModel::class;