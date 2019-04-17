<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\DoctrineSchema\Database\DbPlatform;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Event\Listeners\SQLSessionInit;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Table;

class SqliteDbPlatform extends SqlitePlatform implements DbPlatform
{
    /**
     * {@inheritdoc}
     */
    public function addEventSubscribers(EventManager $eventManager): void
    {
        $eventManager->addEventSubscriber(new SQLSessionInit('PRAGMA FOREIGN_KEYS = ON'));
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateTableSQL(Table $table, $createFlags = null)
    {
        $createFlags = $createFlags ?? self::CREATE_INDEXES | self::CREATE_FOREIGNKEYS;

        $hasCompositePK = $table->hasPrimaryKey() && count($table->getPrimaryKeyColumns()) > 1;

        // drop autoincrement if table as composite key as this is not supported
        if ($hasCompositePK) {
            foreach ($table->getColumns() as $column) {
                $column->setAutoincrement(false);
            }
        }

        return parent::getCreateTableSQL($table, $createFlags);
    }

    /**
     * {@inheritdoc}
     */
    public function getDriverName(): string
    {
        return 'pdo_sqlite';
    }

    /**
     * Override default behavior of Sqlite db platform to force generating foreign keys.
     *
     * @return bool
     */
    public function supportsForeignKeyConstraints(): bool
    {
        return true;
    }

    /**
     * Override default behavior of Sqlite db platform not to throw exception for unsupported operation of dropping FKs.
     *
     * {@inheritdoc}
     */
    public function getDropForeignKeySQL($foreignKey, $table): string
    {
        // dropping FKs is not supported by Sqlite

        return '-- ';
    }

    /**
     * Override default behavior of Sqlite db platform not to throw exception for unsupported operation of creating FKs.
     *
     * {@inheritdoc}
     */
    public function getCreateForeignKeySQL(ForeignKeyConstraint $foreignKey, $table): string
    {
        return '-- ';
    }
}
