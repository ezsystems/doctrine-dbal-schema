<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Tests\DoctrineSchema\Importer;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use EzSystems\DoctrineSchema\Importer\SchemaImporter;
use PHPUnit\Framework\TestCase;

class SchemaImporterTest extends TestCase
{
    /**
     * Create test matrix as a combination of all input files and all platform and their expected SQL outputs.
     *
     * @see testImportFromFile
     *
     * @return array [[$yamlSchemaDefinitionFile, $expectedSchema]]
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function providerForTestImportFromFile(): array
    {
        $data = [
            0 => [
                '00-simple_pk.yaml',
                new Schema(
                    [
                        new Table(
                            'my_table',
                            [
                                (new Column('id', Type::getType('integer')))->setAutoincrement(
                                    true
                                ),
                            ],
                            [
                                new Index('primary', ['id'], false, true),
                            ]
                        ),
                    ]
                ),
            ],
            1 => [
                '01-composite_pk.yaml',
                new Schema(
                    [
                        new Table(
                            'my_table',
                            [
                                (new Column('id', Type::getType('integer')))->setDefault(0),
                                (new Column('version', Type::getType('integer')))->setDefault(0),
                                new Column('name', Type::getType('string')),
                            ],
                            [
                                new Index('primary', ['id', 'version'], false, true),
                            ]
                        ),
                    ]
                ),
            ],
            2 => [
                '02-composite_pk_with_ai.yaml',
                new Schema(
                    [
                        new Table(
                            'my_table',
                            [
                                (new Column('id', Type::getType('integer')))
                                    ->setAutoincrement(true),
                                (new Column('version', Type::getType('integer')))->setDefault(0),
                                new Column('name', Type::getType('string')),
                            ],
                            [
                                new Index('primary', ['id', 'version'], false, true),
                            ]
                        ),
                    ]
                ),
            ],
            3 => [
                '03-foreign_key.yaml',
                new Schema(
                    [
                        new Table(
                            'my_main_table',
                            [
                                (new Column('id', Type::getType('integer')))
                                    ->setAutoincrement(true),
                                new Column('name', Type::getType('string')),
                            ],
                            [
                                new Index('primary', ['id'], false, true),
                            ]
                        ),
                        new Table(
                            'my_secondary_table',
                            [
                                (new Column('id', Type::getType('integer')))
                                    ->setAutoincrement(true),
                                new Column('main_id', Type::getType('integer')),
                            ],
                            [
                                new Index('primary', ['id'], false, true),
                            ],
                            [
                                new ForeignKeyConstraint(
                                    ['main_id'],
                                    'my_main_table',
                                    ['id'],
                                    'fk_my_secondary_table_id_main',
                                    ['onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE']
                                ),
                            ]
                        ),
                    ]
                ),
            ],
            4 => [
                '04-nullable_field.yaml',
                new Schema(
                    [
                        new Table(
                            'my_table',
                            [
                                (new Column('data', Type::getType('integer')))->setNotnull(false),
                            ]
                        ),
                    ]
                ),
            ],
            5 => [
                '05-varchar_length.yaml',
                new Schema(
                    [
                        new Table(
                            'my_table',
                            [
                                (new Column('name', Type::getType('string')))->setLength(64),
                            ]
                        ),
                    ]
                ),
            ],
            6 => [
                '06-index.yaml',
                new Schema(
                    [
                        new Table(
                            'my_table',
                            [
                                new Column('data1', Type::getType('integer')),
                                new Column('data2', Type::getType('integer')),
                                new Column('name', Type::getType('string')),
                            ],
                            [
                                new Index('ix_simple', ['data1'], false, false),
                                new Index('ix_composite', ['data1', 'data2'], false, false),
                                new Index('ux_name', ['name'], true, false),
                            ]
                        ),
                    ]
                ),
            ],
        ];

        return $data;
    }

    /**
     * @dataProvider providerForTestImportFromFile
     *
     * @param string $yamlSchemaDefinitionFile custom Yaml schema definition fixture file name
     * @param \Doctrine\DBAL\Schema\Schema $expectedSchema
     *
     * @throws \EzSystems\DoctrineSchema\API\Exception\InvalidConfigurationException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testImportFromFile(
        string $yamlSchemaDefinitionFile,
        Schema $expectedSchema
    ) {
        $yamlSchemaDefinitionFilePath = realpath(__DIR__ . "/_fixtures/{$yamlSchemaDefinitionFile}");
        if (false === $yamlSchemaDefinitionFilePath) {
            self::markTestIncomplete("Missing output fixture {$yamlSchemaDefinitionFilePath}");
        }

        $importer = new SchemaImporter();
        $actualSchema = $importer->importFromFile($yamlSchemaDefinitionFilePath);

        self::assertEquals(
            $expectedSchema,
            $actualSchema,
            "Yaml schema definition {$yamlSchemaDefinitionFile} produced unexpected Schema object"
        );
    }
}
