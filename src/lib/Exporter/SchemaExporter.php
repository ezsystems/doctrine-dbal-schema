<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\DoctrineSchema\Exporter;

use Doctrine\DBAL\Schema\Schema;
use EzSystems\DoctrineSchema\API\SchemaExporter as APISchemaExporter;
use EzSystems\DoctrineSchema\Exporter\Table\SchemaTableExporter;
use Symfony\Component\Yaml\Yaml;

/**
 * Export the given database Schema object to the custom Yaml format.
 *
 * @internal Type-hint API interface \EzSystems\DoctrineSchema\API\SchemaExporter
 */
class SchemaExporter implements APISchemaExporter
{
    /**
     * @var \EzSystems\DoctrineSchema\Exporter\Table\SchemaTableExporter
     */
    private $tableExporter;

    public function __construct(SchemaTableExporter $tableYamlExporter)
    {
        $this->tableExporter = $tableYamlExporter;
    }

    /**
     * Export \Doctrine\DBAL\Schema object to the custom Yaml format.
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     *
     * @return string representation of database schema in Yaml format
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function export(Schema $schema): string
    {
        $schemaDefinition = [];
        foreach ($schema->getTables() as $table) {
            $schemaDefinition['tables'] = array_merge(
                $schemaDefinition['tables'] ?? [],
                $this->tableExporter->export($table)
            );
        }

        return Yaml::dump($schemaDefinition, 4);
    }
}
