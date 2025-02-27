<?php

/**
 * Contao Open Source CMS.
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\VersionsBundle\Model;

use Contao\Model;
use Contao\Model\Collection;

class VersionModel extends Model
{
    protected static $strTable = 'tl_version';

    /**
     * Find the current version of a given model.
     *
     * @param int    $intInstance      The parent model instance's id
     * @param string $strInstanceTable The parent model instance's table
     * @param array  $arrOptions       An optional options array
     *
     * @return VersionModel|null The model or null if there is no previous version
     */
    public static function findCurrent($intInstance, $strInstanceTable, array $arrOptions = []): ?self
    {
        $t = static::$strTable;

        $arrColumns = ["$t.fromTable = ? AND $t.pid = ? AND $t.active = 1"];

        if (!($arrOptions['order'] ?? null)) {
            $arrOptions['order'] = "$t.version DESC";
        }

        return static::findOneBy($arrColumns, [$strInstanceTable, $intInstance], $arrOptions);
    }

    /**
     * Find the previous version of a given model instance.
     *
     * @param int    $intInstance      The parent model instance's id
     * @param string $strInstanceTable The parent model instance's table
     * @param array  $arrOptions       An optional options array
     *
     * @return VersionModel|null The model or null if there is no previous version
     */
    public static function findPrevious($intInstance, $strInstanceTable, array $arrOptions = []): ?self
    {
        $t = static::$strTable;

        $arrColumns = ["$t.fromTable = ? AND $t.pid = ? AND $t.active != 1"];

        if (!$arrOptions['order']) {
            $arrOptions['order'] = "$t.version DESC";
        }

        return static::findOneBy($arrColumns, [$strInstanceTable, $intInstance], $arrOptions);
    }

    /**
     * Find the current version of a given model.
     *
     * @param Model $objModel   The parent entity model
     * @param array $arrOptions An optional options array
     *
     * @return VersionModel|null The model or null if there is no previous version
     */
    public static function findCurrentByModel(Model $objModel, array $arrOptions = []): ?self
    {
        $t = static::$strTable;

        $arrColumns = ["$t.fromTable = ? AND $t.pid = ? AND $t.active = 1"];

        if (!($arrOptions['order'] ?? null)) {
            $arrOptions['order'] = "$t.version DESC";
        }

        return static::findOneBy($arrColumns, [$objModel->getTable(), $objModel->id], $arrOptions);
    }

    /**
     * Find the previous version of a given model.
     *
     * @param Model $objModel   The parent entity model
     * @param array $arrOptions An optional options array
     *
     * @return VersionModel|null The model or null if there is no previous version
     */
    public static function findPreviousByModel(Model $objModel, array $arrOptions = []): ?self
    {
        $t = static::$strTable;

        $arrColumns = ["$t.fromTable = ? AND $t.pid = ? AND $t.active != 1"];

        if (!$arrOptions['order']) {
            $arrOptions['order'] = "$t.version DESC";
        }

        return static::findOneBy($arrColumns, [$objModel->getTable(), $objModel->id], $arrOptions);
    }

    /**
     * Find all previous versions of a given model.
     *
     * @param Model $objModel   The parent entity model
     * @param array $arrOptions An optional options array
     *
     * @return Collection|VersionModel[]|VersionModel|null A collection of models or null if there are no previous versions
     */
    public static function findAllPreviousByModel(Model $objModel, array $arrOptions = [])
    {
        $t = static::$strTable;

        $arrColumns = ["$t.fromTable = ? AND $t.pid = ? AND $t.active != 1"];

        if (!$arrOptions['order']) {
            $arrOptions['order'] = "$t.version DESC";
        }

        return static::findBy($arrColumns, [$objModel->getTable(), $objModel->id], $arrOptions);
    }

    /**
     * Find all versions of a given model.
     *
     * @param Model $objModel   The parent entity model
     * @param array $arrOptions An optional options array
     *
     * @return Collection|VersionModel[]|VersionModel|null A collection of models or null if there are no versions
     */
    public static function findAllByModel(Model $objModel, array $arrOptions = [])
    {
        $t = static::$strTable;

        $arrColumns = ["$t.fromTable = ? AND $t.pid = ?"];

        if (!$arrOptions['order']) {
            $arrOptions['order'] = "$t.version DESC";
        }

        return static::findBy($arrColumns, [$objModel->getTable(), $objModel->id], $arrOptions);
    }

    /**
     * Find the previous version of a given model and check data against values.
     *
     * @param array $arrValues A $key => $value array, the key is the attribute and the value the condition (also possible regular expressions)
     *                         within version data array Example array('title' => 'FOO')
     *
     * @return VersionModel|null The model or null if there is no previous version
     */
    public static function findPreviousByModelAndDataValues(Model $objModel, array $arrValues = [], array $arrOptions = [])
    {
        $t = static::$strTable;

        $arrColumns = ["$t.fromTable = ? AND $t.pid = ? AND $t.active != 1"];

        foreach ($arrValues as $key => $value) {
            $arrColumns[] = "$t.data REGEXP('\"$key\";s:([1-9]+):\"$value\"')";
        }

        if (!$arrOptions['order']) {
            $arrOptions['order'] = "$t.version DESC";
        }

        return static::findOneBy($arrColumns, [$objModel->getTable(), $objModel->id], $arrOptions);
    }

    /**
     * Find all previous versions by given model and check data against values.
     *
     * @param array $arrValues A $key => $value array, the key is the attribute and the value the condition (also possible regular expressions)
     *                         within version data array Example array('title' => 'FOO')
     *
     * @return Collection|VersionModel[]|VersionModel|null A collection of models or null if there are no versions
     */
    public static function findAllPreviousByModelAndDataValues(Model $objModel, array $arrValues = [], array $arrOptions = [])
    {
        $t = static::$strTable;

        $arrColumns = ["$t.fromTable = ? AND $t.pid = ? AND $t.active != 1"];

        foreach ($arrValues as $key => $value) {
            $arrColumns[] = "$t.data REGEXP('\"$key\";s:([1-9]+):\"$value\"')";
        }

        if (!$arrOptions['order']) {
            $arrOptions['order'] = "$t.version DESC";
        }

        return static::findBy($arrColumns, [$objModel->getTable(), $objModel->id], $arrOptions);
    }

    /**
     * Find all versions by given model and check data against values.
     *
     * @param array $arrValues A $key => $value array, the key is the attribute and the value the condition (also possible regular expressions)
     *                         within version data array Example array('title' => 'FOO')
     *
     * @return Collection|VersionModel[]|VersionModel|null A collection of models or null if there are no versions
     */
    public static function findAllByModelAndDataValues(Model $objModel, array $arrValues = [], array $arrOptions = [])
    {
        $t = static::$strTable;

        $arrColumns = ["$t.fromTable = ? AND $t.pid = ?"];

        foreach ($arrValues as $key => $value) {
            $arrColumns[] = "$t.data REGEXP('\"$key\";s:([1-9]+):\"$value\"')";
        }

        if (!$arrOptions['order']) {
            $arrOptions['order'] = "$t.version DESC";
        }

        return static::findBy($arrColumns, [$objModel->getTable(), $objModel->id], $arrOptions);
    }
}
