<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Tests\DoctrineSchema\Exporter;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use EzSystems\DoctrineSchema\Database\DbPlatform\SqliteDbPlatform;
use EzSystems\DoctrineSchema\Exporter\SchemaExporter;
use EzSystems\DoctrineSchema\Exporter\Table\SchemaTableExporter;
use EzSystems\Tests\DoctrineSchema\Database\TestDatabaseConfigurationException;
use EzSystems\Tests\DoctrineSchema\Database\TestDatabaseFactory;
use PHPUnit\Framework\TestCase;

class SchemaExporterTest extends TestCase
{
    /**
     * @var \EzSystems\DoctrineSchema\Exporter\SchemaExporter
     */
    private $exporter;

    /**
     * @var \EzSystems\Tests\DoctrineSchema\Database\TestDatabaseFactory
     */
    private $testDatabaseFactory;

    public function setUp()
    {
        $this->exporter = new SchemaExporter(
            new SchemaTableExporter()
        );
        $this->testDatabaseFactory = new TestDatabaseFactory();
    }

    /**
     * Load expected input/output fixtures for SchemaExporter.
     *
     * @see testExport
     *
     * @return array
     */
    public function providerForTestExport(): array
    {
        $data = [];

        $databasePlatforms = [new SqliteDbPlatform(), new MySqlPlatform()];

        // iterate over output files to avoid loading it for each platform
        $directoryIterator = new \DirectoryIterator(__DIR__ . '/_fixtures/output');
        foreach ($directoryIterator as $outputFile) {
            if (!$outputFile->isFile() || $outputFile->getExtension() !== 'yaml') {
                continue;
            }

            $outputFilePath = $outputFile->getRealPath();
            $expectedSchemaDefinition = file_get_contents($outputFilePath);

            foreach ($databasePlatforms as $databasePlatform) {
                $inputFilePath = sprintf(
                    '%s/_fixtures/input/%s/%s.sql',
                    __DIR__,
                    $databasePlatform->getName(),
                    basename($outputFile->getFilename(), '.yaml')
                );
                $inputSchemaSQL = file_exists($inputFilePath)
                    ? file_get_contents($inputFilePath)
                    : null;

                $data[] = [
                    $databasePlatform,
                    $inputSchemaSQL,
                    $expectedSchemaDefinition,
                    $inputFilePath,
                    $outputFilePath,
                ];
            }
        }

        return $data;
    }

    /**
     * @dataProvider providerForTestExport
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $databasePlatform
     * @param string $inputSchemaSQL
     * @param string $expectedSchemaDefinition
     * @param string $inputFilePath
     * @param string $outputFilePath
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testExport(
        AbstractPlatform $databasePlatform,
        ?string $inputSchemaSQL,
        string $expectedSchemaDefinition,
        string $inputFilePath,
        string $outputFilePath
    ): void {
        if (null === $inputSchemaSQL) {
            self::markTestIncomplete(
                "Missing input SQL for {$databasePlatform->getName()} output available in {$outputFilePath}"
            );
        }

        try {
            $connection = $this->getDatabaseConnection($databasePlatform);
            $connection->exec($inputSchemaSQL);
            $inputSchema = $connection->getSchemaManager()->createSchema();
            $rootDir = dirname(__DIR__, 3);

            self::assertEquals(
                $expectedSchemaDefinition,
                $this->exporter->export($inputSchema),
                sprintf(
                    "%s database export: SQL file\n\t%s\ndid not create expected yaml defined in\n\t%s",
                    $databasePlatform->getName(),
                    // left-trim name to make it more readable for debugging purposes
                    str_replace($rootDir, '.', $inputFilePath),
                    str_replace($rootDir, '.', $outputFilePath)
                )
            );

            // manually rollback changes, as some DBMS don't allow to rollback DDL
            foreach ($inputSchema->toDropSql($databasePlatform) as $dropSql) {
                $connection->exec($dropSql);
            }
        } catch (TestDatabaseConfigurationException $e) {
            self::markTestSkipped($e->getMessage());
        }
    }

    /**
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $databasePlatform
     *
     * @return \Doctrine\DBAL\Connection
     *
     * @throws \EzSystems\Tests\DoctrineSchema\Database\TestDatabaseConfigurationException
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getDatabaseConnection(AbstractPlatform $databasePlatform): Connection
    {
        return $this->testDatabaseFactory->prepareAndConnect($databasePlatform);
    }
}
