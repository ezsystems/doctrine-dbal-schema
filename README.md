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

## COPYRIGHT
Copyright (C) 1999-2021 Ibexa AS (formerly eZ Systems AS). All rights reserved.

## LICENSE
This source code is available separately under the following licenses:

A - Ibexa Business Use License Agreement (Ibexa BUL),
version 2.3 or later versions (as license terms may be updated from time to time)
Ibexa BUL is granted by having a valid Ibexa DXP (formerly eZ Platform Enterprise) subscription,
as described at: https://www.ibexa.co/product
For the full Ibexa BUL license text, please see:
https://www.ibexa.co/software-information/licenses-and-agreements (latest version applies)

AND

B - GNU General Public License, version 2
Grants an copyleft open source license with ABSOLUTELY NO WARRANTY. For the full GPL license text, please see:
https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
