<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\DoctrineSchema\API\Event;

use Doctrine\DBAL\Schema\Schema;
use EzSystems\DoctrineSchema\API\Builder\SchemaBuilder;
use Symfony\Component\EventDispatcher\Event;

class SchemaBuilderEvent extends Event
{
    /**
     * @var \EzSystems\DoctrineSchema\API\Builder\SchemaBuilder
     */
    private $schemaBuilder;

    /**
     * @var \Doctrine\DBAL\Schema\Schema
     */
    private $schema;

    /**
     * @param \EzSystems\DoctrineSchema\API\Builder\SchemaBuilder $schemaBuilder
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function __construct(SchemaBuilder $schemaBuilder, Schema $schema)
    {
        $this->schemaBuilder = $schemaBuilder;
        $this->schema = $schema;
    }

    public function getSchema(): Schema
    {
        return $this->schema;
    }

    public function getSchemaBuilder(): SchemaBuilder
    {
        return $this->schemaBuilder;
    }
}
