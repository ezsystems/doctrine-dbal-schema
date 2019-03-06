<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\DoctrineSchema\Event\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;

/**
 * SQLite Session Init Event Subscriber which enables foreign key support.
 */
class SqliteSessionInit implements EventSubscriber
{
    /**
     * @param \Doctrine\DBAL\Event\ConnectionEventArgs $args
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function postConnect(ConnectionEventArgs $args)
    {
        $args->getConnection()->executeUpdate('PRAGMA FOREIGN_KEYS = ON');
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [Events::postConnect];
    }
}
