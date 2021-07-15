<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\VersionsBundle\Version;


use Contao\Versions;
use Doctrine\DBAL\Connection;

class VersionControl
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * VersionControl constructor.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Create a new version
     *
     * Options:
     * - hideUser: (bool) Don't add user to version. Default false
     * - additionalData: (array|null) Data that should be added to the versions entry. Provide as ['databaseFieldName' => 'value']. Default null
     * - instance: (Versions|null) Pass a custom versions instance. Default null
     *
     * @param string $table
     * @param int $id
     * @param array $options
     */
    public function createVersion(string $table, int $id, array $options = []): void
    {
        $defaults = [
            'hideUser' => false,
            'additionalData' => null,
            'instance' => null,
        ];
        $options = array_merge($defaults, $options);

        if (!empty($options['additionalData'])) {
            $connection = $this->connection;
            $additionalData = $options['additionalData'];
            $GLOBALS['TL_DCA'][$table]['config']['oncreate_version_callback']['huh_versions_additional_data_'.$table.'_'.$id] =
                function (string $table, int $pid, int $version, array $data) use ($connection, $additionalData)
                {
                    $stmt = $connection->prepare("SELECT id FROM tl_version WHERE fromTable=? AND pid=? AND version=?");
                    $stmt->execute([$table, $pid, $version]);
                    $id = $stmt->fetchColumn(0);
                    $stmt = $connection->prepare("UPDATE tl_version SET ".implode("=?, ", array_keys($additionalData))."=? WHERE id=?");
                    $stmt->execute(array_merge(array_values($additionalData), [$id]));
                };
        }

        $versions = $options['instance'] ?? $this->getVersionsInstance($table, $id);

        $versions->create($options['hideUser']);

        unset($GLOBALS['TL_DCA'][$table]['config']['oncreate_version_callback']['huh_versions_additional_data_'.$table.'_'.$id]);
    }

    /**
     * @param string $table
     * @param int $id
     */
    public function getVersionsInstance(string $table, int $id)
    {
        return new Versions($table, $id);
    }
}