<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Tests\DoctrineSchema\Database\Builder;

use Doctrine\Common\EventManager;
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
        $dbPlatform = new SqliteDbPlatform();
        $eventManager = new EventManager();
        $dbPlatform->addEventSubscribers($eventManager);

        return DriverManager::getConnection(
            [
                'url' => 'sqlite:///:memory:',
                'platform' => $dbPlatform,
            ],
            new Configuration(),
            $eventManager
        );
    }
}
