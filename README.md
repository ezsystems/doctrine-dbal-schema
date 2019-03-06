# Doctrine Schema Bundle

This Symfony Bundle provides basic abstraction layer for cross-DBMS schema import.

It introduces custom Yaml format for schema definition and provides autowired APIs.

## Schema Builder

Provided by APIs defined on the `\EzSystems\DoctrineSchema\API\SchemaImporter` interface,
imports given Yaml source string or Yaml file into `\Doctrine\DBAL\Schema` object.

## Schema Exporter

Provided by APIs defined on the `\EzSystems\DoctrineSchema\API\SchemaExporter` interface,
exports given `\Doctrine\DBAL\Schema` object to the custom Yaml format.

## SchemaBuilder

Provided by APIs defined on the `\EzSystems\DoctrineSchema\API\Builder\SchemaBuilder` interface,
is an extensibility point to be used by Symfony-based projects.

The `SchemaBuilder` is event-driven. To hook into the process of building schema, a custom `EventSubscriber` is required, e.g.

```php
use EzSystems\DoctrineSchema\API\Event\SchemaBuilderEvent;
use EzSystems\DoctrineSchema\API\Event\SchemaBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BuildSchemaSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $schemaFilePath;

    public function __construct(string $schemaFilePath)
    {
        $this->schemaFilePath = $schemaFilePath;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            SchemaBuilderEvents::BUILD_SCHEMA => ['onBuildSchema', 200],
        ];
    }

    /**
     * @param \EzSystems\DoctrineSchema\API\Builder\SchemaBuilderEvent $event
     */
    public function onBuildSchema(SchemaBuilderEvent $event)
    {
        $event
            ->getSchemaBuilder()
            ->importSchemaFromFile($this->schemaFilePath);
    }
}
```

Schema provided in this way can be imported into Schema object by e.g.:

```php
    public function __construct(SchemaBuilder $schemaBuilder)
    {
        $this->schemaBuilder = $schemaBuilder;
    }

    public function importSchema()
    {
        $schema = $this->schemaBuilder->buildSchema();
        // ...
    }
```

## Copyright & License

Copyright (c) eZ Systems AS. For copyright and license details see provided LICENSE file.
