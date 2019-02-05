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
use EzSystems\DoctrineSchema\Database\DbPlatform\SqliteDbPlatform;

class SqliteTestDatabaseBuilder implements TestDatabaseBuilder
{
    /**
     * @return \Doctrine\DBAL\Connection
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function buildDatabase(): Connection
    {
        return DriverManager::getConnection(
            [
                'url' => 'sqlite:///:memory:',
                'platform' => new SqliteDbPlatform(),
            ],
            new Configuration()
        );
    }
}
