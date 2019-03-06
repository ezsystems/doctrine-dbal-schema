<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Tests\DoctrineSchema\Database\Builder;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use EzSystems\Tests\DoctrineSchema\Database\TestDatabaseConfigurationException;

class MySqlTestDatabaseBuilder implements TestDatabaseBuilder
{
    /**
     * @return \Doctrine\DBAL\Connection
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \EzSystems\Tests\DoctrineSchema\Database\TestDatabaseConfigurationException
     */
    public function buildDatabase(): Connection
    {
        if (false === ($url = getenv('MYSQL_DATABASE_URL'))) {
            throw new TestDatabaseConfigurationException('To run MySQL-specific test set MYSQL_DATABASE_URL environment variable');
        }

        $connection = DriverManager::getConnection(
            [
                'url' => $url,
            ],
            new Configuration()
        );
        // cleanup database
        $statements = $connection->getSchemaManager()->createSchema()->toDropSql(
            $connection->getDatabasePlatform()
        );
        foreach ($statements as $statement) {
            $connection->exec($statement);
        }

        return $connection;
    }
}
