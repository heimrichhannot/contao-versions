<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\VersionsBundle\Version;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\Versions;
use Doctrine\DBAL\Connection;
use HeimrichHannot\Versions\VersionUser;
use Symfony\Component\HttpFoundation\RequestStack;

class VersionControl
{
    /**
     * @var Connection
     */
    protected $connection;
    /**
     * @var RequestStack
     */
    protected $requestStack;
    /**
     * @var ScopeMatcher
     */
    protected $scopeMatcher;

    /**
     * VersionControl constructor.
     */
    public function __construct(Connection $connection, RequestStack $requestStack, ScopeMatcher $scopeMatcher)
    {
        $this->connection = $connection;
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;
    }

    /**
     * Create a new version.
     *
     * Options:
     * - hideUser: (bool) Don't add user to version. Default false
     * - additionalData: (array|null) Data that should be added to the versions entry. Provide as ['databaseFieldName' => 'value']. Default null
     * - instance: (Versions|null) Pass a custom versions instance. Default null
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
                function (string $table, int $pid, int $version, array $data) use ($connection, $additionalData) {
                    $stmt = $connection->prepare('SELECT id FROM tl_version WHERE fromTable=? AND pid=? AND version=?');
                    $id = $stmt->executeQuery([$table, $pid, $version])->fetchOne();
                    $stmt = $connection->prepare('UPDATE tl_version SET '.implode('=?, ', array_keys($additionalData)).'=? WHERE id=?');
                    $stmt->execute(array_merge(array_values($additionalData), [$id]));
                };
        }

        $versions = $options['instance'] ?? $this->getVersionsInstance($table, $id);

        if (null !== ($request = $this->requestStack->getCurrentRequest()) && $this->scopeMatcher->isFrontendRequest($request)) {
            if (!$versions->username) {
                $versions->setUsername(VersionUser::VERSION_USER_EMAIL);
            }

            if (!$versions->intUserId) {
                $versions->setUserId(0);
            }
        }

        $versions->create($options['hideUser']);

        unset($GLOBALS['TL_DCA'][$table]['config']['oncreate_version_callback']['huh_versions_additional_data_'.$table.'_'.$id]);
    }

    public function getVersionsInstance(string $table, int $id)
    {
        return new Versions($table, $id);
    }
}
