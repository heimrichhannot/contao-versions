<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\VersionsBundle\DataContainer;


use Contao\Automator;
use Contao\Config;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class VersionsContainer
{
    /** @var array */
    protected $bundleConfig = [];
    /**
     * @var Connection
     */
    protected $connection;

    /** @var LoggerInterface */
    private $logger;

    /** @var array|null */
    protected $persistentTables = null;

    /**
     * VersionsContainer constructor.
     * @param array $bundleConfig
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger, array $bundleConfig, Connection $connection)
    {
        $this->bundleConfig = $bundleConfig;
        $this->logger       = $logger;
        $this->connection = $connection;
    }

    /**
     * Clean the version table respecting persistent table configuration
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Exception
     */
    public function cleanTable(): void
    {
        if ($this->hasPersistentTables()) {
            // Fallback for legacy integrations, will be default 0 in version 3.
            $period = $this->bundleConfig['persistent_version_period'] ?? (int)Config::get('versionPeriod');
            $tstamp = $period > 0 ?  time() - (int)Config::get('versionPeriod') : 0;
            $this->connection->executeQuery("DELETE FROM tl_version WHERE tstamp<$tstamp AND fromTable NOT IN('" . implode('\',\'', $this->getPersistentTables()) . "')");
        } else {
            // Truncate the table
            // Delete old versions from the database
            $tstamp = time() - (int)Config::get('versionPeriod');
            $this->connection->executeQuery("DELETE FROM tl_version WHERE tstamp<$tstamp");
        }
    }

    /**
     * Purge the version table respecting persistent table configuration
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Exception
     */
    public function purgeTable(): void
    {
        if ($this->hasPersistentTables()) {
            $this->connection->executeQuery("DELETE FROM tl_version WHERE fromTable NOT IN('" . implode('\',\'', $this->getPersistentTables()) . "')");

            $this->log('Cleared non persistent entries from version table', __METHOD__, TL_CRON);
        } else {
            /** @var Automator $automator */
            $automator = System::importStatic(Automator::class);
            $automator->purgeVersionTable();
        }
    }

    public function hasPersistentTables(): bool
    {
        return !empty($this->getPersistentTables());
    }

    public function getPersistentTables(): array
    {
        if (null === $this->persistentTables) {
            $tables = $this->bundleConfig['persistent_tables'] ?? [];
            if (isset($GLOBALS['PERSISTENT_VERSION_TABLES']) && is_array($GLOBALS['PERSISTENT_VERSION_TABLES']) && !empty($GLOBALS['PERSISTENT_VERSION_TABLES'])) {
                $tables = array_merge($tables, $GLOBALS['PERSISTENT_VERSION_TABLES']);
            }
            $this->persistentTables = array_unique($tables);
        }
        return $this->persistentTables;
    }

    /**
     * Add a log entry to the database
     *
     * @param string $text     The log message
     * @param string $function The function name
     * @param string $category The category name
     */
    public function log(string $text, string $function, ?string $category = null)
    {
        $level = 'ERROR' === $category ? LogLevel::ERROR : LogLevel::INFO;
        $this->logger->log($level, $text, array('contao' => new ContaoContext($function, $category)));
    }
}