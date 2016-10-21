<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


$arrDca = &$GLOBALS['TL_DCA']['tl_version'];

$arrFields = array(
    'memberusername'         => array(
        'label' => &$GLOBALS['TL_LANG']['tl_version']['memberusername'],
        'sql'   => "varchar(255) NULL"
    ),
    'memberid'               => array(
        'label' => &$GLOBALS['TL_LANG']['tl_version']['memberid'],
        'sql' => "int(10) unsigned NULL"
    ),
    'formhybrid_backend_url' => array(
        'label' => &$GLOBALS['TL_LANG']['tl_version']['formhybrid_backend_url'],
        'sql' => "varchar(255) NULL"
    )
);

$arrDca['fields'] = array_merge($arrFields, $arrDca['fields']);

tl_version_versions::modifyExistingFields($arrDca);

class tl_version_versions {
    public static function modifyExistingFields(&$arrDca)
    {
        foreach (array('version', 'fromTable', 'userid', 'username', 'description', 'editUrl', 'active', 'data') as $strField)
        {
            $arrDca['fields'][$strField]['label'] = &$GLOBALS['TL_LANG']['tl_version'][$strField];
        }
    }
}