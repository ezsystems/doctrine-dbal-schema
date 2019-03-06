<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\DoctrineSchema\Exporter\Table;

use Doctrine\DBAL\Schema\Table;

/**
 * Exports \Doctrine\DBAL\Schema\Table to custom array representation.
 */
class SchemaTableExporter
{
    /**
     * Export \Doctrine\DBAL\Schema\Table to array representation.
     *
     * @param \Doctrine\DBAL\Schema\Table $table
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function export(Table $table): array
    {
        $tableName = $table->getName();

        $tableMetadata = [];

        $tableMetadata = $this->exportIndices($tableMetadata, $table);
        $tableMetadata = $this->exportColumns($tableMetadata, $table);
        $tableMetadata = $this->exportForeignKeys($tableMetadata, $table);

        return [$tableName => $tableMetadata];
    }

    /**
     * Remove default \Doctrine\DBAL\Schema\Index options.
     *
     * @param array $options Index options (
     *
     * @return array filtered Index options array
     */
    private function filterOutIndexDefaultOptions(array $options): array
    {
        // unset lengths if they contain only null values
        if (isset($options['lengths']) && [null] === array_unique($options['lengths'])) {
            unset($options['lengths']);
        }

        return $options;
    }

    /**
     * Export Schema Table indices.
     *
     * @param array $tableMetadata
     * @param \Doctrine\DBAL\Schema\Table $table
     *
     * @return array modified $tableMetadata
     */
    private function exportIndices(array $tableMetadata, Table $table): array
    {
        foreach ($table->getIndexes() as $index) {
            if ($index->isPrimary()) {
                // covered when processing columns
                continue;
            }

            $indexName = $index->getName();

            $indexGroup = $index->isUnique() ? 'uniqueConstraints' : 'indexes';

            $tableMetadata[$indexGroup][$indexName] = [
                'fields' => $index->getColumns(),
            ];

            $options = $this->filterOutIndexDefaultOptions($index->getOptions());
            if (!empty($options)) {
                $tableMetadata[$indexGroup][$indexName]['options'] = $options;
            }

            $flags = $index->getFlags();
            if (!empty($flags)) {
                $tableMetadata[$indexGroup][$indexName]['flags'] = $flags;
            }
        }

        return $tableMetadata;
    }

    /**
     * Export Schema Table columns.
     *
     * @param array $tableMetadata
     * @param \Doctrine\DBAL\Schema\Table $table
     *
     * @return array modified $tableMetadata
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function exportColumns(array $tableMetadata, Table $table): array
    {
        $primaryKeyColumns = $table->hasPrimaryKey() ? $table->getPrimaryKeyColumns() : [];
        foreach ($table->getColumns() as $column) {
            $fieldName = $column->getName();

            $fieldGroup = !in_array($fieldName, $primaryKeyColumns) ? 'fields' : 'id';
            $field = [
                'type' => $column->getType()->getName(),
                'nullable' => !$column->getNotnull(),
            ];

            if (null !== ($length = $column->getLength())) {
                $field['length'] = $length;
            }

            if (null !== ($default = $column->getDefault())) {
                $field['options']['default'] = $default;
            }

            if ($column->getAutoincrement()) {
                $field['options']['autoincrement'] = true;
            }

            $tableMetadata[$fieldGroup][$fieldName] = $field;
        }

        return $tableMetadata;
    }

    /**
     * Export Schema Table columns.
     *
     * @param array $tableMetadata
     * @param \Doctrine\DBAL\Schema\Table $table
     *
     * @return array modified $tableMetadata
     */
    private function exportForeignKeys(array $tableMetadata, Table $table): array
    {
        $foreignKeys = $table->getForeignKeys();
        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $foreignKeyName => $foreignKey) {
                $options = $foreignKey->getOptions();
                // unset dbms-specific defaults
                foreach (['deferrable', 'deferred'] as $optionName) {
                    if (isset($options[$optionName]) && false === $options[$optionName]) {
                        unset($options[$optionName]);
                    }
                }
                $tableMetadata['foreignKeys'][$foreignKeyName] = [
                    'fields' => $foreignKey->getColumns(),
                    'foreignTable' => $foreignKey->getForeignTableName(),
                    'foreignFields' => $foreignKey->getForeignColumns(),
                    'options' => $options,
                ];
            }
        }

        return $tableMetadata;
    }
}
