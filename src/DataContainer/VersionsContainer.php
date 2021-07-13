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


use Contao\Config;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class VersionsContainer
{
    /** @var array */
    protected $bundleConfig = [];

    /** @var LoggerInterface */
    private $logger;

    /**
     * VersionsContainer constructor.
     * @param array $bundleConfig
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
//        $this->bundleConfig = $bundleConfig;
        $this->logger       = $logger;
    }

    public function cleanTable(): void
    {
        $objDatabase = Database::getInstance();

        if ($this->hasPersistentTables()) {
            $tstamp = time() - (int)Config::get('versionPeriod');
            $objDatabase->query("DELETE FROM tl_version WHERE tstamp<$tstamp AND fromTable NOT IN('" . implode('\',\'', $GLOBALS['PERSISTENT_VERSION_TABLES']) . "')");
        } else {
            // Truncate the table
            // Delete old versions from the database
            $tstamp = time() - (int)Config::get('versionPeriod');
            $objDatabase->query("DELETE FROM tl_version WHERE tstamp<$tstamp");
        }

    }

    public function purgeTable(): void
    {
        $objDatabase = Database::getInstance();

        if ($this->hasPersistentTables()) {
            $objDatabase->execute("DELETE FROM tl_version WHERE fromTable NOT IN('" . implode('\',\'', $GLOBALS['PERSISTENT_VERSION_TABLES']) . "')");

            $this->log('Cleared non persistent entries from version table', __METHOD__, TL_CRON);
        } else {
            $objDatabase->execute("TRUNCATE TABLE tl_version");

            $this->log('Purged the version table', __METHOD__, TL_CRON);
        }
    }

    public function hasPersistentTables(): bool
    {
        return !empty($this->getPersistentTables());
    }

    public function getPersistentTables(): array
    {
        $tables = $this->bundleConfig['persistent_tables'] ?? [];
        if (isset($GLOBALS['PERSISTENT_VERSION_TABLES']) && is_array($GLOBALS['PERSISTENT_VERSION_TABLES']) && !empty($GLOBALS['PERSISTENT_VERSION_TABLES'])) {
            $tables = array_merge($tables, $GLOBALS['PERSISTENT_VERSION_TABLES']);
        }
        return array_unique($tables);
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