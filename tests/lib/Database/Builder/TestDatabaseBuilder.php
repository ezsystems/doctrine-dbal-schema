<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Tests\DoctrineSchema\Database\Builder;

use Doctrine\DBAL\Connection;

interface TestDatabaseBuilder
{
    /**
     * @return \Doctrine\DBAL\Connection
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \EzSystems\Tests\DoctrineSchema\Database\TestDatabaseConfigurationException
     */
    public function buildDatabase(): Connection;
}
