<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\DoctrineSchema\API\Builder;

use Doctrine\DBAL\Schema\Schema;

/**
 * Doctrine\DBAL\Schema event-driven builder.
 */
interface SchemaBuilder
{
    /**
     * Build schema by dispatching the SchemaBuilderEvent event.
     *
     * To build schema you should implement EventSubscriber subscribing to SchemaBuilderEvents::BUILD_SCHEMA.
     * The method handling this event accepts single argument of SchemaBuilderEvent type
     *
     * @see \EzSystems\DoctrineSchema\API\Event\SchemaBuilderEvent
     * @see \EzSystems\DoctrineSchema\API\Event\SchemaBuilderEvents::BUILD_SCHEMA
     *
     * @return \Doctrine\DBAL\Schema\Schema
     */
    public function buildSchema(): Schema;

    /**
     * Import Schema from Yaml schema definition file into Schema object.
     *
     * @param string $schemaFilePath
     *
     * @return \Doctrine\DBAL\Schema\Schema
     */
    public function importSchemaFromFile(string $schemaFilePath): Schema;
}
