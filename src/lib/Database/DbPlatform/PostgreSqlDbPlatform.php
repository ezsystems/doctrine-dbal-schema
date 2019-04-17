<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\DoctrineSchema\Database\DbPlatform;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Event\SchemaDropTableEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Schema\Table;
use InvalidArgumentException;

class PostgreSqlDbPlatform extends PostgreSQL100Platform implements DbPlatform
{
    /**
     * {@inheritdoc}
     */
    public function addEventSubscribers(EventManager $eventManager): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getDriverName(): string
    {
        return 'pdo_pgsql';
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateSchemaSQL($schemaName)
    {
        return 'CREATE SCHEMA IF NOT EXISTS ' . $schemaName;
    }

    /**
     * Returns the SQL snippet to drop an existing table.
     *
     * @param \Doctrine\DBAL\Schema\Table|string $table
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getDropTableSQL($table): string
    {
        $tableArg = $table;

        if ($table instanceof Table) {
            $table = $table->getQuotedName($this);
        }

        if (!is_string($table)) {
            throw new InvalidArgumentException('getDropTableSQL() expects $table parameter to be string or \Doctrine\DBAL\Schema\Table.');
        }

        // note: unfortunately this logic was copied from parent
        if ($this->_eventManager !== null && $this->_eventManager->hasListeners(Events::onSchemaDropTable)) {
            $eventArgs = new SchemaDropTableEventArgs($tableArg, $this);
            $this->_eventManager->dispatchEvent(Events::onSchemaDropTable, $eventArgs);

            if ($eventArgs->isDefaultPrevented()) {
                return $eventArgs->getSql();
            }
        }

        return 'DROP TABLE IF EXISTS ' . $table . ' CASCADE';
    }
}
