<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\DoctrineSchema\Importer;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use EzSystems\DoctrineSchema\API\SchemaImporter as APISchemaImporter;
use Symfony\Component\Yaml\Yaml;

/**
 * Import database schema from custom Yaml Doctrine Schema format into Schema object.
 *
 * @see \Doctrine\DBAL\Schema\Schema
 */
class SchemaImporter implements APISchemaImporter
{
    /**
     * {@inheritdoc}
     */
    public function importFromFile(string $schemaFilePath, ?Schema $targetSchema = null): Schema
    {
        return $this->importFromArray(
            Yaml::parseFile($schemaFilePath),
            $targetSchema
        );
    }

    /**
     * {@inheritdoc}
     */
    public function importFromSource(string $schemaDefinition, ?Schema $targetSchema = null): Schema
    {
        return $this->importFromArray(
            Yaml::parse($schemaDefinition),
            $targetSchema
        );
    }

    /**
     * Import schema described by array loaded from Yaml custom format to the currently configured database.
     *
     * @param array $schemaDefinition
     * @param \Doctrine\DBAL\Schema\Schema|null $targetSchema
     *
     * @return \Doctrine\DBAL\Schema\Schema
     */
    private function importFromArray(array $schemaDefinition, ?Schema $targetSchema = null): Schema
    {
        if (null === $targetSchema) {
            $targetSchema = new Schema();
        }

        foreach ($schemaDefinition['tables'] as $tableName => $tableConfiguration) {
            $this->importSchemaTable($targetSchema, $tableName, $tableConfiguration);
        }

        return $targetSchema;
    }

    /**
     * Import table from the given configuration to the given schema.
     *
     * @param \Doctrine\DBAL\Schema\Schema target schema
     * @param string $tableName
     * @param array $tableConfiguration
     */
    private function importSchemaTable(
        Schema $schema,
        string $tableName,
        array $tableConfiguration
    ): void {
        $table = $schema->createTable($tableName);

        if (!empty($defaultTableOptions)) {
            foreach ($defaultTableOptions as $name => $value) {
                $table->addOption($name, $value);
            }
        }

        if (isset($tableConfiguration['id'])) {
            $this->addSchemaTableColumns($table, $tableConfiguration['id']);
            $table->setPrimaryKey(array_keys($tableConfiguration['id']));
        }

        if (isset($tableConfiguration['fields'])) {
            $this->addSchemaTableColumns($table, $tableConfiguration['fields']);
        }

        if (isset($tableConfiguration['foreignKeys'])) {
            foreach ($tableConfiguration['foreignKeys'] as $foreignKeyName => $foreignKey) {
                $table->addForeignKeyConstraint(
                    $foreignKey['foreignTable'],
                    $foreignKey['fields'],
                    $foreignKey['foreignFields'],
                    $foreignKey['options'] ?? [],
                    $foreignKeyName
                );
            }
        }

        if (isset($tableConfiguration['indexes'])) {
            foreach ($tableConfiguration['indexes'] as $indexName => $index) {
                $table->addIndex(
                    $index['fields'], $indexName, [], $index['options'] ?? []
                );
            }
        }

        if (isset($tableConfiguration['uniqueConstraints'])) {
            foreach ($tableConfiguration['uniqueConstraints'] as $indexName => $index) {
                $table->addUniqueIndex(
                    $index['fields'], $indexName, $index['options'] ?? []
                );
            }
        }
    }

    /**
     * Adds columns to the given $table.
     *
     * @param \Doctrine\DBAL\Schema\Table $table
     * @param array $columnList list of columns with their configuration
     */
    private function addSchemaTableColumns(Table $table, array $columnList): void
    {
        foreach ($columnList as $columnName => $columnConfiguration) {
            if (isset($columnConfiguration['length'])) {
                $columnConfiguration['options']['length'] = $columnConfiguration['length'];
            }

            $column = $table->addColumn(
                $columnName,
                $columnConfiguration['type'],
                $columnConfiguration['options'] ?? []
            );

            if (isset($columnConfiguration['nullable'])) {
                $column->setNotnull(!$columnConfiguration['nullable']);
            }
        }
    }
}
