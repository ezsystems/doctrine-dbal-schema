<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Tests\DoctrineSchema\Database\DbPlatform;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\ParameterType;
use EzSystems\DoctrineSchema\Database\DbPlatform\SqliteDbPlatform;
use EzSystems\Tests\DoctrineSchema\Database\TestDatabaseFactory;
use PHPUnit\Framework\TestCase;

class SqliteDbPlatformTest extends TestCase
{
    /**
     * @var \EzSystems\Tests\DoctrineSchema\Database\TestDatabaseFactory
     */
    private $testDatabaseFactory;

    /**
     * @var \EzSystems\DoctrineSchema\Database\DbPlatform\SqliteDbPlatform
     */
    private $sqliteDbPlatform;

    public function setUp(): void
    {
        $this->sqliteDbPlatform = new SqliteDbPlatform();
        $this->testDatabaseFactory = new TestDatabaseFactory();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \EzSystems\Tests\DoctrineSchema\Database\TestDatabaseConfigurationException
     */
    public function testForeignKeys(): void
    {
        $connection = $this->testDatabaseFactory->prepareAndConnect($this->sqliteDbPlatform);
        $schema = $connection->getSchemaManager()->createSchema();

        $primaryTable = $schema->createTable('my_primary_table');
        $primaryTable->addColumn('id', 'integer');
        $primaryTable->setPrimaryKey(['id']);

        $secondaryTable = $schema->createTable('my_secondary_table');
        $secondaryTable->addColumn('id', 'integer');
        $secondaryTable->setPrimaryKey(['id']);
        $secondaryTable->addForeignKeyConstraint($primaryTable, ['id'], ['id']);

        // persist table structure
        foreach ($schema->toSql($connection->getDatabasePlatform()) as $query) {
            $connection->executeUpdate($query);
        }

        $connection->insert($primaryTable->getName(), ['id' => 1], [ParameterType::INTEGER]);
        $connection->insert($secondaryTable->getName(), ['id' => 1], [ParameterType::INTEGER]);

        // insert broken record
        $this->expectException(DBALException::class);
        $this->expectExceptionMessage('FOREIGN KEY constraint failed');
        $connection->insert($secondaryTable->getName(), ['id' => 2], [ParameterType::INTEGER]);
    }
}
