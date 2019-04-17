<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\DoctrineSchema\Database\DbPlatform;

use Doctrine\Common\EventManager;

interface DbPlatform
{
    /**
     * Get name of the driver associated with Database Platform implementation.
     *
     * Every Database Platform implementation should extend Doctrine AbstractPlatform
     * (or its implementation).
     *
     * @see \Doctrine\DBAL\Platforms\AbstractPlatform
     *
     * @return string
     */
    public function getDriverName(): string;

    /**
     * Add event subscribers predefined and required by an implementation.
     *
     * @param \Doctrine\Common\EventManager $eventManager
     */
    public function addEventSubscribers(EventManager $eventManager): void;
}
