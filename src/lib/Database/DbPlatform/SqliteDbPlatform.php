<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\DoctrineSchema\Database\DbPlatform;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Table;
use EzSystems\DoctrineSchema\Event\Subscriber\SqliteSessionInit;

class SqliteDbPlatform extends SqlitePlatform implements DbPlatform
{
    public function __construct(EventManager $eventManager)
    {
        parent::__construct();

        $eventManager->addEventSubscriber(new SqliteSessionInit());
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
    public function getDropForeignKeySQL($foreignKey, $table)
    {
        // dropping FKs is not supported by Sqlite

        return '-- ';
    }

    /**
     * Override default behavior of Sqlite db platform not to throw exception for unsupported operation of creating FKs.
     *
     * {@inheritdoc}
     */
    public function getCreateForeignKeySQL(ForeignKeyConstraint $foreignKey, $table)
    {
        return '-- ';
    }
}
