<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\Tests\DoctrineSchema\Builder;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use EzSystems\DoctrineSchema\API\Event\SchemaBuilderEvent;
use EzSystems\DoctrineSchema\API\Event\SchemaBuilderEvents;
use EzSystems\DoctrineSchema\API\SchemaImporter;
use EzSystems\DoctrineSchema\Builder\SchemaBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SchemaBuilderTest extends TestCase
{
    public function testBuildSchema()
    {
        $eventDispatcher = new EventDispatcher();

        $builder = new SchemaBuilder(
            $eventDispatcher,
            $this->createMock(SchemaImporter::class)
        );

        $eventDispatcher->addSubscriber(
            new class() implements EventSubscriberInterface {
                public static function getSubscribedEvents()
                {
                    return [
                        SchemaBuilderEvents::BUILD_SCHEMA => ['onBuildSchema', 200],
                    ];
                }

                public function onBuildSchema(SchemaBuilderEvent $event)
                {
                    $event
                        ->getSchema()->createTable('my_table');
                }
            }
        );

        self::assertEquals(
            new Schema([new Table('my_table')]),
            $builder->buildSchema()
        );
    }
}
