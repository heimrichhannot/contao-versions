<?php

/**
 * Contao Open Source CMS.
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */
$arrDca = &$GLOBALS['TL_DCA']['tl_version'];

$arrFields = [
    'memberusername' => [
        'sql' => 'varchar(255) NULL',
    ],
    'memberid' => [
        'sql' => 'int(10) unsigned NULL',
    ],
    'formhybrid_backend_url' => [
        'sql' => 'varchar(255) NULL',
    ],
];

$arrDca['fields'] = array_merge($arrFields, $arrDca['fields']);
