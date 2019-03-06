<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Tests\DoctrineSchema\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class TestDatabaseFactory
{
    /**
     * @var \EzSystems\Tests\DoctrineSchema\Database\Builder\TestDatabaseBuilder[]
     */
    private $databaseBuildersForPlatforms = [];

    public function __construct()
    {
        $this->databaseBuildersForPlatforms = [
            'sqlite' => new Builder\SqliteTestDatabaseBuilder(),
            'mysql' => new Builder\MySqlTestDatabaseBuilder(),
        ];
    }

    /**
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $databasePlatform
     *
     * @return \Doctrine\DBAL\Connection
     *
     * @throws \EzSystems\Tests\DoctrineSchema\Database\TestDatabaseConfigurationException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function prepareAndConnect(AbstractPlatform $databasePlatform): Connection
    {
        $name = $databasePlatform->getName();
        if (!isset($this->databaseBuildersForPlatforms[$name])) {
            throw new TestDatabaseConfigurationException("Unsupported DBMS '{$name}'");
        }

        return $this->databaseBuildersForPlatforms[$name]->buildDatabase();
    }
}
